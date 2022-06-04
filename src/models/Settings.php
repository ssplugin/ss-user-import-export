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

/**
 * SsUserImportExport Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    ssplugin
 * @package   SsUserImportExport
 * @since     1.0.0
 */
class Settings extends Model
{    
    /**
     * Some field model attribute
     *
     * @var string
     */
    public $response_header = '';
    public $response_data = '';
    public $lastUpFile = '';
}
