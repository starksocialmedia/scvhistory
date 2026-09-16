<?php

namespace craft\contentmigrations;

use Craft;
use craft\db\Migration;
use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
use craft\fieldlayoutelements\entries\EntryTitleField;
use craft\fields\Entries;
use craft\fields\PlainText;
use craft\helpers\StringHelper;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use RuntimeException;

class m260915_210000_war_memorial_section extends Migration
{
    public function safeUp(): bool
    {
        $entries = Craft::$app->getEntries();
        $site = Craft::$app->getSites()->getPrimarySite();
        $persons = $entries->getSectionByHandle('persons');
        if ($persons === null) {
            throw new RuntimeException('Persons section missing.');
        }

        $this->ensurePlainText('wmBranch', 'WM Branch', false);
        $this->ensurePlainText('wmRank', 'WM Rank', false);
        $this->ensurePlainText('wmUnit', 'WM Unit', false);
        $this->ensurePlainText('wmConflict', 'WM Conflict', false);
        $this->ensurePlainText('wmHomeOfRecord', 'WM Home of Record', false);
        $this->ensureEntriesField('wmRelatedPerson', 'WM Related Person', $persons->uid, 1);

        $type = $this->ensureEntryType('warMemorial', 'War Memorial', true);
        $this->ensureSection(
            'warMemorials',
            'War Memorials',
            'war-memorial/{slug}',
            'war-memorial/_entry',
            $type,
            $site->id
        );
        $this->applyLayout($type, [
            'Content' => ['title', 'featuredImage', 'body', 'deathDate', 'burialPlace'],
            'Service' => ['wmBranch', 'wmRank', 'wmUnit', 'wmConflict', 'wmHomeOfRecord', 'wmRelatedPerson'],
            'Ingest' => ['legacyKey', 'legacyUrl', 'sourcePath', 'legacyHtml'],
        ], true);

        return true;
    }

    public function safeDown(): bool
    {
        return false;
    }

    private function ensurePlainText(string $handle, string $name, bool $multiline): PlainText
    {
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
        $field->searchable = false;
        $field->uid = StringHelper::UUID();
        if (!$fields->saveField($field)) {
            throw new RuntimeException("Could not save {$handle}: " . json_encode($field->getErrors()));
        }
        return $field;
    }

    private function ensureEntriesField(string $handle, string $name, string $sectionUid, ?int $max): Entries
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
        $field->maxRelations = $max;
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
        $tab->setElements([new EntryTitleField()]);
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
