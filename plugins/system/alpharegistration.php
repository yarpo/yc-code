<?php
/**
 * @version		2.0.9 alpharegistration $
 * @package		alpharegistration
 * @copyright	Copyright © 2009 - Bernard Gilly - All rights reserved.
 * @license		GNU/GPL
 * @author		Bernard Gilly
 * @author mail	contact@alphaplug.com
 * @website		www.alphaplug.com
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
class plgSystemAlphaRegistration extends JPlugin
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
	function plgSystemAlphaRegistration(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterRender()
	{
		global $mainframe;
		
		$user 	=& JFactory::getUser();
		
		$option = JRequest::getCmd('option', '', 'default', 'string');		
		$task   = JRequest::getCmd('task', '', 'default',   'string');
		$view	= JRequest::getVar('view', '', 'default',   'string');
		
		$itemid = "";
		
		if( $mainframe->isAdmin() || $user->id || $option!='com_user' ) return;
		
		if ( $option=='com_user' && ($task=='register' || $view=='register') ) {
			// load AlphaRegistration params
			$arg_params = &JComponentHelper::getParams( 'com_alpharegistration' );
			
			if ( $user->get('guest') && $arg_params->get( 'enabledARG' ) ) {
			
				// force Itemid
				if ( $arg_params->get( 'itemid' ) ) $itemid = '&Itemid=' . $arg_params->get( 'itemid' ) ;
				
				$newlink = JRoute::_('index.php?option=com_alpharegistration&task=register'.$itemid);
				//$newlink = 'index.php?option=com_alpharegistration&task=register'.$itemid;
				$mainframe->redirect($newlink);
	
			}
		}
	}

}
?>