<?php
namespace Download;

use SilverStripe\Admin\ModelAdmin;

class DownloadModelAdmin extends ModelAdmin
{
    private static array $managed_models = [
        Download::class,
        DownloadCategory::class,

    ];
    
    private static string $url_segment = 'Downloads';

    private static string $menu_title = 'Downloads';
    
}