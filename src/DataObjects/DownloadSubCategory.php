<?php
namespace Download;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DownloadSubCategory extends DataObject
{
  /**
   * Defines the database table name
   * @var string
   */
  private static $table_name = 'DownloadSubCategory';
  /**
   * Database fields
   * @var array
   */
  private static $db = [
    'Title' => 'Text',
    'Style' =>  'Varchar'
  ];
  /**
   * Belongs_to relationship
   * @var array
   */
  private static $has_one = [
    'DownloadCategory'  => DownloadCategory::class,
  ];
  /**
   * Belongs_many_many relationship
   * @var array
   */
  private static $belongs_many_many = [
    'Downloads' => Download::class,
  ];

  /**
   * CMS Fields
   * @return FieldList
   */
  public function getCMSFields()
  {
    $fields = parent::getCMSFields();
    $fields->removeByName([
      'Downloads'
    ]);
    if(Config::inst()->get("DownloadModuleConfig")["UseCategoryStylesForSubCategories"] == true)
    {
      if(Config::inst()->get("DownloadModuleConfig")["CategoryStyles"] != "" && count($Styles = explode(",",Config::inst()->get("DownloadModuleConfig")["CategoryStyles"])) > 1)
      {
        $array = array();
        foreach(explode(",",Config::inst()->get("DownloadModuleConfig")["CategoryStyles"]) as $key => $value)
        {
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
    }
    else
    {
      if(Config::inst()->get("DownloadModuleConfig")["SubCategoryStyles"] != "" && count($Styles = explode(",",Config::inst()->get("DownloadModuleConfig")["SubCategoryStyles"])) > 1)
      {
        $array = array();
        foreach(explode(",",Config::inst()->get("DownloadModuleConfig")["SubCategoryStyles"]) as $key => $value)
        {
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
    }

    $fields->addFieldToTab(
      'Root.Main',
      TextField::create(
        'Title',
        'Titel'
      )
    );
    $this->extend('updateCMSFields', $fields);
    return $fields;
  }
}
