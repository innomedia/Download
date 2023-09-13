<?php
namespace Download;

use SilverStripe\Admin\ModelAdmin;

class DownloadModelAdmin extends ModelAdmin
{
    private static $managed_models = [
        Download::class,
        DownloadCategory::class,

    ];
    private static $url_segment = 'Downloads';

    private static $menu_title = 'Downloads';
    
}