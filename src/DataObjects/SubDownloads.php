<?php
namespace Download;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\File;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class SubDownload extends DataObject
{
  /**
   * Defines the database table name
   * @var string
   */
  private static $table_name = 'DownloadSubDownload';
  /**
   * Database fields
   * @var array
   */
  private static $db = [
    'Title' => 'Text',
    'SortOrder' => 'Int'
  ];
  /**
   * Has_one relationship
   * @var array
   */
  private static $has_one = [
    'File' => File::class,
    'Download'  =>  Download::class
  ];
  /**
   * CMS Fields
   * @return FieldList
   */
  public function getCMSFields()
  {
    $fields = parent::getCMSFields();
    $fields->removeByName([
      'Download',
      'File',
      'SortOrder'
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
    $this->extend('updateCMSFields', $fields);
    return $fields;
  }
}
