<?php
/**
* @version		1.2.2.1
* @package		Blog Calendar
* @author		Justo Gonzalez de Rivera
* @license		GNU/GPL
*/


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementcustomRadio extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'customRadio';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$options = array ();
		foreach ($node->children() as $option)
		{
		
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
			
		}
				
		return "<span id=\"$name\">".JHTML::_('select.radiolist', $options, ''.$control_name.'['.$name.']', $atributes, 'value', 'text', $value, $control_name.$name ) . "</span>";
	}
}