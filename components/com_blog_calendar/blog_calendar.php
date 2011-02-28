<?php
/**
* @version		1.05
* @package		BlogCalendar Reload
* @author		Juan Padial
* @authorwebsite	http://www.shikle.com
* @license		GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_COMPONENT.DS.'controller.php');
$classname	= 'BlogCalendarController'.$controller;
$controller = new $classname( );
$controller->execute( JRequest::getVar('task'));
$controller->redirect();
?>