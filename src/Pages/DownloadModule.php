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
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class DownloadModule extends Page
{
    private static $description = 'Hiermit können Sie eine Downloadseite erstellen - Downloads werden direkt im Module gepflegt';

    private static $db = [
        'FooterAsignment' => 'Enum(array("Presse","Download"))',
    ];

    private static $has_many = [
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
        if (Config::inst()->get("DownloadModuleConfig")["CategoriesEnabled"]) {
            $fields->addFieldToTab(
                'Root.Kategorien',
                GridField::create(
                    'DownloadCategories',
                    'DownloadCategories',
                    $this->DownloadCategories(),
                    GridFieldConfig_RecordEditor::create()->addComponent(GridFieldOrderableRows::create("SortOrder"))
                )
            );
        }
        $fields->addFieldToTab(
            'Root.Downloads',
            GridField::create(
                'Downloads',
                'Downloads',
                $this->Downloads(),
                GridFieldConfig_RecordEditor::create()->addComponent(GridFieldOrderableRows::create("SortOrder"))
            )
        );
        /*$fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'FooterAsignment',
                'Footer Bereich',
                $this->dbObject("FooterAsignment")->enumValues()
            )
        );*/
        $this->extend('updateDownloadCMSFields', $fields);
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
