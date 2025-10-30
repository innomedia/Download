<?php
namespace Download;
use PageController;

class DownloadModuleController extends PageController
{
  protected function init()
  {
      parent::init();
      $this->extend('updateDownloadModuleInit');
  }
  
}
