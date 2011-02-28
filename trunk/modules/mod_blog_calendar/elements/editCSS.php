<?php
/**
* @license		GNU/GPL
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementeditCSS extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'editCSS';
	function fetchElement($name, $value, &$node, $control_name)
	{
	return '<a href=\''.'?option=com_blog_calendar&task=editcss&task=edit_css'.'\' >Edit css</a>';
		}
}
?>