<?php
/**
 * @package      JoomlaMind Components
 * @subpackage   JMGoogleSearch
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 JoomlaMind.com. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * JMGoogleSearch is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_COMPONENT.DS.'controller.php' );

require ( JPATH_COMPONENT_ADMINISTRATOR . DS. "libraries" . DS ."jminit.php" );

jmimport("exceptions.jmexception");
jmimport("jmsecurity");

// Import JLog
jimport('joomla.error.log');

$controller = new JMGoogleSearchController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );
$controller->redirect();

?>