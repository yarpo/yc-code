<?php
/**
* @version 1.0
* @package Raffle
* @copyright (C) 2010 migusbox.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* Editor button for raffle 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgButtonraffle extends JPlugin
{
	function plgButtonraffle(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	function onDisplay($name) {
		$app = JFactory::getApplication();

		$document = & JFactory::getDocument();
        $lang = & JFactory::getLanguage();
        $lang->load('plg_editors-xtd_raffle', JPATH_ADMINISTRATOR);
		$template = $app->getTemplate();
        $document->addStyleSheet( JURI::root().'plugins/editors-xtd/raffle.css', 'text/css', null, array() ); 
		JHTML::_('behavior.modal');
		$link = 'index.php?option=com_alphauserpoints&task=editorInsertRaffle&amp;tmpl=component&amp;e_name='.$name;		
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('AUP_BUTTON_PLG_TEXT'));
		$button->set('name', 'raffle');
		$button->set('options', "{handler: 'iframe', size: {x: 450, y: 400}}");
		if (!$app->isAdmin()) {
			$button = null;
		}
		return $button;
	}
}
?>