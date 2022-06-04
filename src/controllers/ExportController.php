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
use craft\web\View;
use Dompdf\Dompdf;
use Dompdf\Options;
use craft\helpers\FileHelper;
use yii\web\Response;
use craft\helpers\UrlHelper;

use Craft;
use craft\web\Controller;

/**
 * Export Controller
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
class ExportController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'export-user'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/ss-user-import-export/export
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->renderTemplate('ss-user-import-export/tab/export');
    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/ss-user-import-export/export/do-something
     *
     * @return mixed
     */
    public function actionExportUser()
    {        
        if( Craft::$app->getEdition() === Craft::Solo ){            
            Craft::$app->session->setError( 'Craft Pro is required.' );
            return $this->redirect( UrlHelper::cpUrl( 'ss-user-import-export' ) );          
        }
        $this->requirePostRequest();
        $request  = Craft::$app->getRequest()->getBodyParams();            
        if( isset( $request[ 'filename' ] ) &&  !empty( $request[ 'filename' ] ) ) {
            $filename = $request[ 'filename' ].'.csv';
        }
        
        if( isset( $request[ 'status' ] ) && !empty( $request[ 'status' ] ) ) {
            $status = implode( ',', $request[ 'status' ] );
        }
        if( isset( $request[ 'userGroup' ] ) ) {
            $groups = implode( ',', $request[ 'userGroup' ] );            
            if( isset( $status ) && !empty( $status ) ) {
                $users = \craft\elements\User::find()->status( $status )->groupId( $groups )->all();
            } else {
                $users = \craft\elements\User::find()
                    ->status( ['active','suspended', 'pending', 'locked'] )
                    ->groupId( $groups )->all();
            }
        } else {
            if( isset( $status ) && !empty( $status ) ) {
                $users = \craft\elements\User::find()->status( $status )->all();
            }else{
                $users = \craft\elements\User::find()->status( [ 'active','suspended', 'pending', 'locked' ] )->all();
            }
        }
        if( !empty( $users ) ) {
            $header = [ 'username', 'email', 'group', 'firstname', 'lastname', 'status' ];
            if( isset( $request[ 'userfields' ] ) ) {
               $header = array_merge( $header, $request[ 'userfields' ] ); 
            }           
            $userdata = [];
            foreach ( $users as $key => $value ) {                             
                $userdata[ $key ][ 'username' ] = $value->username;
                $userdata[ $key ][ 'email' ] = $value->email;
                
                if( !empty( $value->getGroups() ) ) {
                    $userdata[ $key ][ 'group' ] = $value->getGroups()[0]->handle;
                } elseif ( $value->admin == 1 ) {
                    $userdata[ $key ][ 'group' ] = 'admin';                
                } else {
                    $userdata[ $key ][ 'group' ] = '';
                }

                $userdata[ $key ][ 'firstName' ] = $value->firstName;
                $userdata[ $key ][ 'lastName' ] = $value->lastName;               
                $userdata[ $key ][ 'status' ] = $value->getStatus();
                
                if( isset( $request[ 'userfields' ] ) ) {                	                	        
                    $fieldval = $this->getUserFieldVal( $request[ 'userfields' ], $value );                 
                    $userdata[ $key ] = array_merge( $userdata[ $key ], $fieldval );
                }
            }
            
            array_unshift( $userdata, $header );                
            $file = tempnam( sys_get_temp_dir(), 'export' );
            $fp = fopen( $file, 'wb' );                
            foreach ( $userdata as $result ) {
                fputcsv( $fp, $result, ',' );
            }
            fclose( $fp );
            $contents = file_get_contents( $file );
            unlink( $file );
            if( empty( $filename )) {
                $filename = 'registered_users.csv';
            }                
            $mimeType = FileHelper::getMimeTypeByExtension( $filename );                
            $response = Craft::$app->getResponse();
            $response->content = $contents;
            $response->format = Response::FORMAT_RAW;
            $response->setDownloadHeaders( $filename, $mimeType );
            return $response;
        } else {
            Craft::$app->session->setError( "Could not found user." );
            return $this->redirect( UrlHelper::cpUrl( 'ss-user-import-export' ) );
        }
        return true;
    }

    public function getUserFieldVal( $fields, $value ) {
        foreach ( $fields as $field ) {            
            $fieldType = Craft::$app->fields->getFieldByHandle( $field );
            if( $fieldType->displayName() == "Radio Buttons" || $fieldType->displayName() == 'Dropdown' ) {
            	$options = $value[ $field ]->getOptions();
                $selectedOptions = [];
                foreach ( $options as $option ) {
                    if ( $option->selected ) {
                        $selectedOptions[] = $option->value;
                    }
                }
                $fieldval[ $field ] = implode( ',', $selectedOptions );
            }else{
            	$fieldval[ $field ] = $value[ $field ];
            }            
        }        
        return $fieldval;
    }
}
