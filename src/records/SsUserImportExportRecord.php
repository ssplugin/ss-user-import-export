<?php
/**
 * SsUserImportExport plugin for Craft CMS 3.x
 *
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2021 ssplugin
 */

namespace ssplugin\ssuserimportexport\records;

use ssplugin\ssuserimportexport\SsUserImportExport;

use Craft;
use craft\db\ActiveRecord;


class SsUserImportExportRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ssuserimportdata}}';
    }
}
