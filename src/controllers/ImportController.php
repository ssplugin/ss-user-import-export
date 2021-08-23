<?php
/**
 * SS User Import Export plugin for Craft CMS 3.x
 *
 * This plugin help new user import using csv and export user into the csv file.
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2020 ssplugin
 */

namespace ssplugin\ssuserimportexport\controllers;

use ssplugin\ssuserimportexport\SsUserImportExport;
use craft\helpers\UrlHelper;
use craft\web\Request;
use craft\elements\User;
use yii\web\NotFoundHttpException;
use Craft;
use craft\web\Controller;
use ssplugin\ssuserimportexport\models\SsUserImportExportModel;
use ssplugin\ssuserimportexport\records\SsUserImportExportRecord;

class ImportController extends Controller
{    
    protected $allowAnonymous = ['index', 'import-user','user-import', 'element-map'];

    public function actionIndex()
    {
        $this->renderTemplate('ss-user-import-export/tab/import');
    }

    public function actionElementMap()
    {        
        $this->renderTemplate('ss-user-import-export/tab/element-map');
    }
    
    public function actionImportUser()
    {        
        $request = Craft::$app->getRequest()->getBodyParams();    
        //Make sure activated craft pro version.
        if( Craft::$app->getEdition() === Craft::Solo ){            
            Craft::$app->session->setError('Craft Pro is required.');
            return $this->redirect(UrlHelper::cpUrl('ss-user-import-export/import'));          
        }
        
        if( isset( $_FILES['file'] ) ) {            
            $file_name = $_FILES['file']['name'];          
            $file_tmp  = $_FILES['file']['tmp_name'];            
            $ext       = pathinfo($file_name, PATHINFO_EXTENSION);
            if( $ext == 'csv' ) {
                $str = fopen($file_tmp, 'r');
                $header = NULL;
                $data = [];
                while ( ( $row = fgetcsv( $str, 1000, ',' ) ) !== FALSE)
                {
                    
                    if( !$header ) {
                        $header = $row;
                    } else {  
                        if (count($header) == count($row)) {
                            $data[] = array_combine($header, $row);
                        }
                    }
                }                
                fclose($str);
                if( !empty( $data ) && !empty( $header ) ){
                    $existimportData = SsUserImportExport::getInstance()->ssuserimportservice->getImportData();
                    if( !empty( $existimportData ) && !empty( $existimportData->response_data ) ){
                        $record = SsUserImportExport::getInstance()->ssuserimportservice->getImportData();
                    }else{
                        $record = new SsUserImportExportModel();
                    }
                    $record->response_data = json_encode( $data );
                    $record->response_header = json_encode( $header );
                    $record->lastUpFile = $file_name;

                    if($record->validate()){
                        
                        $isSave = SsUserImportExport::getInstance()->ssuserimportservice->saveImportData($record);
                        if( $isSave ){
                            Craft::$app->getSession()->setNotice('File uploaded successfully');
                            $url = UrlHelper::cpUrl('ss-user-import-export/import/element-map');
                            return $this->redirect($url);
                        }else{
                            Craft::$app->getSession()->setNotice('Failed to upload file');
                        }
                    }   
                }else{
                    Craft::$app->getSession()->setError('CSV file data is not valid format');
                }
                            
            } else {
                Craft::$app->session->setError('Please choose a CSV file..');
            }                              
        }
    }

    public function actionUserImport() {       
        $request = Craft::$app->getRequest()->getBodyParams();        
        if( !empty( $request ) ){
            $url = UrlHelper::cpUrl( 'ss-user-import-export/import/element-map' );
            if( empty( $request[ 'field' ][ 'username' ] ) || empty( $request[ 'field' ][ 'email' ] ) ){
                Craft::$app->session->setError( 'Username and Email are required.' );  
                return $this->redirect( $url );      
            }
            if( empty( $request[ 'field' ][ 'usergroup' ] ) && empty( $request[ 'field' ][ 'default_group' ] ) ) {
                Craft::$app->session->setError( 'User Group is required.' );
                return $this->redirect( $url );
            }
            SsUserImportExport::$plugin->ssUserImportExportService->importUser( $request );           
        }
    }
}   
