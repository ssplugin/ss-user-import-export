<?php
/**
 * SS User Import Export plugin for Craft CMS 3.x
 *
 * This plugin help new user import using csv and export user into the csv file.
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2020 ssplugin
 */

namespace ssplugin\ssuserimportexport\services;

use ssplugin\ssuserimportexport\SsUserImportExport;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use craft\web\Request;
use craft\elements\User;
use yii\web\NotFoundHttpException;

/**
 * SsUserImportExportService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    ssplugin
 * @package   SsUserImportExport
 * @since     1.0.0
 */
class SsUserImportExportService extends Component
{
    // Public Methods
    // =========================================================================    
    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     SsUserImportExport::$plugin->ssUserImportExportService->importUser()
     *
     * @return mixed
     */
    public function importUser($request)
    {        
        $settings = Craft::$app->plugins->getPlugin('ss-user-import-export')->getSettings();       
        $usercount = 0;                         
        foreach ( $settings->response_data as $key => $value ) {
            $fields = [];
            foreach ( $request['field'] as $k => $val ) {
                
                if( $k != 'default_group' && $k != 'default_userstatus' ){
                    if( $val != '' ) {
                        $fields[$k] = $value[$val];
                    }else{
                        $fields[$k] = '';
                    }
                }else{
                    $fields[$k] = $val;
                    $fields[$k] = $val;
                }
            }              
            if( !empty( $fields['username'] ) && !empty($fields['email']) ){               
                if (filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                    $isUsernameExist = User::find()->username($fields['username'])->status(['active','suspended', 'pending', 'locked'])->one();
                    $isEmailExist = User::find()->email($fields['email'])->status(['active','suspended', 'pending', 'locked'])->one();                         
                    if( empty($isUsernameExist) && empty($isEmailExist)) {
                        $group = '';
                        if( !empty( $fields['default_group'] ) ){
                            $group = $fields['default_group'];
                        }else{
                            if( $fields['usergroup'] == 'admin') {
                                $group = 'admin';
                            } else {
                                $groups = Craft::$app->userGroups->getGroupByHandle( $fields['usergroup'] );
                                if( !empty($groups)){
                                    $group = $groups['id'];
                                } else {
                                    $projectConfig = Craft::$app->getSystemSettings()->getSettings('users');
                                    if( !empty($projectConfig['defaultGroup']) ){
                                        $defaultGroup = Craft::$app->userGroups->getGroupByUid($projectConfig['defaultGroup']);
                                        $group = $defaultGroup['id'];
                                    }
                                }
                            } 
                        }
                        if( isset($group) && !empty($group)){                                                    
                            if( !empty($fields)){                               

                                $user = new User();                    
                                $user->username = $fields['username'];
                                $user->firstName= $fields["firstName"];
                                $user->lastName = $fields["lastName"];
                                $user->email    = $fields["email"];
                                if(!empty($fields["password"]) ){
                                    $user->newPassword  = trim($fields["password"]);    
                                }
                                if( $group == 'admin'){
                                    $user->admin  = true;
                                }
                                if( !empty( $fields['default_userstatus'] ) ){
                                    $userStatus = $fields['default_userstatus'];
                                }else{
                                    $userStatus = $fields['userstatus'];
                                }
                                switch (strtolower($userStatus)) {
                                    case 'active':
                                    case '1':
                                        $user->pending  = false;
                                        break;
                                    case 'pending':
                                    case '0':
                                        $user->pending  = true;
                                        break;
                                    case 'suspended':                        
                                        $user->suspended  = true;
                                        break;
                                    default:
                                        $user->pending  = true;
                                        break;
                                }
                                
                                if( isset($request['userfield']) && !empty($request['userfield']) ){
                                    $userfield = [];
                                    foreach ($request['userfield'] as $k => $v) {
                                        if( !empty($v) ) {
                                            $userfield[$k] = $value[$v];
                                        }
                                    }
                                    if( !empty($userfield) ) {
                                        $user->setFieldValues( $userfield );
                                    }
                                }                                   
                                $isSaveUser = Craft::$app->getElements()->saveElement($user, false);
                                if( $isSaveUser ){
                                    $usercount++;                                    
                                    if( $group != 'admin'){
                                        Craft::$app->users->assignUserToGroups($user->id, [$group]);
                                    }
                                    if($request['sendmail'] == 'yes'){
                                        if( strtolower($userStatus) == 'active' || strtolower($userStatus) == '1'){
                                            Craft::$app->getUsers()->sendActivationEmail($user);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if( $usercount > 0 ){
            Craft::$app->session->setNotice("Users has been added successfully.");
            return;
        }
        return;
    }
}
