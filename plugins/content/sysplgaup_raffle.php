<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2010 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * AlphaUserPoints Content Plugin
 *
 * @package		Joomla
 * @subpackage	AlphaUserPoints
 * @since 		1.5
 */

class plgContentsysplgaup_raffle extends JPlugin
{

	function plgContentsysplgaup_raffle( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}


	function onPrepareContent( &$article, &$params, $limitstart )
	{
		$app = JFactory::getApplication();
		
		$db	   					= & JFactory::getDBO();
		$user 					= & JFactory::getUser();
		
		$print  				= JRequest::getVar('print', '');
		$format 				= JRequest::getVar('format', '');
		
		$aupraffleid 			= JRequest::getVar('aupraffleid', 0, 'POST', 'int');		
		$aupraffleuserid 		= JRequest::getVar('aupraffleuserid', 0, 'POST', 'int');
		$auprafflepoints		= JRequest::getVar('auprafflepoints', 0, 'POST', 'int');
		$auprafflepointsremove  = JRequest::getVar('auprafflepointsremove', 0, 'POST', 'int');
		$multipleentries  		= JRequest::getVar('multipleentries', 0, 'POST', 'int');		
		
		$inscription 			= 0;
		$pointstoparticipate 	= 0;
		$removepointstoparticipate =  0;
	
		//if ( !$user->id || $print || $format=='pdf' ) {
		if ( $print || $format=='pdf' ) {
			$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
			return;
		}
		
		if ($app->isAdmin()) return;
		
		JPlugin::loadLanguage( 'com_alphauserpoints' );		
		
		$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
		require_once ($api_AUP);		
		
		if ( preg_match('#{AUP::RAFFLE=(.*)}#Uis', $article->text, $m) && !$aupraffleid ) // form not sent
		{
			$raffleid = $m[1];
			
			if ( $raffleid=='ID' ) {
				// sample
				return;
			}
			
			$query = "SELECT * FROM #__alpha_userpoints_raffle WHERE `id`='$raffleid' AND `published`='1'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();			
			if ( $result ) 
			{
				$inscription = $result[0]->inscription;
				$pointstoparticipate = $result[0]->pointstoparticipate;
				$removepointstoparticipate = $result[0]->removepointstoparticipate;
				$multipleentries  = $result[0]->multipleentries;
				$alreadyProceeded = $result[0]->winner1;
			} 
			
			// You can choose number subscriptions members
			//$query = "SELECT COUNT(DISTINCT userid) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$raffleid'";
			
			// You can choose number of tickets sold!
			$query = "SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$raffleid'";
			$db->setQuery( $query );
			$num_subscription = $db->loadResult();			
			$article->text .= "\n<p>".JText::_('AUP_NUMBER_SUBSCRIPTION_CURRENT_RAFFLE'). " " . $num_subscription . "</p>\n";
			
			if ( $inscription && $user->id && $alreadyProceeded==0) 
			{	
			
				if ( !$multipleentries ) {										
					$query = "SELECT userid FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='$user->id' AND `raffleid`='$raffleid'";
					$db->setQuery( $query );
					$userid = $db->loadResult();					
				} else $userid=0;				
				
				if ( !$userid )
				{					
					$registrationForm = "\n<form action=\"\" method=\"post\" name=\"RaffleForm\">\n"
										. "<input type=\"hidden\" name=\"aupraffleid\" id=\"aupraffleid\" value=\"".$raffleid."\" />\n"
										. "<input type=\"hidden\" name=\"aupraffleuserid\" id=\"aupraffleuserid\" value=\"".$user->id."\" />\n"
										. "<input type=\"hidden\" name=\"auprafflepoints\" id=\"auprafflepoints\" value=\"".$pointstoparticipate."\" />\n"
										. "<input type=\"hidden\" name=\"auprafflepointsremove\" id=\"auprafflepointsremove\" value=\"".$removepointstoparticipate."\" />\n"
										. "<input type=\"hidden\" name=\"multipleentries\" id=\"multipleentries\" value=\"".$multipleentries."\" />\n"										
										. "<input class=\"button\" type=\"submit\" name=\"Submit\" value=\"".JText::_('AUP_SIGNUP_FOR_THIS_RAFFLE_NOW')."\" />\n"
										."</form>\n";
		
					if ( $pointstoparticipate ) 
					{
						$referreid = AlphaUserPointsHelper::getAnyUserReferreID( $user->id );
						$currentpoints = AlphaUserPointsHelper::getCurrentTotalPoints ( $referreid );
						if ( $currentpoints>=$pointstoparticipate )						
						{
							$article->text .= $registrationForm;
						}
						else
						{
							$article->text .= "\n<p>".JText::_('AUP_YOUDONOTHAVEENOUGHPOINTSTOPERFORMTHISOPERATION')."</p>\n";
						}
					}
					else
					{					
						$article->text .= $registrationForm;
					}									
				}
				else
				{
					$article->text .= "\n<p>".JText::_('AUP_ALREADY_REGISTERED_FOR_THIS_RAFFLE')."</p>\n";
				}
			
			}
			elseif ( $inscription && $user->id && $alreadyProceeded>0)
			{
				$article->text .= "\n<p>".JText::_('AUP_DRAW_HAS_BEEN_MADE_YOU_CANT_REGISTER')."</p>\n";
				$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
				return;				
			} else 	{
				$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
				return;
			}			
		} 
		elseif ( preg_match('#{AUP::RAFFLE=(.*)}#Uis', $article->text, $m) && $aupraffleid && $aupraffleuserid )  
		{			
			if ( !$multipleentries ) {			
				$query = "SELECT userid FROM #__alpha_userpoints_raffle_inscriptions WHERE `userid`='$aupraffleuserid' AND `raffleid`='$aupraffleid'";
				$db->setQuery( $query );
				$alreadyregister = $db->loadResult();				
			} else $alreadyregister=0;
			
			if ( !$alreadyregister )
			{
				//Save registration				
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_alphauserpoints'.DS.'tables');
				$row =& JTable::getInstance('raffle_inscriptions');
				$row->id				= NULL;
				$row->raffleid			= $aupraffleid;
				$row->userid			= $aupraffleuserid;
				if ( !$row->store() )
				{
					JError::raiseError(500, $row->getError());
				} 
				else 
				{
					// remove points if necessary
					if ( $auprafflepointsremove && $auprafflepoints ) {
						AlphaUserPointsHelper::newpoints( 'sysplgaup_raffle', '', '', JText::_('AUP_LABEL_REGISTRATION_RAFFLE'), (abs($auprafflepoints)*(-1)) );
					}					
					$app->enqueueMessage( JText::_('AUP_CONFIRM_RAFFLE_REGISTRATION') );
					
					// You can choose number subscriptions members
					//$query = "SELECT COUNT(DISTINCT aupraffleuserid) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid'";			
					// You can choose number of tickets sold!
					$query = "SELECT COUNT(id) FROM #__alpha_userpoints_raffle_inscriptions WHERE `raffleid`='$aupraffleid'";
					$db->setQuery( $query );
					$num_subscription = $db->loadResult();		
					$article->text .= "\n<p>".JText::_('AUP_NUMBER_SUBSCRIPTION_CURRENT_RAFFLE'). " " . $num_subscription . "</p>\n";

					$article->text .= "\n<p>".JText::_('AUP_YOUR_SUBSCRIPTION_HAS_BEEN_REGISTERED')."</p>\n";
					// check new total points to play again
					$referreid = AlphaUserPointsHelper::getAnyUserReferreID( $user->id );
					$currentpoints = AlphaUserPointsHelper::getCurrentTotalPoints ( $referreid );
					if ( $currentpoints>=$auprafflepoints && $multipleentries )						
					{
						$uri =& JURI::getInstance();
						$url = $uri->toString();
						$article->text .= "\n<p><a href=\"$url\">".JText::_('AUP_SUBSCRIBE_AGAIN')."</a></p>\n";
					}

				}			
			} else $article->text .= "\n<p>".JText::_('AUP_ALREADY_REGISTERED_FOR_THIS_RAFFLE')."</p>\n";
		
		}

		//  article text updated
		$article->text = preg_replace( " |{AUP::RAFFLE=(.*)}| ", "", $article->text );
	}
	
}
?>