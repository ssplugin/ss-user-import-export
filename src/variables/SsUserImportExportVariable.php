<?php
/**
 * SS User Import Export plugin for Craft CMS 3.x
 *
 * This plugin help new user import using csv and export user into the csv file.
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2020 ssplugin
 */

namespace ssplugin\ssuserimportexport\variables;
use ssplugin\ssuserimportexport\SsUserImportExport;
use Craft;

class SsUserImportExportVariable
{
    public function getImportData(){
        $importData = SsUserImportExport::getInstance()->ssuserimportservice->getImportData();
        $data = [];
        if( !empty( $importData ) ){
            $data[ 'response_header' ] = json_decode( $importData->response_header );
            $data[ 'response_data' ] = json_decode( $importData->response_data );
            $data[ 'lastUpFile' ] = $importData->lastUpFile;
        }
        return $data;
    }    
}
