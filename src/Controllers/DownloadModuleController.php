<?php
namespace Download;
use SilverStripe\CMS\Controllers\ContentController;
use PageController;

class DownloadModuleController extends PageController
{
  protected function init()
  {
      parent::init();
      $this->extend('updateDownloadModuleInit');
  }
  
}
