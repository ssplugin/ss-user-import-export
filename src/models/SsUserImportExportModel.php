<?php
/**
 * SS User Import Export plugin for Craft CMS 3.x
 *
 * This plugin help new user import using csv and export user into the csv file.
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2020 ssplugin
 */

namespace ssplugin\ssuserimportexport\models;

use ssplugin\ssuserimportexport\SsUserImportExport;

use Craft;
use craft\base\Model;

class SsUserImportExportModel extends Model
{
    public $id;
    public $uid;
    public $response_header = '';
    public $response_data = '';
    public $lastUpFile = '';

    /**
     * @var \DateTime
     */
    public $dateCreated;

    /**
     * @var \DateTime
     */
    public $dateUpdated;

    public function dateTimeAttributes(): array
    {
        return [
            'dateCreated',
            'dateUpdated',
        ];
    }
}
