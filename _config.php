<?php

use SilverStripe\Dev\Debug;
use SilverStripe\Admin\CMSMenu;
use Download\DownloadModelAdmin;
use SilverStripe\Core\Config\Config;



if(Config::inst()->get("DownloadModuleConfig") != null)
{
    if(!array_key_exists("ModelAdminVisible",Config::inst()->get("DownloadModuleConfig")))
    {
        CMSMenu::remove_menu_class(DownloadModelAdmin::class);
    }
    if(array_key_exists("ModelAdminVisible",Config::inst()->get("DownloadModuleConfig")) && Config::inst()->get("DownloadModuleConfig")["ModelAdminVisible"] == false)
    {
        CMSMenu::remove_menu_class(DownloadModelAdmin::class);
    }
}
else
{
    CMSMenu::remove_menu_class(DownloadModelAdmin::class);
}
