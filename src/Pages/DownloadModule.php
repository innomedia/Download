<?php

namespace Download;

use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\TextField;
use SilverStripe\Dev\Debug;
use SilverStripe\CMS\Model\SiteTree;

use Page;
use Download\DownloadCategory;
use Download\Download;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use SilverStripe\Core\Config\Config;

class DownloadModule extends Page
{
    private static $singular_name = 'Downloadmodul';
    private static $plural_name = 'Downloadmodule';
    private static $description ='Hier können Sie Dokumente hinterlegen, die in einer Übersicht zum Download angeboten werden.';

    private static $db = [
        'FooterAsignment' => 'Enum(array("Presse","Download"))',
    ];

    private static $many_many = [
        'DownloadCategories' => DownloadCategory::class,
        'Downloads' => Download::class
    ];

    private static $many_many_extraFields = [
        'DownloadCategories' => [
            'SortOrder' => 'Int'
        ],
        'Downloads' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('WeitereLeistungen');
        $fields->removeByName('AngeboteneLeistung');
        $fields->removeByName('Gallerie');
        $fields->removeByName('Icon');
        $fields->removeByName('SecondContent');
        $fields->addFieldToTab(
            'Root.Kategorien',
            GridField::create(
                'DownloadCategories',
                'DownloadCategories',
                $this->DownloadCategories(),
                GridFieldConfig_RecordEditor::create()->addComponent(new GridFieldSortableRows("SortOrder"))
            )
        );
        $fields->addFieldToTab(
            'Root.Downloads',
            GridField::create(
                'Downloads',
                'Downloads',
                $this->Downloads(),
                GridFieldConfig_RecordEditor::create()->addComponent(new GridFieldSortableRows("SortOrder"))
            )
        );
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'FooterAsignment',
                'Footer Bereich',
                $this->dbObject("FooterAsignment")->enumValues()
            )
        );
        return $fields;
    }

    public function SortedCategories()
    {
        return $this->DownloadCategories()->sort('SortOrder ASC');
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        foreach($this->Downloads() as $Download)
        {
            $Download->write();
        }
        foreach($this->DownloadCategories() as $DownloadCategories)
        {
            $DownloadCategories->write();
        }
    }


}
