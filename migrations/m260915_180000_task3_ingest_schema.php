<?php

namespace craft\contentmigrations;

use Craft;
use craft\db\Migration;
use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
use craft\fieldlayoutelements\entries\EntryTitleField;
use craft\fields\Assets;
use craft\fields\Entries;
use craft\fields\PlainText;
use craft\helpers\StringHelper;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use RuntimeException;

class m260915_180000_task3_ingest_schema extends Migration
{
    private const VOLUME_HANDLE = 'archiveMedia';
    private const INGEST_TAB = 'Ingest';
    private const MIGRATED_TYPES = [
        'person',
        'organization',
        'place',
        'event',
        'group',
        'article',
        'obituary',
        'collection',
    ];

    public function safeUp(): bool
    {
        $fields = Craft::$app->getFields();
        $entries = Craft::$app->getEntries();
        $volume = Craft::$app->getVolumes()->getVolumeByHandle(self::VOLUME_HANDLE);
        if ($volume === null) {
            throw new RuntimeException('Volume archiveMedia is missing.');
        }
        $volumeUid = $volume->uid;
        $site = Craft::$app->getSites()->getPrimarySite();

        $this->ensurePlainText('sourcePath', 'Source Path', false);
        $this->ensurePlainText('legacyHtml', 'Legacy HTML', true, 12, true);
        $this->ensurePlainText('legacyCategory', 'Legacy Category', false);
        $this->ensurePlainText('creditRaw', 'Credit Raw', true, 4, false);
        $this->ensurePlainText('creditDpi', 'Credit DPI', false);
        $this->ensurePlainText('creditProcess', 'Credit Process', false);
        $this->ensurePlainText('creditKind', 'Credit Kind', false);
        $this->ensurePlainText('creditName', 'Credit Name', false);

        $this->ensureAssetsField('archivalFiles', 'Archival Files', $volumeUid);
        $this->ensureAssetsField('documentFiles', 'Document Files', $volumeUid);

        $photoType = $this->ensureEntryType('photograph', 'Photograph', true);
        $this->ensureSection(
            'photographs',
            'Photographs',
            'photographs/{slug}',
            'photographs/_entry',
            $photoType,
            $site->id
        );
        $photoSection = $entries->getSectionByHandle('photographs');
        if ($photoSection === null) {
            throw new RuntimeException('Photographs section missing after save.');
        }

        $this->ensureEntriesField('relatedPhotographs', 'Related Photographs', $photoSection->uid);

        $this->applyLayout($photoType, $this->photographTabs(), true);

        $docType = $this->ensureEntryType('document', 'Document', true);
        $this->ensureSection(
            'documents',
            'Documents',
            'documents/{slug}',
            'documents/_entry',
            $docType,
            $site->id
        );
        $this->applyLayout($docType, $this->documentTabs(), true);

        foreach (self::MIGRATED_TYPES as $handle) {
            $type = $entries->getEntryTypeByHandle($handle);
            if ($type === null) {
                throw new RuntimeException("Entry type {$handle} is missing.");
            }
            $this->addIngestTab($type, $handle);
        }

        return true;
    }

    public function safeDown(): bool
    {
        return false;
    }

    private function ensurePlainText(
        string $handle,
        string $name,
        bool $multiline,
        int $rows = 4,
        bool $code = false
    ): PlainText {
        $fields = Craft::$app->getFields();
        $existing = $fields->getFieldByHandle($handle);
        if ($existing instanceof PlainText) {
            return $existing;
        }
        if ($existing !== null) {
            throw new RuntimeException("Field {$handle} exists as a different type.");
        }
        $field = new PlainText();
        $field->name = $name;
        $field->handle = $handle;
        $field->multiline = $multiline;
        $field->initialRows = $rows;
        $field->code = $code;
        $field->searchable = false;
        $field->uid = StringHelper::UUID();
        if (!$fields->saveField($field)) {
            throw new RuntimeException("Could not save {$handle}: " . json_encode($field->getErrors()));
        }
        return $field;
    }

    private function ensureAssetsField(string $handle, string $name, string $volumeUid): Assets
    {
        $fields = Craft::$app->getFields();
        $existing = $fields->getFieldByHandle($handle);
        if ($existing instanceof Assets) {
            return $existing;
        }
        if ($existing !== null) {
            throw new RuntimeException("Field {$handle} exists as a different type.");
        }
        $field = new Assets();
        $field->name = $name;
        $field->handle = $handle;
        $field->restrictFiles = false;
        $field->maxRelations = null;
        $field->sources = ['volume:' . $volumeUid];
        $field->defaultUploadLocationSource = 'volume:' . $volumeUid;
        $field->viewMode = Assets::VIEW_MODE_LIST;
        $field->previewMode = Assets::PREVIEW_MODE_FULL;
        $field->searchable = false;
        $field->uid = StringHelper::UUID();
        if (!$fields->saveField($field)) {
            throw new RuntimeException("Could not save {$handle}: " . json_encode($field->getErrors()));
        }
        return $field;
    }

    private function ensureEntriesField(string $handle, string $name, string $sectionUid): Entries
    {
        $fields = Craft::$app->getFields();
        $existing = $fields->getFieldByHandle($handle);
        if ($existing instanceof Entries) {
            return $existing;
        }
        if ($existing !== null) {
            throw new RuntimeException("Field {$handle} exists as a different type.");
        }
        $field = new Entries();
        $field->name = $name;
        $field->handle = $handle;
        $field->sources = ['section:' . $sectionUid];
        $field->maxRelations = null;
        $field->allowSelfRelations = false;
        $field->viewMode = Entries::VIEW_MODE_LIST;
        $field->searchable = false;
        $field->uid = StringHelper::UUID();
        if (!$fields->saveField($field)) {
            throw new RuntimeException("Could not save {$handle}: " . json_encode($field->getErrors()));
        }
        return $field;
    }

    private function ensureEntryType(string $handle, string $name, bool $hasTitle): EntryType
    {
        $entries = Craft::$app->getEntries();
        $existing = $entries->getEntryTypeByHandle($handle);
        if ($existing !== null) {
            return $existing;
        }
        $type = new EntryType();
        $type->name = $name;
        $type->handle = $handle;
        $type->hasTitleField = $hasTitle;
        $type->uid = StringHelper::UUID();
        $layout = new FieldLayout(['type' => Entry::class]);
        $tab = new FieldLayoutTab();
        $tab->name = 'Content';
        $tab->setLayout($layout);
        $tab->setElements($hasTitle ? [new EntryTitleField()] : []);
        $layout->setTabs([$tab]);
        $type->setFieldLayout($layout);
        if (!$entries->saveEntryType($type)) {
            throw new RuntimeException("Could not save entry type {$handle}: " . json_encode($type->getErrors()));
        }
        $saved = $entries->getEntryTypeByHandle($handle);
        if ($saved === null) {
            throw new RuntimeException("Entry type {$handle} missing after save.");
        }
        return $saved;
    }

    private function ensureSection(
        string $handle,
        string $name,
        string $uriFormat,
        string $template,
        EntryType $entryType,
        int $siteId
    ): Section {
        $entries = Craft::$app->getEntries();
        $existing = $entries->getSectionByHandle($handle);
        if ($existing !== null) {
            return $existing;
        }
        $section = new Section();
        $section->name = $name;
        $section->handle = $handle;
        $section->type = Section::TYPE_CHANNEL;
        $section->uid = StringHelper::UUID();
        $section->setEntryTypes([$entryType]);
        $section->setSiteSettings([
            $siteId => new Section_SiteSettings([
                'siteId' => $siteId,
                'enabledByDefault' => true,
                'hasUrls' => true,
                'uriFormat' => $uriFormat,
                'template' => $template,
            ]),
        ]);
        if (!$entries->saveSection($section)) {
            throw new RuntimeException("Could not save section {$handle}: " . json_encode($section->getErrors()));
        }
        $saved = $entries->getSectionByHandle($handle);
        if ($saved === null) {
            throw new RuntimeException("Section {$handle} missing after save.");
        }
        return $saved;
    }

    private function photographTabs(): array
    {
        return [
            'Content' => [
                'title',
                'featuredImage',
                'body',
                'photoDate',
                'photoCredit',
                'photoCaptionExt',
                'photoSourceCode',
                'photoSequence',
            ],
            'Credits' => [
                'creditRaw',
                'creditDpi',
                'creditProcess',
                'creditKind',
                'creditName',
            ],
            'Files' => ['archivalFiles'],
            'Relations' => [
                'photoPeople',
                'photoPlaces',
                'photoOrganizations',
                'photoEvents',
                'photoArticles',
                'photoGroups',
                'relatedPhotographs',
            ],
            self::INGEST_TAB => $this->ingestHandles('photograph'),
            'Taxonomy' => [
                'historicalEra',
                'historicalPeriod',
                'neighborhood',
                'culturalSensitivityNote',
            ],
        ];
    }

    private function documentTabs(): array
    {
        return [
            'Content' => ['title', 'featuredImage', 'body', 'documentFiles'],
            self::INGEST_TAB => $this->ingestHandles('document'),
            'Taxonomy' => [
                'historicalEra',
                'historicalPeriod',
                'neighborhood',
                'culturalSensitivityNote',
            ],
        ];
    }

    private function ingestHandles(string $entryTypeHandle): array
    {
        $handles = ['legacyKey', 'legacyUrl', 'sourcePath', 'legacyHtml', 'legacyCategory'];
        if ($entryTypeHandle === 'article') {
            $handles = ['sourcePath', 'legacyHtml', 'legacyCategory'];
        } elseif ($entryTypeHandle === 'collection') {
            $handles = ['legacyKey', 'sourcePath', 'legacyHtml', 'legacyCategory'];
        }
        return $handles;
    }

    private function applyLayout(EntryType $type, array $tabsSpec, bool $includeTitle): void
    {
        $layout = new FieldLayout(['type' => Entry::class]);
        $tabs = [];
        foreach ($tabsSpec as $name => $handles) {
            $tab = new FieldLayoutTab();
            $tab->name = $name;
            $tab->setLayout($layout);
            $tab->setElements($this->elementsForHandles($handles, $includeTitle && $name === 'Content'));
            $tabs[] = $tab;
        }
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if (!Craft::$app->getEntries()->saveEntryType($type)) {
            throw new RuntimeException("Could not save layout for {$type->handle}: " . json_encode($type->getErrors()));
        }
    }

    private function addIngestTab(EntryType $type, string $handle): void
    {
        $layout = $type->getFieldLayout();
        $present = [];
        foreach ($layout->getCustomFields() as $field) {
            $present[$field->handle] = true;
        }
        $toAdd = [];
        foreach ($this->ingestHandles($handle) as $fieldHandle) {
            if (!isset($present[$fieldHandle])) {
                $toAdd[] = $fieldHandle;
            }
        }
        if ($toAdd === []) {
            return;
        }
        $tabs = $layout->getTabs();
        $ingest = null;
        foreach ($tabs as $tab) {
            if ($tab->name === self::INGEST_TAB) {
                $ingest = $tab;
                break;
            }
        }
        if ($ingest === null) {
            $ingest = new FieldLayoutTab();
            $ingest->name = self::INGEST_TAB;
            $ingest->setLayout($layout);
            $ingest->setElements($this->elementsForHandles($toAdd, false));
            $tabs[] = $ingest;
        } else {
            $elements = $ingest->getElements();
            foreach ($this->elementsForHandles($toAdd, false) as $el) {
                $elements[] = $el;
            }
            $ingest->setElements($elements);
        }
        $layout->setTabs($tabs);
        $type->setFieldLayout($layout);
        if (!Craft::$app->getEntries()->saveEntryType($type)) {
            throw new RuntimeException("Could not add ingest tab to {$handle}: " . json_encode($type->getErrors()));
        }
    }

    private function elementsForHandles(array $handles, bool $ensureTitle): array
    {
        $elements = [];
        $fields = Craft::$app->getFields();
        if ($ensureTitle && !in_array('title', $handles, true)) {
            array_unshift($handles, 'title');
        }
        foreach ($handles as $handle) {
            if ($handle === 'title') {
                $elements[] = new EntryTitleField();
                continue;
            }
            $field = $fields->getFieldByHandle($handle);
            if ($field === null) {
                throw new RuntimeException("Missing field {$handle} for layout.");
            }
            $el = new CustomField();
            $el->setField($field);
            $el->required = false;
            $el->width = 100;
            $elements[] = $el;
        }
        return $elements;
    }
}
