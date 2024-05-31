<?php

namespace Download;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HtmlEditorField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

use Download\Download;
use SilverStripe\Dev\Debug;
use SilverStripe\Core\Config\Config;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DownloadCategory extends DataObject
{
    private static $table_name = 'DownloadCategory';

    private static $db = [
        'Title' => 'Text',
        'Content' => 'HTMLText',
        'Style' => 'Varchar',
        'TagSortTitle' => 'Text',
        'SortOrder' =>  'Int'
        
    ];

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->TagSortTitle != $this->Title) {
            $this->TagSortTitle = $this->Title;
            $this->write();
        }
    }

    private static $belongs_many_many = [
        'Downloads' => Download::class
        
    ];

    private static $has_one = [
        'DownloadModule' => DownloadModule::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'Downloads',
            'Style',
            'Content',
            'TagSortTitle',
            'SortOrder',
            'DownloadModuleID'
        ]);
        if (Config::inst()->get("DownloadModuleConfig")["CategoryStyles"] != "" && count($Styles = explode(",", Config::inst()->get("DownloadModuleConfig")["CategoryStyles"])) > 1) {
            $array = array();
            foreach (explode(",", Config::inst()->get("DownloadModuleConfig")["CategoryStyles"]) as $key => $value) {
                $array[$value] = $value;
            }
            $fields->addFieldToTab(
                'Root.Main',
                DropdownField::create(
                    'Style',
                    'Style',
                    $array
                )
            );
        }
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'Title',
                'Titel'
            )
        );
        if (Config::inst()->get("DownloadModuleConfig")["CategoriesHaveContent"] == true) {
            $fields->addFieldToTab(
                'Root.Main',
                HtmlEditorField::create(
                    'Content',
                    'Inhalt'
                )
            );
        }
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function Link()
    {
        return $this->DownloadModule()->Link() . "#DownloadCat" . $this->ID;
    }

    public function SortedDownloads()
    {
        return $this->Downloads()->sort("SortOrder ASC");
    }
}
