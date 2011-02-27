<?php
/**
 * @version		$Id: GoogleDocs.php 9764 2007-12-30 07:48:11Z ircmaxell $
 * @package		plugins
 * @copyright	Copyright (C) 2008 soeren. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Editor GoogleDocs buton
 *
 * @author soeren
 * @package Editors-xtd
 */
class plgButtonGoogleDocs extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgButtonGoogleDocs(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * GoogleDocs button
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		global $mainframe;

		$doc 		=& JFactory::getDocument();
		$appname = $mainframe->getName();
		$apppath = $appname == 'site' ? '' : '../';
		$link = $apppath . 'plugins/editors-xtd/googledocs_form.php?app='.$appname.'&amp;e_name='.$name;

		JHTML::_('behavior.modal');
		$doc->addStyleDeclaration('
	.button2-left .googledocs {
background:transparent url('.JURI::base() .$apppath. 'plugins/editors-xtd/j_button2_googledocs.png) no-repeat scroll 100% 0pt;
}');
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text','GoogleDocs');
		$button->set('name', 'googledocs');
		$button->set('options', "{handler: 'iframe', size: {x: 500, y: 320}}");
		
		return $button;
	}
}
?>