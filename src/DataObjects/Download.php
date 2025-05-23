<?php

namespace Download;

use Download\DownloadCategory;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

class Download extends DataObject
{
    public static $convert_path = '/usr/bin/convert';

    private static $table_name = 'Downloads';

    private static $db = [
        'Title' => 'Text',
        'TagSortTitle' => 'Text',
        'SortOrder' => 'Int',
        'ShowNewDownload' => 'Boolean',
    ];

    private static $has_one = [
        'File' => File::class,
        'PreviewThumbnail' => Image::class,
        'DownloadModule' => DownloadModule::class,
    ];

    private static $many_many = [
        'DownloadCategories' => DownloadCategory::class,
    ];

    public function PreparedTitle()
    {
        return html_entity_decode(str_replace("|", "&shy;", $this->Title));
    }

    public function PublishFiles($CanViewType)
    {
        $this->PublishFile($this->File(), $CanViewType);
        $this->PublishFile($this->PreviewThumbnail(), $CanViewType);
        $this->extend("UpdatePublishFiles", $CanViewType);
    }
    public function PublishFile($file, $CanViewType)
    {
        $writefile = false;
        if ($file->CanViewType != $CanViewType) {
            $file->CanViewType = $CanViewType;
            $writefile = true;
        }
        if ($file->ViewerGroups()->count() > 0) {
            $writefile = true;
            foreach ($file->ViewerGroups() as $deleteGroup) {
                $file->ViewerGroups()->remove($deleteGroup);
            }
        }
        if ($writefile) {
            $file->PublishFile();
            $file->write();
        }
    }

    public function ProtectFiles($CanViewType, $ViewerGroups)
    {
        $this->ProtectFile($this->File(), $CanViewType, $ViewerGroups);
        $this->ProtectFile($this->PreviewThumbnail(), $CanViewType, $ViewerGroups);
        $this->extend("UpdateProtectFiles", $CanViewType, $ViewerGroups);
    }
    public function ProtectFile($file, $CanViewType, $ViewerGroups = null)
    {
        $writefile = false;
        if ($file->CanViewType != $CanViewType) {
            $file->CanViewType = $CanViewType;
            $writefile = true;
        }
        if ($ViewerGroups != null && count($ViewerGroups) > 0 && $CanViewType == "OnlyTheseUsers") {
            $groupids = [];
            foreach ($ViewerGroups as $group) {
                $groupids[] = $group->ID;
                if ($file->ViewerGroups()->filter("ID", $group->ID)->Count() == 0) {
                    $file->ViewerGroups()->add($group);
                    $writefile = true;
                }
            }
            if (count($groupids) > 0) {
                foreach ($file->ViewerGroups()->exclude("ID", $groupids) as $removedGroup) {
                    $file->ViewerGroups()->remove($removedGroup);
                    $writefile = true;
                }
            }
        } else {
            foreach ($file->ViewerGroups() as $group) {
                $file->ViewerGroups()->remove($group);
                $writefile = true;
            }
        }

        if ($writefile == true) {
            $file->write();
        }
    }

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'DownloadCategories',
            'TagSortTitle',
            'DownloadModuleID',
            'SortOrder',
            'PreviewThumbnail',
        ]);
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'Title',
                'Titel'
            )
        );
        $fields->addFieldToTab(
            'Root.Main',
            UploadField::create(
                'File',
                'Datei'
            )
        );
        if(Config::inst()->get("DownloadModuleConfig")["PreviewImageEnabled"]){
            $fields->addFieldToTab(
                'Root.Main',
                UploadField::create(
                    'PreviewThumbnail',
                    'Vorschau Bild'
                )
            );
        }
        if (Config::inst()->get("DownloadModuleConfig")["CategoriesEnabled"]) {
            $fields->addFieldToTab('Root.Main', CheckboxSetField::create('DownloadCategories', 'Kategorien', DownloadCategory::get()->map()));
        }
        $fields->addFieldToTab('Root.Main', CheckboxField::create('ShowNewDownload', 'Als NEU markieren'));

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function RenderMe($Layout = "Download\Download")
    {
        return $this->renderWith($Layout);
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->PreviewThumbnailID == 0 && Config::inst()->get("DownloadModuleConfig")["PreviewImageEnabled"]) {
            //\SilverStripe\Dev\Debug::dump($this->File()->FileName);die;
            $store = Injector::inst()->get(AssetStore::class);
            $file_filename = Director::baseFolder() . "/public/assets" . str_replace("assets/", "", $store->getAsURL($this->File()->FileName, $this->File()->getHash()));
            if (!file_exists($file_filename)) {
                $file_filename = Director::baseFolder() . "/public/assets/.protected" . str_replace("assets/", "", $store->getAsURL($this->File()->FileName, $this->File()->getHash()));
            }
            try {
                if (strpos(strtolower($this->File()->FileName), ".pdf") !== false) {
                    $cache_filename = str_replace(".pdf", "", str_replace("Uploads/", "", $this->File()->Name)) . ".jpg";
                    $absoluteFilePath = "/tmp/" . $cache_filename;
                    $command = self::$convert_path . ' ' . escapeshellarg($file_filename . '[' . (0) . ']') . ' -background "#FFFFFF" -flatten -quality 90 ' . escapeshellarg($absoluteFilePath);
                    $out = shell_exec($command);
                    if (file_exists($absoluteFilePath)) {
                        $img = new Image();
                        $img->setFromLocalFile($absoluteFilePath, 'Uploads/' . str_replace("/tmp/", "", $absoluteFilePath));
                        $img->write();

                        // Das Bild veröffentlichen
                        if ($img->hasMethod('publishSingle')) {
                            $img->publishSingle();
                        } else {
                            $img->doPublish();
                        }

                        $this->PreviewThumbnailID = $img->ID;
                        $this->write();
                    }
                }
            } catch (\Exception $ex) {

            }

        }
        if (($this->Title == null || $this->Title == "") && $this->TagSortTitle != "") {
            $this->Title = $this->TagSortTitle;
            $this->write();
        }
        if ($this->TagSortTitle != $this->Title) {
            $this->TagSortTitle = $this->Title;
            $this->write();
        }
        $protecteddownloadmodule = $this->DownloadModule()->ViewerGroups()->Count() > 0 && $this->DownloadModule()->CanViewType == "OnlyTheseUsers" || $this->DownloadModule()->CanViewType == "LoggedInUsers";
        if ($protecteddownloadmodule) {
            $this->ProtectFiles($this->DownloadModule()->CanViewType, $this->DownloadModule()->ViewerGroups());
        } else {
            $this->PublishFiles($this->DownloadModule()->CanViewType);
        }
    }
    
}
