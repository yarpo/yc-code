<?php

/**
 * @package JFusion
 * @subpackage Plugin_Auth
 * @author JFusion development team
 * @copyright Copyright (C) 2008 JFusion. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
* Load the JFusion framework
*/
jimport('joomla.event.plugin');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.factory.php');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php');

/**
* JFusion Authentication class
* @package JFusion
*/
class plgAuthenticationjfusion extends JPlugin
{
	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for
	* plugins because func_get_args ( void ) returns a copy of all passed arguments
	* NOT references. This causes problems with cross-referencing necessary for the
	* observer design pattern.
	*/
	function plgAuthenticationjfusion(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the language
		$this->loadLanguage('com_jfusion', JPATH_BASE);
	}

	/**
	* This method should handle any authentication and report back to the subject
	*
	* @access public
	* @param array $credentials Array holding the user credentials
	* @param array $options Array of extra options
	* @param object $response Authentication response object
	* @return boolean
	* @since 1.5
	*/
	function onAuthenticate($credentials, $options, &$response )
	{
		jimport('joomla.user.helper');
		global $mainframe;

		// Initialize variables
        $response->debug = array();
		$conditions = '';
		$db =& JFactory::getDBO();

		//get the JFusion master
		$master = JFusionFunction::getMaster();
		if(!empty($master)) {
			$JFusionMaster =& JFusionFactory::getUser($master->name);
			$userinfo = $JFusionMaster->getUser($credentials['username']);

			//check if a user was found
			if (!empty($userinfo)) {

				//check to see if the login checker wanted a skip password
				$debug = JFusionFunction::isAdministrator();
        		if (!empty($options['skip_password_check']) && $debug === true){
			        $response->debug[] = JText::_('SKIPPED'). ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') ;
					$response->status = JAUTHENTICATE_STATUS_SUCCESS;
					$response->email = $userinfo->email;
					$response->fullname = $userinfo->name;
					$response->error_message = '';
					$response->userinfo = $userinfo;
					$result = true;
					return $result;
		        }

				// Joomla does not like blank passwords
				if (empty($credentials['password'])) {
					$response->status = JAUTHENTICATE_STATUS_FAILURE;
					$response->error_message = JText::_('EMPTY_PASSWORD_NO_ALLOWED');
					$result = false;
					return $result;
				}		        
		        
				//store this to be stored jfusion_user table by the joomla_int createUser function
				$userinfo->credentialed_username = $credentials['username'];

				//apply the cleartext password to the user object
				$userinfo->password_clear = $credentials['password'];

				//check the master plugin for a valid password
				$model =& JFusionFactory::getAuth($master->name);
				$testcrypt = $model->generateEncryptedPassword($userinfo);
				if ($testcrypt == $userinfo->password){
					//found a match
			        $response->debug[] = $master->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('SUCCESS');
					$response->status = JAUTHENTICATE_STATUS_SUCCESS;
					$response->email = $userinfo->email;
					$response->fullname = $userinfo->name;
					$response->error_message = '';
					$response->userinfo = $userinfo;
					$result = true;
					return $result;
				}
				if(isset($options['show_unsensored'])){
			        $response->debug[] = $master->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('INVALID') . ' ' . $testcrypt. ' vs ' .$userinfo->password;					
				} else {
			        $response->debug[] = $master->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('INVALID');				
				}


				//otherwise check the other authentication models
				$query = 'SELECT name FROM #__jfusion WHERE master = 0 AND check_encryption = 1';
				$db->setQuery($query);
				$auth_models = $db->loadObjectList();

				//loop through the different models
				foreach($auth_models as $auth_model) {
					//Generate an encrypted password for comparison
					$model =& JFusionFactory::getAuth($auth_model->name);
					$JFusionSlave =& JFusionFactory::getUser($auth_model->name);
            		$slaveuserinfo = $JFusionSlave->getUser($userinfo);
            		// add in the clear password to be able to generate the hash
            		if (!empty($slaveuserinfo)){
            			$slaveuserinfo->password_clear = $userinfo->password_clear;
						$testcrypt = $model->generateEncryptedPassword($slaveuserinfo);
						$check = ($testcrypt == $slaveuserinfo->password);
           			} else {
						$testcrypt = $model->generateEncryptedPassword($userinfo);
						$check = ($testcrypt == $userinfo->password);
            		}
					if ($check){
						//found a match
				        $response->debug[] = $auth_model->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('SUCCESS');
						$response->status = JAUTHENTICATE_STATUS_SUCCESS;
						$response->email = $userinfo->email;
						$response->fullname = $userinfo->name;
						$response->error_message = '';
						$response->userinfo = $userinfo;

						//update the password format to what the master expects
						$status = array();
						$status['debug'] = array();
						$status['error'] = array();
						$JFusionMaster =& JFusionFactory::getUser($master->name);
						$JFusionMaster->updatePassword($userinfo, $userinfo, $status);
						if (!empty($status['error'])) {
					        $response->debug[] = $auth_model->name . ' ' . JText::_('PASSWORD') . ' ' .JText::_('UPDATE') . ' ' . JText::_('ERROR') . ': ' .$status['error'];
							JFusionFunction::raiseWarning($master->name . ' ' .JText::_('PASSWORD') . ' ' . JText::_('UPDATE'), $status['error'],1);
						} else {
					        $response->debug[] = $auth_model->name . ' ' . JText::_('PASSWORD') . ' ' .JText::_('UPDATE') . ' ' . JText::_('SUCCESS');
						}

						$result = true;
						return $result;
					} else {
						if(isset($options['show_unsensored'])){
					        $response->debug[] = $auth_model->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('INVALID') . ' ' . $testcrypt. ' vs ' .$userinfo->password;					
						} else {
					        $response->debug[] = $auth_model->name . ' ' . JText::_('PASSWORD') . ' ' . JText::_('ENCRYPTION') . ' ' . JText::_('CHECK') . ': ' . JText::_('INVALID');				
						}
					}
				}

				//let's do one last ditch effort with Joomla's plugin to try and prevent a lock out
				if($master->name!='joomla_int' && !in_array('joomla_int',$auth_models) && $mainframe->isAdmin()) {
					$JAuth = JPATH_PLUGINS.DS.'authentication'.DS.'joomla.php';
					if(file_exists($JAuth)) {
						require_once($JAuth);
						plgAuthenticationJoomla::onAuthenticate($credentials, $options, $response);
						$response->debug[] = JText::_('JOOMLA_AUTH_PLUGIN_USED_JFUSION_FAILED');
					}
				}

				if(isset($response->status) && $response->status!=JAUTHENTICATE_STATUS_SUCCESS) {
					//no matching password found
					$response->status = JAUTHENTICATE_STATUS_FAILURE;
					$response->error_message = JText::_('Invalid password');
				}
			} else {
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = JText::_('USER_NOT_EXIST');
			}
		} elseif($mainframe->isAdmin()) {
			//we have to call the main Joomla plugin as we have no master
			$JAuth = JPATH_PLUGINS.DS.'authentication'.DS.'joomla.php';
			if(file_exists($JAuth)) {
				require_once($JAuth);
				plgAuthenticationJoomla::onAuthenticate($credentials, $options, $response);
				$response->debug[] = JText::_('JOOMLA_AUTH_PLUGIN_USED_NO_MASTER');
			}
		}
	}
}