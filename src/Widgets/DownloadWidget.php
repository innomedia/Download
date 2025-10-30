<?php

namespace Download;
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
    private static string $title = 'Download Widget';

    private static string $cmsTitle = 'Download Widget';

    private static string $description = 'Displays one Download';

    private static array $db = [
    ];

    private static array $has_one = [
        'Download' => Download::class,
    ];

    private static string $table_name = 'DownloadWidget';

    /**
     * {@inheritdoc}
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields): void {

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
