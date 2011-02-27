<?php
/*
 * @package      JoomlaMind Modules
 * @subpackage   JMGoogleSearch
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 JoomlaMind.com. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * JMGoogleSearch is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access'); // no direct access 

jimport('joomla.application.component.helper');

if (!JComponentHelper::isEnabled('com_jmgooglesearch', true)) {
    
    JError::raiseError(404, JText::_('JMGoogleSearch component does not exist.'));
    
}

$jmgsq = JRequest::getString("jmgsq", "", "get");

require(JModuleHelper::getLayoutPath('mod_jmgooglesearch'));
