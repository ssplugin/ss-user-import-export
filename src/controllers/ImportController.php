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

/**
 * Import Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    ssplugin
 * @package   SsUserImportExport
 * @since     1.0.0
 */
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
                $plugin   = SsUserImportExport::getInstance();
                
                $settings = ['response_data'  => $data, 'response_header' => $header, 'lastUpFile' => $file_name];
                $url = UrlHelper::cpUrl('ss-user-import-export/import/element-map');
                $isSave = Craft::$app->getPlugins()->savePluginSettings( $plugin, $settings );
                Craft::$app->session->setNotice('File uploaded successfully.');
                return $this->redirect($url);               
            } else {
                Craft::$app->session->setError('Please choose a CSV file..');
            }                              
        }
    }

    public function actionUserImport(){       
        $request = Craft::$app->getRequest()->getBodyParams();        
        if( !empty($request) ){
            if( empty($request['field']['username']) || empty($request['field']['email']) || empty($request['field']['usergroup']) ){
                $url = UrlHelper::cpUrl('ss-user-import-export/import/element-map');
                Craft::$app->session->setError('Select Username, email and Group. These fields are required.');
                return $this->redirect($url);                
            }
            SsUserImportExport::$plugin->ssUserImportExportService->importUser($request);           
        }
    }
}   
