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
                    $this->SortedCategories(),
                    GridFieldConfig_RecordEditor::create()->addComponent(GridFieldOrderableRows::create("SortOrder"))
                )
            );
        }
        $fields->addFieldToTab(
            'Root.Downloads',
            GridField::create(
                'Downloads',
                'Downloads',
                $this->sortedDownloads(),
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
    
    public function sortedDownloads()
    {
        return $this->Downloads()->sort('SortOrder ASC');
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        $protecteddownloadmodule = $this->ViewerGroups()->Count() > 0 && $this->CanViewType == "OnlyTheseUsers" || $this->CanViewType == "LoggedInUsers";
        foreach($this->Downloads() as $Download)
        {
            if($protecteddownloadmodule)
            {
                $Download->ProtectFiles($this->CanViewType,$this->ViewerGroups());
            }
            $Download->write();
            if(!$protecteddownloadmodule)
            {
                $Download->PublishFiles($this->CanViewType);
            }
        }
        foreach($this->DownloadCategories() as $DownloadCategories)
        {
            $DownloadCategories->write();
        }
    }

    public function canCreate($member = null,$context = [])
    {
        if(Config::inst()->get("DownloadModuleConfig") == null || Config::inst()->get("DownloadModuleConfig") != null && !array_key_exists("CanCreatePages",Config::inst()->get("DownloadModuleConfig")))
        {
            //if not configured alway allow
            return true;
        }
        return Config::inst()->get("DownloadModuleConfig")["CanCreatePages"] && parent::canCreate($member,$context);
    }


}
