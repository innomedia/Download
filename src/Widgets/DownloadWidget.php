<?php

namespace Download;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Widgets\Model\Widget;

if (!class_exists(Widget::class)) {
    return;
}

/**
 * @method Blog Blog()
 *
 * @property string $ArchiveType
 * @property int $NumberToDisplay
 */
class DownloadWidget extends Widget
{
    /**
     * @var string
     */
    private static $title = 'Download Widget';

    /**
     * @var string
     */
    private static $cmsTitle = 'Download Widget';

    /**
     * @var string
     */
    private static $description = 'Displays one Download';

    /**
     * @var array
     */
    private static $db = [
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Download' => Download::class,
    ];

    /**
     * @var string
     */
    private static $table_name = 'DownloadWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            /**
             * @var FieldList $fields
             */
            $fields->merge([
                DropdownField::create(
                    'DownloadID',
                    Download::class,
                    Download::get()->map()
                )
            ]);
        });

        return parent::getCMSFields();
    }
}
