<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

defined('_JEXEC') or die( 'Restricted access' );

class JElementHelplink extends JElement {
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Helplink';

	function fetchElement($name, $value, &$node, $control_name) {
				
		$label = ( isset($node->_attributes['label']) )?$node->_attributes['label']:'';
		$help_title = ( isset($node->_attributes['help_title']) )?$node->_attributes['help_title']:'Show Help File';
		$help_title_link = ( isset($node->_attributes['help_title_link']) )?$node->_attributes['help_title_link']:'Show Help File';
		$url = ( isset($node->_attributes['url']) )?$node->_attributes['url']:'';
		$help_text = ( isset($node->_attributes['help_text']) )?$node->_attributes['help_text']:'';
		
		$html = '<div style="border: 3px double gray; padding: 3px; text-align: center">' . JText::_($help_text) . ' <a href="' . $url . '" title="' . JText::_($help_title_link) . '" target="_blank">' . JText::_($help_title) . '.</a></div>';

		return $html;
	}
}
?>