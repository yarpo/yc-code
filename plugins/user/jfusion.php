<?php
/**
* @package JFusion
* @subpackage Plugin_User
* @author JFusion development team
* @copyright Copyright (C) 2008 JFusion. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
* Load the JFusion framework
*/
jimport('joomla.plugin.plugin');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.factory.php');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php');


/**
* JFusion User class
* @package JFusion
*/
 class plgUserJfusion extends JPlugin
{
	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for plugins
	* because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	* This causes problems with cross-referencing necessary for the observer design pattern.
	*
	* @param object $subject The object to observe
	* @param array $config An array that holds the plugin configuration
	* @since 1.5
	*/
	function plgUserJfusion(& $subject, $config)
	{
		parent::__construct($subject, $config);
		//load the language
		$this->loadLanguage('com_jfusion', JPATH_BASE);
	}

	/**
	* Remove all sessions for the user name
	*
	* Method is called after user data is deleted from the database
	*
	* @param array holds the user data
	* @param boolean true if user was succesfully stored in the database
	* @param string message
	*/
	function onAfterDeleteUser($user, $succes, $msg)
	{
		if (!$succes) {
			$result = false;
			return $result;
		}
      
        //create an array to store the debug info
        $debug_info = array();
		
		//convert the user array into a user object
		$userinfo = (object) $user;

		//delete the master user if it is not Joomla
		$master = JFusionFunction::getMaster();

		if($master->name != 'joomla_int'){
			$params =& JFusionFactory::getParams($master->name);
			$deleteEnabled = $params->get('allow_delete_users',0);
			$JFusionMaster =& JFusionFactory::getUser($master->name);
			$MasterUser = $JFusionMaster->getUser($userinfo);

			if(!empty($MasterUser) && $deleteEnabled) {
				$status = $JFusionMaster->deleteUser($MasterUser);
				if(!empty($status['error'])){
					$debug_info[$master->name . ' ' . JText::_('ERROR')] = $status['error'];					
				}
				$debug_info[$master->name] = $status['debug'];								
			} elseif($deleteEnabled) {
				$debug_info[$master->name] = JText::_('NO_USER_DATA_FOUND');
			} else {
				$debug_info[$master->name] = JText::_('DELETE_DISABLED');
			}
		}

		//delete the user in the slave plugins
		$slaves = JFusionFunction::getPlugins();
		foreach($slaves as $slave) {
			$params =& JFusionFactory::getParams($slave->name);
			$deleteEnabled = $params->get('allow_delete_users',0);

			$JFusionSlave =& JFusionFactory::getUser($slave->name);
			$SlaveUser = $JFusionSlave->getUser($userinfo);

			if(!empty($SlaveUser) && $deleteEnabled) {
				$status = $JFusionSlave->deleteUser($SlaveUser);
				if(!empty($status['error'])){
					$debug_info[$slave->name . ' ' . JText::_('ERROR')] = $status['error'];					
				}
				$debug_info[$slave->name] = $status['debug'];								
			} elseif($deleteEnabled) {
				$debug_info[$slave->name] = JText::_('NO_USER_DATA_FOUND');
			} else {
				$debug_info[$slave->name] = JText::_('DELETE'). ' ' . JText::_('DISABLED');
			}
		}

		//remove userlookup data
		JFusionFunction::removeUser($userinfo);

		//delete any sessions that the user could have active
		$db =& JFactory::getDBO();
		$db->setQuery('DELETE FROM #__session WHERE userid = '.$db->Quote($user['id']));
		$db->Query();		

		//return output if allowed
		$isAdministrator = JFusionFunction::isAdministrator();
		if($isAdministrator === true){
			JFusionFunction::raiseWarning('' ,$debug_info,1);			
		}
		
		$result = true;
		return $result;
	}

	/**
	* This method should handle any login logic and report back to the subject
	*
	* @access public
	* @param array holds the user data
	* @param array array holding options (remember, autoregister, group)
	* @return boolean True on success
	* @since 1.5
	*/
	function onLoginUser($user, &$options)
	{
		//prevent any output by the plugins (this could prevent cookies from being passed to the header)
		ob_start();

		jimport('joomla.user.helper');
		global $JFusionActive, $mainframe;
		$JFusionActive = true;
		$options['debug'] = array();
		$options['debug']['init'] = array();

		//determine if overwrites are allowed
		$isAdministrator = JFusionFunction::isAdministrator();		
   		if (!empty($options['overwrite']) && $isAdministrator === true){
   			$overwrite = 1;
   		} else {
   			$overwrite = 0;
   		}

		//allow for the detection of external mods to exclude jfusion plugins
		global $JFusionActivePlugin;

		//get the JFusion master
		$master = JFusionFunction::getMaster();

		//if we are in the admin and no master is selected, make joomla_int master to prevent lockouts
		if(empty($master) && $mainframe->isAdmin()) {
			$master = new stdClass();
			$master->name = 'joomla_int';
			$master->joomlaAuth = true;
		}

		if(!empty($master)) {
			$JFusionMaster =& JFusionFactory::getUser($master->name);
			//check to see if userinfo is already present
			if(!empty($user['userinfo'])){
				//the jfusion auth plugin is enabled
				$options['debug']['init'][]= JText::_('USING_JFUSION_AUTH');
				$userinfo = $user['userinfo'];
			} else {
				//other auth plugin enabled get the userinfo again
				//temp userinfo to see if the user exists in the master
				$auth_userinfo = new stdClass();
				$auth_userinfo->username = $user['username'];
				$auth_userinfo->email = $user['email'];
				$auth_userinfo->password_clear = $user['password'];
				$auth_userinfo->name = $user['fullname'];

				//get the userinfo for real
				$userinfo = $JFusionMaster->getUser($auth_userinfo);

				if(isset($master->joomlaAuth)) {
					$options['debug']['init'][]= JText::_('USING_JOOMLA_AUTH') ;
				} else {
					$options['debug']['init'][]= JText::_('USING_OTHER_AUTH');
				}

				if(empty($userinfo)){
					//should be auto-create users?
					$params =& JFusionFactory::getParams('joomla_int');
					$autoregister = $params->get('autoregister',0);
					if($autoregister == 1){
						$options['debug']['init'][]= JText::_('CREATING_MASTER_USER');
						$status = array();
						$status['debug'] = array();
						$status['error'] = array();

						//try to create a Master user
						$JFusionMaster->createUser($auth_userinfo, $status);
						if(empty($status['error'])){
							//success
							$userinfo = $status['userinfo'];
							$options['debug']['init'][]= JText::_('MASTER') . ' ' .JText::_('USER') . ' ' .JText::_('CREATE') . ' ' .JText::_('SUCCESS');
						} else {
							//could not create user
							ob_end_clean();
							$options['debug']['init'][] = $master->name . ' ' .JText::_('USER') . ' ' .JText::_('CREATE') .' ' .JText::_('ERROR')  .' '. $status['error'];
							JFusionFunction::raiseWarning($master->name . ' ' .JText::_('USER') . ' ' .JText::_('CREATE'), $status['error'],1);
							$success = false;
							return $success;
						}
					} else {
						//return an error
						$options['debug']['init'][] = JText::_('COULD_NOT_FIND_USER');
						ob_end_clean();
						$success = false;
						return $success;
					}
				}
			}

			//apply the cleartext password to the user object
			$userinfo->password_clear = $user['password'];

			// See if the user has been blocked or is not activated
			if (!empty($userinfo->block) || !empty($userinfo->activation)) {
				//make sure the block is also applied in slave softwares
				$slaves = JFusionFunction::getSlaves();
				foreach($slaves as $slave) {
					$JFusionSlave =& JFusionFactory::getUser($slave->name);
					$SlaveUser = $JFusionSlave->updateUser($userinfo,$overwrite);
					if (!empty($SlaveUser['error'])) {
						$options['debug'][$slave->name.' ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('ERROR')] = $SlaveUser['error'];
					} 
					$options['debug'][$slave->name.' ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('DEBUG')] = $SlaveUser['debug'];
					$options['debug'][$slave->name.' ' . JText::_('USERINFO')] = $SlaveUser['userinfo'];					
				}

				if (!empty($userinfo->block)) {
					$options['debug']['error'][] = JText::_('FUSION_BLOCKED_USER');
					JError::raiseWarning('500', JText::_('FUSION_BLOCKED_USER'));
					//hide the default Joomla login failure message
					JError::setErrorHandling(E_WARNING, 'ignore');
					ob_end_clean();
					$success = false;
					return $success;
				} else {
					$options['debug']['error'][] = JText::_('FUSION_INACTIVE_USER');
					JError::raiseWarning('500', JText::_('FUSION_INACTIVE_USER'));
					//hide the default Joomla login failure message
					JError::setErrorHandling(E_WARNING, 'ignore');
					ob_end_clean();
					$success = false;
					return $success;
				}
			 }

			//setup the master session if
			//a) The master is not joomla_int and the user is logging into Joomla's frontend only
			//b) The master is joomla_int and the user is logging into either Joomla's frontend or backend
			if ($JFusionActivePlugin != $master->name && (!isset($options['group']) || $master->name=='joomla_int')){
				$MasterSession = $JFusionMaster->createSession($userinfo, $options);
				if (!empty($MasterSession['error'])) {
					$options['debug'][$master->name .' ' . JText::_('SESSION') . ' ' . JText::_('DEBUG')] = $MasterSession['debug'];
					$options['debug'][$master->name .' ' . JText::_('SESSION') . ' ' . JText::_('ERROR')] = $MasterSession['error'];
					//report the error back
					JFusionFunction::raiseWarning($master->name .' ' .JText::_('SESSION').' ' .JText::_('CREATE'), $MasterSession['error'],1);
					if ($master->name == 'joomla_int'){
						//we can not tolerate Joomla session failures
						ob_end_clean();
						//hide the default Joomla login failure message
						JError::setErrorHandling(E_WARNING, 'ignore');
						$success = false;
						return $success;
					}
				} else {
					$options['debug'][$master->name . ' ' . JText::_('SESSION')] = $MasterSession['debug'];
				}
			}

			//check to see if we need to setup a Joomla session
			if ($master->name != 'joomla_int'){

				//setup the Joomla user
				$JFusionJoomla =& JFusionFactory::getUser('joomla_int');
				$JoomlaUser = $JFusionJoomla->updateUser($userinfo,$overwrite);
				if (!empty($JoomlaUser['error'])) {
					//no Joomla user could be created, fatal error
					$options['debug']['joomla_int ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('DEBUG')] = $JoomlaUser['debug'];
					$options['debug']['joomla_int ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('ERROR')] = $JoomlaUser['error'];
					JFusionFunction::raiseWarning('joomla_int: '.' ' .JText::_('USER').' ' .JText::_('UPDATE'), $JoomlaUser['error'],1);
					//hide the default Joomla login failure message
					JError::setErrorHandling(E_WARNING, 'ignore');
					ob_end_clean();
					$success = false;
					return $success;
				} else {
					$options['debug']['joomla_int ' . JText::_('USER').' ' .JText::_('UPDATE')] = $JoomlaUser['debug'];
					$options['debug']['joomla_int ' . JText::_('USER').' ' .JText::_('DETAILS')] = $JoomlaUser['userinfo'];
				}

				//create a Joomla session
				if($JFusionActivePlugin != 'joomla_int'){
					$JoomlaSession = $JFusionJoomla->createSession($JoomlaUser['userinfo'], $options);
					if (!empty($JoomlaSession['error'])) {
						$options['debug']['joomla_int ' . JText::_('SESSION') . ' ' . JText::_('DEBUG')] = $JoomlaSession['debug'];
						$options['debug']['joomla_int ' . JText::_('SESSION') . ' ' . JText::_('ERROR')] = $JoomlaSession['error'];
						//no Joomla session could be created -> deny login
						JFusionFunction::raiseWarning('joomla_int ' .' ' .JText::_('SESSION') .' ' .JText::_('CREATE'), $JoomlaSession ['error'],1);
						//hide the default Joomla login failure message
						JError::setErrorHandling(E_WARNING, 'ignore');
						ob_end_clean();
						$success = false;
						return $success;
					} else {
						$options['debug']['joomla_int ' .JText::_('SESSION')] = $JoomlaSession['debug'];
					}
				}
			} else {
				//joomla already setup, we can copy its details from the master
				$JFusionJoomla = $JFusionMaster;
				$JoomlaUser = array( 'userinfo' => $userinfo, 'error' => '');
			}
			//allow for joomlaid retrieval in the loginchecker
			$options['joomlaid']=$JoomlaUser['userinfo']->userid;

			if ($master->name != 'joomla_int') {
				JFusionFunction::updateLookup($userinfo, $JoomlaUser['userinfo']->userid, $master->name);
			}

			//setup the other slave JFusion plugins
			$slaves = JFusionFunction::getPlugins();
			foreach($slaves as $slave) {
				$JFusionSlave =& JFusionFactory::getUser($slave->name);
				$SlaveUser = $JFusionSlave->updateUser($userinfo,$overwrite);
				if (!empty($SlaveUser['error'])) {
					$options['debug'][ $slave->name . ' ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('DEBUG')] = $SlaveUser['debug'];
					$options['debug'][ $slave->name . ' ' . JText::_('USER').' ' .JText::_('UPDATE') . ' ' . JText::_('ERROR')] = $SlaveUser['error'];
					JFusionFunction::raiseWarning($slave->name . ' ' . JText::_('USER') .' ' .JText::_('UPDATE') , $SlaveUser['error'],1);
				} else {
					$options['debug'][ $slave->name . ' ' . JText::_('USER').' ' .JText::_('UPDATE')] = $SlaveUser['debug'];
					$options['debug'][ $slave->name . ' ' . JText::_('USER').' ' .JText::_('DETAILS')] = $SlaveUser['userinfo'];

					//apply the cleartext password to the user object
					$SlaveUser['userinfo']->password_clear = $user['password'];

					JFusionFunction::updateLookup($SlaveUser['userinfo'], $JoomlaUser['userinfo']->userid, $slave->name);

					if (!isset($options['group']) && $slave->dual_login == 1 && $JFusionActivePlugin != $slave->name) {
						$SlaveSession = $JFusionSlave->createSession($SlaveUser['userinfo'], $options);
						if (!empty($SlaveSession['error'])) {
							$options['debug'][ $slave->name . ' ' . JText::_('SESSION').' ' .JText::_('DEBUG')] = $SlaveSession['debug'];
							$options['debug'][ $slave->name . ' ' . JText::_('SESSION').' ' .JText::_('ERROR')] = $SlaveSession['error'];
							JFusionFunction::raiseWarning($slave->name . ' ' . JText::_('SESSION') .' ' .JText::_('CREATE'), $SlaveSession['error'],1);
						} else {
							$options['debug'][ $slave->name . ' ' . JText::_('SESSION')] = $SlaveSession['debug'];
						}
					}
				}
			}
			//Clean up the joomla session table
			$conf =& JFactory::getConfig();
			$expire = ($conf->getValue('config.lifetime')) ? $conf->getValue('config.lifetime') * 60 : 900;
			$session = & JTable::getInstance('session');
			$session->purge($expire);

			ob_end_clean();
			$result = true;
			return $result;
		} else {
			ob_end_clean();
			$result = false;
			return $result;
		}
	}


	/**
	* This method should handle any logout logic and report back to the subject
	*
	* @access public
	* @param array holds the user data
	* @param array array holding options (client, ...)
	* @return object True on success
	* @since 1.5
	*/
	function onLogoutUser($user, &$options = array())
	{
		//initialise some vars
		global $JFusionActive;
		$JFusionActive = true;
		$my =& JFactory::getUser($user['id']);
		$options['debug'] = array();

		//allow for the detection of external mods to exclude jfusion plugins
		global $JFusionActivePlugin;

		//prevent any output by the plugins (this could prevent cookies from being passed to the header)
		ob_start();

		//logout from the JFusion plugins if done through frontend
		if ($options['clientid'][0] != 1) {
			//get the JFusion master
			$master = JFusionFunction::getMaster();
			if ($master->name && $master->name != 'joomla_int' && $JFusionActivePlugin != $master->name) {
				$JFusionMaster =& JFusionFactory::getUser($master->name);
				$userlookup = JFusionFunction::lookupUser($master->name, $my->get('id'));
				$options['debug']['userlookup']=$userlookup;
				$MasterUser = $JFusionMaster->getUser($userlookup);
				$options['debug']['masteruser']=$MasterUser;
				//check if a user was found
				if (!empty($MasterUser)) {
					$MasterSession = $JFusionMaster->destroySession($MasterUser, $options);
					if (!empty($MasterSession['error'])) {
						JFusionFunction::raiseWarning($master->name .' ' .JText::_('SESSION'). ' ' .JText::_('DESTROY'), $MasterSession['error']);
					}
					$options['debug'][$master->name . ' logout']=$MasterSession['debug'];
				} else {
					JFusionFunction::raiseWarning($master->name . ' ' .JText::_('LOGOUT'), JText::_('COULD_NOT_FIND_USER'),1);
				}
			}

			$slaves = JFusionFunction::getPlugins();
			foreach($slaves as $slave) {
				//check if sessions are enabled
				if ($slave->dual_login == 1 && $JFusionActivePlugin != $slave->name) {
					$JFusionSlave =& JFusionFactory::getUser($slave->name);
					$userlookup = JFusionFunction::lookupUser($slave->name, $my->get('id'));
					$SlaveUser = $JFusionSlave->getUser($userlookup);
					$options['debug'][$slave->name . ' userinfo']=$SlaveUser;
					//check if a user was found
					if (!empty($SlaveUser)) {
						$SlaveSession = $JFusionSlave->destroySession($SlaveUser, $options);
						if (!empty($SlaveSession['error'])) {
							JFusionFunction::raiseWarning($slave->name . ' ' .JText::_('SESSION'). ' ' .JText::_('DESTROY'),$SlaveSession['error'],1);
						} else {
							$options['debug'][$slave->name . ' logout']=$SlaveSession['debug'];
						}
					} else {
						JFusionFunction::raiseWarning($slave->name . ' ' .JText::_('LOGOUT'), JText::_('COULD_NOT_FIND_USER'),1);
					}
				}
			}
		}

		//destroy the joomla session itself
		if ($JFusionActivePlugin!='joomla_int'){
			$JoomlaUser =& JFusionFactory::getUser('joomla_int');
			$JoomlaUser->destroySession($user, $options);
		}


		ob_end_clean();
		$result = true;
		return $result;
	}

	function onBeforeStoreUser($olduser, $isnew) {

		global $JFusionActive;

		if (! $JFusionActive) {
			// Recover old data from user before to save it. The purpose is to provide it to the plugins if needed
			$session = & JFactory::getSession ();
			$session->set ( 'olduser', $olduser );
		}
	}

	function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		if(!$succes){
			$result = false;
			return $result;
		}

        //create an array to store the debug info
        $debug_info = array();

		//prevent any output by the plugins (this could prevent cookies from being passed to the header)
		ob_start();
		$Itemid_backup = JRequest::getInt('Itemid',0);

		global $JFusionActive;

		if (!$JFusionActive) {
			//A change has been made to a user without JFusion knowing about it
			
			//we need to make sure that group_id is in the $user array
			if(!key_exists('group_id',$user) && key_exists('gid',$user)) {
				$user['group_id'] = $user['gid'];
			}

			//convert the user array into a user object
			$JoomlaUser = (object) $user;

			//check to see if we need to update the master
			$master = JFusionFunction::getMaster();

			// Recover the old data of the user
			// This is then used to determine if the username was changed
			$session = & JFactory::getSession ();
			$JoomlaUser->olduserinfo = (object) $session->get ( 'olduser' );
			$session->clear ( 'olduser' );
			$updateUsername = (!$isnew && $JoomlaUser->olduserinfo->username != $JoomlaUser->username) ? true : false;

			//retrieve the username stored in jfusion_users if it exists
			$db =& JFactory::getDBO();
			$query = 'SELECT username FROM #__jfusion_users WHERE id = ' . (int) $JoomlaUser->id;
			$db->setQuery($query);
			$storedUsername = $db->loadResult();

			if($updateUsername) {
				//update the jfusion_user table with the new username
	           $query = 'REPLACE INTO #__jfusion_users (id, username) VALUES (' . (int) $JoomlaUser->id . ', ' . $db->Quote($JoomlaUser->username) . ')';
	           $db->setQuery($query);
	           if (!$db->query()) {
	                JError::raiseWarning(0,$db->stderr());
	           }

	           //if we had a username stored in jfusion_users, update the olduserinfo with that username before passing it into the plugins so they will find the intended user
	           if(!empty($storedUsername)) {
	           		$JoomlaUser->olduserinfo->username = $storedUsername;
	           }
			} else {
				if(!empty($JoomlaUser->original_username)) {
					//the user was created by JFusion's JFusionJplugin::createUser and we have the original username which must be used as the jfusion_user table has not been updated yet
					$JoomlaUser->username = $JoomlaUser->original_username;
				} elseif(!empty($storedUsername)) {
					//the username is not being updated but if there is a username stored in jfusion_users table, it must be used instead to prevent user duplication
	           		$JoomlaUser->username = $storedUsername;
	           }
			}

			//update the master user if not joomla_int
			if($master->name != 'joomla_int'){
				$JFusionMaster =& JFusionFactory::getUser($master->name);

				//if the username was updated, call the updateUsername function before calling updateUser
				if($updateUsername) {
					$updateUsernameStatus = array();
					$jfusion_userinfo = $JFusionMaster->getUser($JoomlaUser->olduserinfo);
					if(!empty($jfusion_userinfo)) {
						$JFusionMaster->updateUsername($JoomlaUser, $jfusion_userinfo, $updateUsernameStatus);
						if(!empty($updateUsernameStatus['error'])){
							$debug_info[$master->name . ' ' . JText::_('USERNAME'). ' '. JText::_('UPDATE'). ' ' . JText::_('ERROR')] = $updateUsernameStatus['error'];					
						}
						$debug_info[$master->name . ' ' . JText::_('USERNAME'). ' '. JText::_('UPDATE')] = $updateUsernameStatus['debug'];
					} else {
						$debug_info[$master->name] = JText::_('NO_USER_DATA_FOUND');
					}
				}

				//run the update user to ensure any other userinfo is updated as well
				$MasterUser = $JFusionMaster->updateUser($JoomlaUser, 1);
				if(!empty($MasterUser['error'])){
					$debug_info[$master->name] = $MasterUser['error'];					
				}
				$debug_info[$master->name] = $MasterUser['debug'];

				//update the jfusion_users_plugin table
				JFusionFunction::updateLookup($MasterUser['userinfo'], $JoomlaUser->id, $master->name);
			}

			//update the user details in any JFusion slaves
			$slaves = JFusionFunction::getPlugins();
			foreach($slaves as $slave) {
				$JFusionSlave =& JFusionFactory::getUser($slave->name);

				//if the username was updated, call the updateUsername function before calling updateUser
				if($updateUsername) {
					$jfusion_userinfo = $JFusionSlave->getUser($JoomlaUser->olduserinfo);
					if(!empty($jfusion_userinfo)) {
						$updateUsernameStatus = array();
						$JFusionSlave->updateUsername($JoomlaUser, $jfusion_userinfo, $updateUsernameStatus);
						if(!empty($updateUsernameStatus['error'])){
							$debug_info[$slave->name . ' ' . JText::_('USERNAME'). ' '. JText::_('UPDATE'). ' ' . JText::_('ERROR')] = $updateUsernameStatus['error'];					
						}
						$debug_info[$slave->name . ' ' . JText::_('USERNAME'). ' '. JText::_('UPDATE')] = $updateUsernameStatus['debug'];
					} else {
						$debug_info[$slave->name] = JText::_('NO_USER_DATA_FOUND');
					}
				}

				$SlaveUser = $JFusionSlave->updateUser($JoomlaUser, 1);
				if(!empty($SlaveUser['error'])){
					$debug_info[$slave->name] = $SlaveUser['error'];					
				}
				$debug_info[$slave->name] = $SlaveUser['debug'];

				//update the jfusion_users_plugin table
				JFusionFunction::updateLookup($SlaveUser['userinfo'], $JoomlaUser->id, $slave->name);
			}
		 }
		 //check to see if the Joomla database is still connnected incase the plugin messed it up
		 JFusionFunction::reconnectJoomlaDb();

		//reset the global $Itemid so that modules are not repeated
		global $Itemid;
		$Itemid = $Itemid_backup;
		//reset Itemid so that it can be obtained via getVar
		JRequest::setVar('Itemid',$Itemid_backup);

		//return output if allowed
		$isAdministrator = JFusionFunction::isAdministrator();
		if($isAdministrator === true){
			JFusionFunction::raiseWarning('' ,$debug_info,1);			
		}		
		
		 //stop output buffer
		 ob_end_clean();
	}
}