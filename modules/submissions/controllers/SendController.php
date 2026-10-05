<?php
/**
 * The send-a-photograph form and its review queue.
 *
 * SUBMIT (anyone, POST from /send)
 *   Three free checks keep out bots without a puzzle for the reader: a field hidden from people that a bot fills in
 *   (a filled one is thanked and dropped), a signed load time that must be at least MIN_SECONDS old and no more than a
 *   day, and at most RATE submissions a day from one address. Then each file is read with Imagick, never trusted by
 *   its name: it must be a real JPEG, PNG, TIFF or HEIC image, at most MAX_BYTES, at least MIN_LONG pixels on its
 *   longer side. The stored copy has its camera and location metadata stripped (the colour profile is kept) and is
 *   turned upright; the SHA-256 of the bytes as sent is recorded. HEIC and TIFF are stored as JPEG and PNG, which a
 *   browser can show on the queue page. The entry is saved disabled in a section with no URLs; the files go to
 *   storage/submissions/<uid>/, outside the web root.
 *
 * FILE, DECIDE (admins only)
 *   file streams one stored file to the queue page. decide accepts a file as the record's portrait or among its
 *   images (it becomes archive media with its provenance, the way the import scripts make it; a portrait it replaces
 *   moves to the record's images, never deleted), declines with a one-line reason, or marks spam (the files are
 *   deleted and the address cleared). Nothing is attached without that click.
 */

namespace modules\submissions\controllers;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use craft\web\Controller;
use craft\web\UploadedFile;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SendController extends Controller
{
    public const MIN_LONG = 1200;
    public const MAX_BYTES = 25 * 1024 * 1024;
    public const MAX_FILES = 5;
    public const MIN_SECONDS = 5;
    public const RATE = 5;
    public const SECTIONS = ['warMemorials', 'persons'];
    private const FORMATS = ['JPEG' => 'jpg', 'PNG' => 'png', 'TIFF' => 'png', 'HEIC' => 'jpg', 'HEIF' => 'jpg'];

    protected array|int|bool $allowAnonymous = ['submit'];

    public function beforeAction($action): bool
    {
        if ($action->id !== 'submit') {
            $user = Craft::$app->getUser()->getIdentity();
            if (!$user || !$user->admin) {
                throw new ForbiddenHttpException('The submissions queue needs an admin session.');
            }
        }
        return parent::beforeAction($action);
    }

    public static function dir(Entry $e): string
    {
        return Craft::getAlias('@storage') . '/submissions/' . $e->uid;
    }

    public function actionSubmit(): ?Response
    {
        $this->requirePostRequest();
        $req = Craft::$app->getRequest();
        $id = (int)$req->getBodyParam('for');
        /* Built only when returned: redirect() sets the shared response to a 302, which would turn a refused post's
           error page into a redirect to the thank-you page. */
        $kind = $req->getBodyParam('kind') === 'correction' ? 'correction' : 'photograph';
        $done = fn() => $this->redirect('/send?sent=1&kind=' . $kind . ($id ? '&for=' . $id : ''));

        if (trim((string)$req->getBodyParam('website')) !== '') {
            return $done();
        }
        $v = [];
        foreach (['for', 'who', 'when', 'takenBy', 'holder', 'name', 'credit', 'email', 'correction', 'page'] as $k) {
            $v[$k] = trim((string)$req->getBodyParam($k));
        }
        $v['holds'] = (bool)$req->getBodyParam('holds');
        $v['publish'] = (bool)$req->getBodyParam('publish');
        $v['kind'] = $kind;
        $errors = [];

        $t = Craft::$app->getSecurity()->validateData((string)$req->getBodyParam('t'));
        $age = $t === false ? null : time() - (int)$t;
        if ($age === null || $age > 86400) {
            $errors[] = 'The form had expired. Your details are still here: choose the pictures again and send.';
        } elseif ($age < self::MIN_SECONDS) {
            $errors[] = 'That was sent very quickly. Please check the details and send again.';
        }
        $rateKey = 'submissions-rate-' . sha1($req->getUserIP() . '|' . date('Y-m-d'));
        $count = (int)Craft::$app->getCache()->get($rateKey);
        if ($count >= self::RATE) {
            $errors[] = 'This address has sent ' . self::RATE . ' submissions today, the most the form takes in a day. Please try again tomorrow.';
        }
        /* A photograph is for a person or memorial record; a correction may be about any record with a page, or about a page
           named in the form. */
        $rec = $id ? ($kind === 'photograph' ? Entry::find()->id($id)->section(self::SECTIONS)->one() : Entry::find()->id($id)->uri(':notempty:')->one()) : null;
        if ($kind === 'photograph' && !$rec) {
            $errors[] = 'The record this picture is for could not be found. Please use the link on the record page.';
        }
        if ($kind === 'photograph' && $v['who'] === '') { $errors[] = 'Please say who is in the picture.'; }
        if ($kind === 'correction' && $v['correction'] === '') { $errors[] = 'Please say what is wrong, and how you know.'; }
        if ($kind === 'correction' && !$rec && $v['page'] === '') { $errors[] = 'Please say which page the correction is about.'; }
        if ($v['name'] === '') { $errors[] = 'Please give your name.'; }
        if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please give an email address the archivist can reply to.'; }
        $files = UploadedFile::getInstancesByName('photos');
        if (($kind === 'photograph' || $files) && (!$v['holds'] || !$v['publish'])) { $errors[] = 'Both permission boxes need to be ticked before the archive can take a picture.'; }
        foreach (['who' => 2000, 'when' => 200, 'takenBy' => 200, 'holder' => 200, 'name' => 200, 'credit' => 200, 'email' => 254, 'correction' => 5000, 'page' => 500] as $k => $max) {
            if (mb_strlen($v[$k]) > $max) { $errors[] = 'One of the answers is longer than the form takes.'; break; }
        }

        $read = [];
        if (!$files && $kind === 'photograph') {
            $errors[] = 'Please choose at least one picture.';
        } elseif (count($files) > self::MAX_FILES) {
            $errors[] = 'Please send at most ' . self::MAX_FILES . ' pictures at a time.';
        } elseif ($files) {
            foreach ($files as $i => $f) {
                $label = 'The picture "' . $f->name . '"';
                if ($f->hasError || !is_uploaded_file($f->tempName)) { $errors[] = "$label did not arrive. It may be larger than the server takes."; continue; }
                if ($f->size > self::MAX_BYTES) { $errors[] = "$label is larger than 25 MB."; continue; }
                try {
                    $im = new \Imagick();
                    $im->readImage($f->tempName . '[0]');
                    $fmt = strtoupper($im->getImageFormat());
                } catch (\Throwable $e) {
                    $heic = in_array(strtolower(pathinfo($f->name, PATHINFO_EXTENSION)), ['heic', 'heif'], true) && !\Imagick::queryFormats('HEI*');
                    $errors[] = $heic ? "$label is a HEIC picture, which this server cannot read. Please send it as a JPEG: on an iPhone, choose Most Compatible under Settings, Camera, Formats, or share it by email first."
                        : "$label could not be read as a picture. JPEG, PNG, TIFF or HEIC, please.";
                    continue;
                }
                if (!isset(self::FORMATS[$fmt])) { $errors[] = "$label is a $fmt file. JPEG, PNG, TIFF or HEIC, please."; continue; }
                $w = $im->getImageWidth(); $h = $im->getImageHeight();
                if (max($w, $h) < self::MIN_LONG) {
                    $errors[] = "$label is $w by $h pixels. The archive needs at least " . self::MIN_LONG . ' on the longer side; a photograph of the print taken flat in daylight usually is.';
                    continue;
                }
                $read[] = [$f, $im, $fmt, $w, $h];
            }
        }

        if ($errors) {
            Craft::$app->getUrlManager()->setRouteParams(['errors' => $errors, 'values' => $v]);
            return null;
        }

        $section = Craft::$app->getEntries()->getSectionByHandle('submissions');
        $e = new Entry();
        $e->sectionId = $section->id;
        $e->typeId = $section->getEntryTypes()[0]->id;
        $e->enabled = false;
        $e->title = ($kind === 'correction' ? 'Correction for ' : 'Photograph for ') . ($rec ? $rec->title : ($v['page'] ?: 'a page')) . ', from ' . $v['name'];
        $e->setFieldValues([
            'submissionStatus' => 'new', 'submissionKind' => $kind, 'submissionRecord' => $rec ? [$rec->id] : [],
            'submissionCorrection' => $v['correction'], 'submissionPage' => $v['page'],
            'submissionWho' => $v['who'], 'submissionWhen' => $v['when'], 'submissionTakenBy' => $v['takenBy'],
            'submissionHolder' => $v['holder'], 'submissionSenderName' => $v['name'], 'submissionCredit' => $v['credit'],
            'submissionEmail' => $v['email'],
            'submissionPermission' => (!$v['holds'] && !$v['publish']) ? '' : 'Ticked on ' . date('Y-m-d H:i T') . ": \"I hold this photograph, or the person who does has agreed to my sending it\" and \"The archive may publish it with the credit I have given.\"",
        ]);
        if (!Craft::$app->getElements()->saveElement($e)) {
            Craft::error('Submission not saved: ' . json_encode($e->getErrors()), __METHOD__);
            Craft::$app->getUrlManager()->setRouteParams(['errors' => ['The archive could not save this just now. Please try again later.'], 'values' => $v]);
            return null;
        }

        $dir = self::dir($e);
        FileHelper::createDirectory($dir);
        $stored = [];
        foreach ($read as $i => [$f, $im, $fmt, $w, $h]) {
            $ext = self::FORMATS[$fmt];
            $name = ($i + 1) . '.' . $ext;
            $sum = hash_file('sha256', $f->tempName);
            $icc = $im->getImageProfiles('icc', true);
            self::upright($im);
            $im->stripImage();
            if (!empty($icc['icc'])) { $im->profileImage('icc', $icc['icc']); }
            $im->setImageFormat($ext === 'jpg' ? 'JPEG' : 'PNG');
            if ($ext === 'jpg') { $im->setImageCompressionQuality(95); }
            $im->writeImage("$dir/$name");
            $stored[] = ['file' => $name, 'sent' => $f->name, 'format' => $fmt, 'sha256' => $sum, 'bytes' => $f->size,
                'width' => $im->getImageWidth(), 'height' => $im->getImageHeight()];
            $im->clear();
        }
        $e->setFieldValue('submissionFiles', json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Craft::$app->getElements()->saveElement($e);
        Craft::$app->getCache()->set($rateKey, $count + 1, 86400);
        return $done();
    }

    /** Turn the pixels to match the EXIF orientation, since stripping the metadata removes the tag that said so. */
    private static function upright(\Imagick $im): void
    {
        switch ($im->getImageOrientation()) {
            case \Imagick::ORIENTATION_BOTTOMRIGHT: $im->rotateImage('#000', 180); break;
            case \Imagick::ORIENTATION_RIGHTTOP: $im->rotateImage('#000', 90); break;
            case \Imagick::ORIENTATION_LEFTBOTTOM: $im->rotateImage('#000', -90); break;
        }
        $im->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
    }

    private static function load(int $id): Entry
    {
        $e = Entry::find()->section('submissions')->id($id)->status(null)->one();
        if (!$e) { throw new NotFoundHttpException('No such submission.'); }
        return $e;
    }

    private static function files(Entry $e): array
    {
        return json_decode((string)$e->submissionFiles, true) ?: [];
    }

    public function actionFile(): Response
    {
        $req = Craft::$app->getRequest();
        $e = self::load((int)$req->getRequiredQueryParam('id'));
        $n = (int)$req->getRequiredQueryParam('n');
        $f = self::files($e)[$n] ?? null;
        $path = $f ? self::dir($e) . '/' . basename($f['file']) : '';
        if (!$f || !is_file($path)) { throw new NotFoundHttpException('No such file.'); }
        return Craft::$app->getResponse()->sendFile($path, $f['file'], ['inline' => true]);
    }

    public function actionDecide(): Response
    {
        $this->requirePostRequest();
        $req = Craft::$app->getRequest();
        $e = self::load((int)$req->getRequiredBodyParam('id'));
        $do = (string)$req->getRequiredBodyParam('do');
        $session = Craft::$app->getSession();
        $el = Craft::$app->getElements();
        $note = trim((string)$e->submissionDecision);
        $stamp = date('Y-m-d');
        $back = $this->redirect('/admin-submissions');

        if ($do === 'portrait' || $do === 'image') {
            $n = (int)$req->getRequiredBodyParam('n');
            $f = self::files($e)[$n] ?? null;
            $rec = $e->submissionRecord->status(null)->one();
            $path = $f ? self::dir($e) . '/' . basename($f['file']) : '';
            if (!$f || !$rec || !is_file($path)) { $session->setError('The file or its record is missing.'); return $back; }
            if (!empty($f['asset'])) { $session->setError('That file is already in the archive as asset #' . $f['asset'] . '.'); return $back; }

            $volume = Craft::$app->getVolumes()->getVolumeByHandle('archiveMedia');
            $assets = Craft::$app->getAssets();
            $folder = $assets->findFolder(['volumeId' => $volume->id, 'path' => 'submitted/']) ?? $assets->ensureFolderByFullPathAndVolume('submitted', $volume);
            $ext = pathinfo($f['file'], PATHINFO_EXTENSION);
            $fn = StringHelper::toKebabCase($rec->title) . '-sent-' . $stamp . '-' . ($n + 1) . '.' . $ext;
            $tmp = Craft::$app->getPath()->getTempPath() . '/' . $fn;
            copy($path, $tmp);
            $a = new Asset();
            $a->tempFilePath = $tmp; $a->setFilename($fn); $a->newFolderId = $folder->id; $a->setVolumeId($volume->id);
            $a->setScenario(Asset::SCENARIO_CREATE); $a->avoidFilenameConflicts = true;
            $a->title = $rec->title; $a->alt = $rec->title;
            if (!$el->saveElement($a)) { $session->setError('The archive could not save the file: ' . implode('; ', $a->getFirstErrors())); return $back; }
            $a = Asset::find()->id($a->id)->one();
            $sent = $e->dateCreated->format('j F Y');
            $src = 'Sent to the archive by ' . $e->submissionSenderName . ' on ' . $sent . '.'
                . ($e->submissionHolder ? ' The original is held by ' . $e->submissionHolder . '.' : '')
                . ($e->submissionTakenBy ? ' Taken by ' . $e->submissionTakenBy . ', as the sender gives it.' : '')
                . ($e->submissionWhen ? ' Taken ' . $e->submissionWhen . ', as the sender gives it.' : '');
            $vals = ['provenanceKind' => 'donated', 'license' => 'permission', 'acquiredDate' => $e->dateCreated->format('Y-m-d'),
                'source' => $src, 'photoCaptionExt' => (string)$e->submissionWho, 'photoCredit' => (string)$e->submissionCredit,
                'rightsNote' => 'Sent with the sender\'s statement that they hold the photograph, or that the person who does agreed to its sending, and that the archive may publish it with the credit given (' . $sent . ').',
                'sourceChecksum' => 'sha256:' . $f['sha256']];
            $have = array_map(fn($x) => $x->handle, $a->getFieldLayout()->getCustomFields());
            $a->setFieldValues(array_intersect_key($vals, array_flip($have)));
            $el->saveElement($a);

            $imgs = $rec->recordImages->status(null)->ids();
            if ($do === 'portrait') {
                $old = $rec->featuredImage->one();
                if ($old && !in_array($old->id, $imgs)) { $imgs[] = $old->id; }
                $rec->setFieldValue('featuredImage', [$a->id]);
            } else {
                $imgs[] = $a->id;
            }
            $rec->setFieldValue('recordImages', array_values(array_unique($imgs)));
            if (!$el->saveElement($rec)) { $session->setError('The file is asset #' . $a->id . ' but the record did not save: ' . implode('; ', $rec->getFirstErrors())); return $back; }

            $all = self::files($e); $all[$n]['asset'] = $a->id;
            $e->setFieldValue('submissionFiles', json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            $e->setFieldValue('submissionStatus', 'accepted');
            $e->setFieldValue('submissionDecision', trim($note . "\n$stamp: file " . ($n + 1) . ' accepted as ' . ($do === 'portrait' ? 'the portrait' : 'a related image') . " (asset #{$a->id})."));
            $el->saveElement($e);
            $session->setNotice('Accepted: asset #' . $a->id . ' on ' . $rec->title . '.');
            return $back;
        }
        if ($do === 'done') {
            $what = trim((string)$req->getBodyParam('reason'));
            $e->setFieldValue('submissionStatus', 'accepted');
            $e->setFieldValue('submissionDecision', trim($note . "\n$stamp: correction dealt with." . ($what !== '' ? " $what" : '')));
            $el->saveElement($e);
            $session->setNotice('Marked done.');
            return $back;
        }
        if ($do === 'decline') {
            $why = trim((string)$req->getBodyParam('reason'));
            if ($why === '') { $session->setError('A decline needs a one-line reason.'); return $back; }
            $e->setFieldValue('submissionStatus', 'declined');
            $e->setFieldValue('submissionDecision', trim($note . "\n$stamp: declined. $why"));
            $el->saveElement($e);
            $session->setNotice('Declined.');
            return $back;
        }
        if ($do === 'spam') {
            FileHelper::removeDirectory(self::dir($e));
            $e->setFieldValues(['submissionStatus' => 'spam', 'submissionEmail' => '', 'submissionFiles' => '',
                'submissionDecision' => trim($note . "\n$stamp: marked spam; files deleted, address cleared.")]);
            $el->saveElement($e);
            $session->setNotice('Marked spam.');
            return $back;
        }
        throw new NotFoundHttpException('Unknown decision.');
    }
}
