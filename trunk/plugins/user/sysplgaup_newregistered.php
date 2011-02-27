<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2010 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgUserSysplgaup_newregistered extends JPlugin {

	function plgUsersysplgaup_newregistered(& $subject, $config)
	{
		parent::__construct($subject, $config);
		
	}
	
	function onAfterStoreUser($user, $isnew, $succes, $msg) {
		
		if ( $isnew ) {
		
			JPlugin::loadLanguage( 'com_alphauserpoints' );
			
			$jnow		=& JFactory::getDate();		
			$now		= $jnow->toMySQL();
						
			// Get plugin info
			$plugin =& JPluginHelper::getPlugin('user', 'sysplgaup_newregistered');
			$pluginParams = new JParameter( $plugin->params );
			
			// AUPRR = AlphaUserPoints Register by Referrer, AUPRS AlphaUserPoints Register by Self
			
			// get params definitions
			$params =& JComponentHelper::getParams( 'com_alphauserpoints' );		
			
			$prefixSelfRegister = $params->get('prefix_selfregister');
			$prefixReferralRegister = $params->get('prefix_referralregister');
		
			$referrerid = trim(@$_SESSION['referrerid']);
			unset($_SESSION['referrerid']);
		
			$db	   =& JFactory::getDBO();
			$query = "SELECT * FROM #__alpha_userpoints_rules WHERE `plugin_function`='sysplgaup_newregistered' AND `published`='1'";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			
			$prefixNewReferreid = ( $referrerid!='' ) ? strtoupper($prefixReferralRegister) : strtoupper($prefixSelfRegister); 
	
			// if rule enabled
			if ( $result ) {			
				
				if ( !$params->get('referralIDtype') ) {
					$newreferreid = strtoupper(uniqid ( $prefixNewReferreid, false ));	
				} else $newreferreid = $prefixNewReferreid . strtoupper($user['username']);
				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');
				
				$row =& JTable::getInstance('userspoints');
				// insert this new user into alphauserpoints table
			    $row->id			= NULL;
				$row->userid		= $user['id'];
			    $row->referreid		= $newreferreid;
			    $row->points		= $result[0]->points;
			    $row->max_points	= 0;
				//$row->last_update	= $user['registerDate'];
				$row->last_update	= $now;
			    $row->referraluser	= $referrerid;
				
				if (!$row->store()) {
					JError::raiseError(500, $row->getError());
				}				
				
				// save new points into alphauserpoints table details
				$row2 =& JTable::getInstance('userspointsdetails');
			    $row2->id				= NULL;
			    $row2->referreid		= $newreferreid;
			    $row2->points			= $result[0]->points;
				//$row2->insert_date		= $user['registerDate'];
				$row2->insert_date		= $now;
			    $row2->expire_date 		= $result[0]->rule_expire;
			    $row2->status			= $result[0]->autoapproved;
				$row2->rule				= $result[0]->id;
			    $row2->approved			= $result[0]->autoapproved;
				$row2->datareference	= JText::_( 'AUP_WELCOME' );				
			  			
				if (!$row2->store()) {
					JError::raiseError(500, $row2->getError());
				}				
				
				if ( $referrerid ) {
					$data = htmlspecialchars( $user['name'], ENT_QUOTES, 'UTF-8') . " (" . $user['username'] . ") ";
					$data = sprintf ( JText::_('AUP_X_HASJOINEDTHEWEBSITE'), $data );
					$this->sysplgaup_invitewithsuccess( $referrerid, $data );
				}
				
				return true;
				
			} else return false;						
		}
	}
	
	function onAfterDeleteUser($user, $succes, $msg) {

		$db	   =& JFactory::getDBO();
		
		$query = "SELECT `id`, `referreid`, `referraluser` FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$result = $db->loadObject();
		$referreid = $result->referreid;
		$referraluser = $result->referraluser;

		$query = "DELETE FROM #__alpha_userpoints WHERE `userid`='".$user['id']."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_details WHERE `referreid`='".$referreid."'";
		$db->setQuery( $query );
		$db->query();
		
		$query = "DELETE FROM #__alpha_userpoints_medals WHERE `rid`='".$result->id."'";
		$db->setQuery( $query );
		$db->query();
		
		// if the user has been a referral user
		$query = "UPDATE #__alpha_userpoints SET referraluser='' WHERE referraluser='".$referreid."'";
		$db->setQuery($query);
		$db->query();
		
		// recount referrees for the referral user
		$query = "UPDATE #__alpha_userpoints SET referrees=referrees-1 WHERE referreid='".$referraluser."'";
		$db->setQuery($query);
		$db->query();
		
	}

	
	function sysplgaup_invitewithsuccess( $referrerid, $data ) {
	
		$ip = $_SERVER["REMOTE_ADDR"];
		
		/*******************************
		*    FOR TEST PURPOSE ONLY     *		
		* uncomment the following line *
		********************************/
		// $ip = '';		
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
		AlphaUserPointsHelper::userpoints( 'sysplgaup_invitewithsuccess', $referrerid, 0, $ip, $data );

	}
	
	function onLoginUser($user, $options)
	{
		$app = JFactory::getApplication();
		
		$db	   =& JFactory::getDBO();
		
		jimport('joomla.user.helper');

		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
		}
		
		if ($instance->get('block') == 0) {
			require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
			// start the user session for AlphaUserpoints
			AlphaUserPointsHelper::getReferreid( intval($instance->get('id')) );
						
			if( $app->isSite() ){
			
				// load language component
				JPlugin::loadLanguage( 'com_alphauserpoints' );
				
				// *** Inactive user rule (available only on frontend) must be in first position of activities on login to check last_update of points ***
				$inactive_user_rule = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_inactiveuser' );
				//if ( $inactive_user_rule[0]->published ) {
				if ( $inactive_user_rule ) {
					$removepoints = abs($inactive_user_rule[0]->points);					
					$query = "SELECT (TO_DAYS(NOW()) - TO_DAYS(last_update)) FROM #__alpha_userpoints WHERE userid='".intval($instance->get('id'))."'";
					$db->setQuery( $query );
					$num_days = $db->loadResult();
					if ( intval($num_days) > intval($inactive_user_rule[0]->content_items) ) {
						$userinfo = AlphaUserPointsHelper::getUserInfo ( '', intval($instance->get('id')));
						$currentpoints = $userinfo->points;
						$futuretotal = $currentpoints + (($removepoints)*(-1));
						if ( $futuretotal < 0 ) {
							$removepoints = $currentpoints;
						}
						$removepoints = $removepoints*(-1);						
						$keyreference = sprintf ( JText::_('AUP_X_DAYS_WITHOUT_ACTIVITY'), intval($num_days) );
						AlphaUserPointsHelper::userpoints( 'sysplgaup_inactiveuser', '', 0, '', $keyreference, $removepoints );
					}
				}
								
				// *** Daily login rule available only on frontend ***
				$keyreference = date("Y-m-d");
				AlphaUserPointsHelper::userpoints( 'sysplgaup_dailylogin','', 0, $keyreference, $keyreference );
				
				// *** Happy birthday rule available only on frontend ***
				$birthday_rule = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_happybirthday' );
				// check if birthdate of user is setting (!= 0000-00-00)
				$query = "SELECT id FROM #__alpha_userpoints WHERE userid='".intval($instance->get('id'))."' AND birthdate!='0000-00-00'";
				$db->setQuery( $query );
				$birthdate = $db->loadResult();

				if ( $birthday_rule[0]->published && $birthdate ) {
					$year = date('Y');	
					// check if birthday today
					$query = "SELECT id FROM #__alpha_userpoints WHERE userid='".intval($instance->get('id'))."' AND DATE_FORMAT(birthdate, '%-%m-%d')=DATE_FORMAT(NOW(), '%-%m-%d');";
					$db->setQuery( $query );
					$happybirthday = $db->loadResult();
					if ( $happybirthday ) {
						AlphaUserPointsHelper::userpoints ( 'sysplgaup_happybirthday', '', 0, $year );
					}
				}
								
				// check raffle subscription to showing a reminder message
				
				// check first if rule for raffle is enabled
				$result = AlphaUserPointsHelper::checkRuleEnabled( 'sysplgaup_raffle', 1 );
				if ( $result ) {				
					$resultCurrentRaffle = $this->checkIfCurrentRaffleSubscription(intval($instance->get('id')));
					if ($resultCurrentRaffle=='stillRegistered') {
						$messageAvailable = JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE');
						if ( $messageAvailable!='' ) {
							$messageRaffle = sprintf ( JText::_('AUP_YOU_ARE_STILL_NOT_REGISTERED_FOR_RAFFLE'), $user['username'] );
							$app->enqueueMessage( $messageRaffle );
						}		
					}				
				}
			}
			
			//return true;
		}		
		
	}
	
	function onLogoutUser($user, $options = array()) {
		//Make sure we're a valid user first
		if($user['id'] == 0) return true;

		unset($_SESSION['referrerid']);
		return true;
	}
	
	
	function checkIfCurrentRaffleSubscription($userid) {
	
		$db	   =& JFactory::getDBO();
		
		$jnow		=& JFactory::getDate();		
		$now		= $jnow->toMySQL();		
		
		$query = "SELECT id FROM #__alpha_userpoints_raffle WHERE published='1' AND inscription='1' AND raffledate>'$now' AND raffledate!='0000-00-00 00:00:00' AND winner1='' AND winner2='' AND winner3='' LIMIT 1";
		$db->setQuery( $query );
		$nextraffle = $db->loadResult();
		
		if ( $nextraffle ) {
			// check if already subscription
			$query = "SELECT COUNT(*) FROM #__alpha_userpoints_raffle_inscriptions WHERE userid='$userid' AND raffleid='$nextraffle'";
			$db->setQuery( $query );
			$alreadySubscription = $db->loadResult();
			if ( $alreadySubscription ) {
				return 'alreadyRegistered';
			} return 'stillRegistered';
			
		} else return 'noRaffleAvailable';
	
	}
}
?>