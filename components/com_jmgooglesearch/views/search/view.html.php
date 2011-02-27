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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class JMGoogleSearchViewSearch extends JView {

    public function display($tpl = null) {
        
        $model      =   $this->getModel();
        $layout     =   $this->getLayout();
        
        // Add styles
        $document       = JFactory::getDocument();
        /* @var $document JDocument */
        $document->addScript("http://www.google.com/jsapi");
                
        $q  =   JRequest::getString("q", "", "get");
        
        try {
            
            $params =   $model->getData();
            $this->assignRef( "params", $params );
            $this->assign( "q", $q );
            
        } catch ( JmException $e  ){
            
            $e->log();
            // display error
            JError::raiseError(500, JText::_("System error!"));
            
       } catch ( Exception $e  ){

           $jmSecurity = new JmSecurity( $e );
           $jmSecurity->AlertMe();

           // display error
           JError::raiseError(500, JText::_("System error!"));
        }
        
        parent::display($tpl);
    }
}
?>
