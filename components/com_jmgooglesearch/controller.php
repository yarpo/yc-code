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

jimport( 'joomla.application.component.controller' );

/**
 * JMGoogleSearch Controller
 *
 * @package     JoomlaMind Components
 * @subpackage  JMGoogleSearch
 */
class JMGoogleSearchController extends JController {
    
    // Check the table in so it can be edited.... we are done with it anyway
	private    $defaultLink = 'index.php?option=com_jmgooglesearch';
    
    function __construct($config = array()) {
	    
		parent::__construct($config);
		
		// use the same models as the back-end
        $path = JPATH_COMPONENT_ADMINISTRATOR.DS.'models';
        $this->addModelPath($path);
        
	}
	
    public function display() {

        $document =& JFactory::getDocument();
        
        /* @var $document JDocument */
        $viewType = $document->getType();

        // Set add and edit task parameters
        switch($this->getTask())
		{
			case 'add'     :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form'  );
//				JRequest::setVar( 'view'  , 'new');

			} 
			break;
			case 'edit'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form'  );
//				JRequest::setVar( 'view'  , 'edit');

			} 
			break;
			
		}
        
		// Get layout value
        $viewLayout =   JRequest::getVar('layout', 'default');
        // Get view value
        $viewName   =   JRequest::getVar('view', 'search');
        
        // Get view
        $view = &$this->getView( $viewName, $viewType );
        /* @var $view JView */
        
        // Get model
        $model =&   $this->getModel( "parameters" );
        if (!JError::isError( $model ) ) {
            $view->setModel( $model, true );
        }
        
        // Set layout into view 
        $view->setLayout($viewLayout);
        
        // Display view
        $view->display();
        
    }
    
}

?>  