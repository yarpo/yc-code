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
 * AlphaUserPoints System Plugin
 *
 * @package		Joomla
 * @subpackage	AlphaUserPoints
 * @since 		1.5
 */
class plgSystemAlphaUserPoints extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemAlphaUserPoints(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterRender()
	{
		$app = JFactory::getApplication();
		
		// this function stores the referee if the invited user does not register immediately
		
		// No need in admin panel or AUP no exist
		if($app->isAdmin()) return;
		
		//Get ?referrer= from URL
		$referrer = JRequest::getVar('referrer', '', 'get', 'string');
		
		@session_start('alphauserpoints');
		
		// If there is no cookie, session, AND ?referrer=, then guest is self referred.  Skip code
		if(!$referrer && !isset($_COOKIE['referrerid']) && !isset($_SESSION['referrerid'])) {     
			return; 
		}
		else {			
			// If there is a ?referrer=, it is the most recent referrer.  Set it into session & cookie
			$expire=time()+60*60*24*30; //expires in 30 days
			if($referrer) {
				
				 // Set cookie
				setcookie("referrerid", $referrer, $expire );

				// Set session
				$_SESSION['referrerid'] = $referrer;
				return;
			} 
			else {
				
				// If a session is set & does not match the cookie, Set the session into the cookie.  Session is most recent referrer.
				if (isset($_SESSION['referrerid']) && ($_SESSION['referrerid'] != @$_COOKIE['referrerid'])) {

					// Set cookie
					setcookie("referrerid", $_SESSION['referrerid'], $expire);
					return;
				}
				
				// If No session is set And a cookie is set, it is an old referral.  Set cookie into a current session
				if (!isset($_SESSION['referrerid']) && isset($_COOKIE['referrerid'])) {
					
					// Set session
					$_SESSION['referrerid'] = $_COOKIE['referrerid'];
					return;              
				}
			}
		}		
	}
	
	function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		
		// No need in admin panel
		if( $app->isAdmin() ) return;
		
		$sef		= $app->getCfg('sef');
				
		$option = JRequest::getCmd('option', '');
		$task   = JRequest::getCmd('task', '');		
		$view   = JRequest::getVar('view', '');
		$link   = base64_decode ( JRequest::getVar('link', '', 'post', 'base64') );
		$id 	= JRequest::getVar('id', 0);		
		$bid 	= JRequest::getVar('bid', 0);	// banner
		$cid 	= JRequest::getVar('cid', 0);	// vote article
		
		$post	= JRequest::get( 'get' );
		
		$referreruser = JRequest::getVar('referreruser', '', 'GET', 'string');   // used for invite a friend to read
		$keyreference = JRequest::getVar('keyreference', '', 'GET', 'string');   // used for invite a friend to read		
		$datareference = JRequest::getVar('datareference', '', 'GET', 'string'); // used for invite a friend to read
			
		if ( file_exists(JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php') ) 
		{
			require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
		} else return;
		
		JPlugin::loadLanguage( 'com_alphauserpoints' );
		
		$newkeyreference = uniqid(rand(), true);
		
		@session_start('alphauserpoints');
		
		$referreid = @$_SESSION['referrerid'];
		
		switch ( $task )
		{
			case 'vote':
				// Answering a poll
				if ( $option=='com_poll' ) 
				{					
					if ( $id ) 
					{
						$db	   =& JFactory::getDBO();
						$query = "SELECT `title` FROM #__polls WHERE id='$id' AND `published`='1'";
						$db->setQuery( $query );
						$titlepoll = $db->loadResult();
						if ( $titlepoll ) 
						{
							AlphaUserPointsHelper::userpoints( 'sysplgaup_answeringpoll', '', 0, $id, $titlepoll );
						}
					}						
				}
				// vote an article		
				if ( $option=='com_content' ) 
				{								
					AlphaUserPointsHelper::userpoints( 'sysplgaup_votearticle', '', 0, $cid );
				}						
				break;
			case 'save':
				// Submit article
				if ( $option=='com_content' ) 
				{
					$title = JRequest::getVar('title', '', 'post', 'string');	
					if ( !$id && $title!='' ) 
					{											
						AlphaUserPointsHelper::userpoints( 'sysplgaup_submitarticle', '', 0, '', $title );
					}
				}
				// Submit weblink
				if ( $option=='com_weblinks' )
				{
					$jform = JRequest::getVar('jform', array(), 'post', 'array');
					$url = $jform['url'];
					if ( substr( $url, 0, 4)!='http' ) $url = 'http://'.$url;
					$urldatareference = '<a href="'.$url.'">'.$url.'</a>';
					AlphaUserPointsHelper::userpoints( 'sysplgaup_submitweblink', '', 0, '', $urldatareference );
				}
				break;
			case 'send':
				// Recommend an article : Invite a friend to read -> on send email
				if ( $option=='com_mailto' && $referreid!='' && $link!='' )
				{
					if ( AlphaUserPointsHelper::checkRuleEnabled('sysplgaup_recommend') ) 
					{
						if ( $sef ) {
							$linkcontinued = "?referreruser=$referreid&keyreference=$newkeyreference";
						} else {
							$linkcontinued = "&referreruser=$referreid&keyreference=$newkeyreference";
						}							
						$mailto = "&datareference=";
						$mailto .= JRequest::getVar('mailto', '', 'post', 'string');
						$link = $link . $linkcontinued . $mailto;
						JRequest::setVar( 'link', base64_encode($link) );
					}
				}
				break;
			default:
				// Recommend an article : friend (registered or not) read with success
				/*
				if ( $view=='article' && $referreruser!='' && $keyreference!='' && $datareference!='' )
				{
					AlphaUserPointsHelper::userpoints( 'sysplgaup_recommend', $referreruser, 0, $keyreference, $datareference );
				}
				*/
		}
	}	
	
	function onAfterRoute()
	{
		$app = JFactory::getApplication();
		
		// No need in admin panel
		if( $app->isAdmin() ) return;
		
		$option = JRequest::getCmd('option', '');
		$task   = JRequest::getCmd('task', '');
		
		$view   = JRequest::getVar('view', '');

		$bid 	= JRequest::getVar('bid', 0);	// banner		
		
		if ( $option=='com_banners' && $task=='click' && $bid ) {
			
			if ( file_exists(JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php') ) 
			{
				require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
				JPlugin::loadLanguage( 'com_alphauserpoints' );
			} else return;

			AlphaUserPointsHelper::userpoints( 'sysplgaup_clickbanner', '', 0, $bid );
		}
		
		$referreruser = JRequest::getVar('referreruser', '', 'GET', 'string');   // used for invite a friend to read
		$keyreference = JRequest::getVar('keyreference', '', 'GET', 'string');   // used for invite a friend to read		
		$datareference = JRequest::getVar('datareference', '', 'GET', 'string'); // used for invite a friend to read
		
		// Recommend an article : friend (registered or not) read with success
		if ( $view=='article' && $referreruser!='' && $keyreference!='' && $datareference!='' )
		{
			AlphaUserPointsHelper::userpoints( 'sysplgaup_recommend', $referreruser, 0, $keyreference, $datareference );
		}
		
	
	}
	
}
?>