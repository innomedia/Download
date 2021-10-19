<?php

namespace Download;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\TagField\TagField;

use Download\DownloadCategory;
use Download\DownloadSubCategory;
use Download\SubDownload;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Control\Director;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\Forms\CheckboxSetField;

class Download extends DataObject
{
    public static $convert_path = '/usr/bin/convert';

    private static $table_name = 'Downloads';

    private static $db = [
        'Title' => 'Text',
        'SubDownloadContent' => 'HTMLText',
        'TagSortTitle' => 'Text'
    ];

    private static $has_one = [
        'File' => File::class,
        'PreviewThumbnail' => Image::class
    ];

    private static $many_many = [
        'DownloadCategories' => DownloadCategory::class,
        'DownloadSubCategories' => DownloadSubCategory::class
    ];

    public function Title()
    {
        return html_entity_decode(str_replace("|", "&shy;", $this->Title));
    }

    private static $has_many = [
        'SubDownloads' => SubDownload::class
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'DownloadCategories',
            'DownloadSubCategories',
            'SubDownloads',
            'SubDownloadContent',
            'TagSortTitle'
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
        $fields->addFieldToTab(
            'Root.Main',
            UploadField::create(
                'PreviewThumbnail',
                'Vorschau Bild'
            )
        );
        $fields->addFieldToTab('Root.Main', CheckboxSetField::create('DownloadCategories', 'Kategorien', DownloadCategory::get()->map()));

        /*
        $fields->addFieldToTab(
          'Root.Main',
          TagField::create(
              'DownloadCategories',
              'Download Kategorien',
              DownloadCategory::get(),
              $this->DownloadCategories()
          )->setShouldLazyLoad(true)->setCanCreate(true)->setTitleField("TagSortTitle")
        );
        */
        if (Config::inst()->get("DownloadModuleConfig")["SubDownloadsEnabled"]) {
            $fields->addFieldToTab(
                'Root.Unterteilte Downloads',
                GridField::create(
                    'SubDownloads',
                    'Download (Unterteilt)',
                    $this->SubDownloads(),
                    GridFieldConfig_RecordEditor::create()->addComponent(new GridFieldSortableRows('SortOrder'))
                )
            );
            $fields->addFieldToTab(
                'Root.Unterteilte Downloads',
                HtmlEditorField::create(
                    'SubDownloadContent',
                    'Inhalt'
                )
            );
        }
        if (Config::inst()->get("DownloadModuleConfig")["SubCategoriesEnabled"]) {
            $fields->addFieldToTab(
                'Root.Main',
                TagField::create(
                    'DownloadSubCategories',
                    'Download Unter Kategorien',
                    DownloadSubCategory::get(),
                    $this->DownloadSubCategories()
                )->setShouldLazyLoad(true)->setCanCreate(true)
            );
        }
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
        if ($this->PreviewThumbnailID == 0) {
            //\SilverStripe\Dev\Debug::dump($this->File()->FileName);die;
            $store = Injector::inst()->get(AssetStore::class);
            $file_filename = Director::baseFolder() . "/public/assets" . str_replace("assets/", "", $store->getAsURL($this->File()->FileName, $this->File()->getHash()));
            if(!file_exists($file_filename)){
                $file_filename = Director::baseFolder() . "/public/assets/.protected" . str_replace("assets/", "", $store->getAsURL($this->File()->FileName, $this->File()->getHash()));
            }

            if (strpos($this->File()->FileName, ".pdf") !== false) {
                $cache_filename = str_replace(".pdf", "", str_replace("Uploads/", "", $this->File()->Name)) . ".jpg";
                $absoluteFilePath = "/tmp/" . $cache_filename;
                $command = self::$convert_path . ' ' . escapeshellarg($file_filename . '[' . (0) . ']') . ' -background "#FFFFFF" -flatten -quality 90 ' . escapeshellarg($absoluteFilePath);
                $out = shell_exec($command);
                $img = new Image();
                $img->setFromLocalFile($absoluteFilePath, 'Uploads/' . str_replace("/tmp/", "", $absoluteFilePath));
                $img->write();
                $img->doPublish();

                $this->PreviewThumbnailID = $img->ID;
                $this->write();
            }
        }
        if (($this->Title == NULL || $this->Title == "") && $this->TagSortTitle != "") {
            $this->Title = $this->TagSortTitle;
            $this->write();
        }
        if ($this->TagSortTitle != $this->Title) {
            $this->TagSortTitle = $this->Title;
            $this->write();
        }
    }
}
