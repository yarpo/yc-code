<?php
/**
* @package JFusion
* @subpackage System_Plugin
* @author JFusion development team
* @copyright Copyright (C) 2008 JFusion. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
* Load the JFusion framework if installed
*/
jimport('joomla.plugin.plugin' );
$model_file = JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.factory.php';
$factory_file = JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php';
if (file_exists($model_file) && file_exists($factory_file)) {
	/**
	* require the JFusion libraries
	*/
	require_once($model_file);
	require_once($factory_file);
} 

/**
* JFusion User class
* @package JFusion
*/
class plgSystemJfusion extends JPlugin {
    
    /**
* Constructor
*
* For php4 compatability we must not use the __constructor as a constructor for plugins
* because func_get_args ( void ) returns a copy of all passed arguments NOT references.
* This causes problems with cross-referencing necessary for the observer design pattern.
*
* @access	protected
* @param	object	$subject The object to observe
* @param 	array   $config  An array that holds the plugin configuration
* @since	1.0
*/
    function plgSystemJfusion(& $subject, $config)
    {
        
        parent::__construct($subject, $config );
		$this->loadLanguage('com_jfusion', JPATH_BASE);        
        
    }
    
    function onAfterInitialise()
    {
        //initialise some vars
        ob_start();
        $refresh = false;
        $status = array();
        $task = JRequest::getVar('task');
        $status ['debug'] = array();
        $status ['error'] = array();
        
		//prevent endless loops
		$time = JRequest::getVar('time');
		if(!empty($time)) return;
        
        //only call keepAlive if in the frontend
        $keepalive = $this->params->get('keepalive');
        global $mainframe;
        if ($mainframe->isSite()&&!empty($keepalive)&& $task !='logout') {
            //for master if not joomla_int
            $master = JFusionFunction::getMaster();
            if (!empty($master) && $master->name != 'joomla_int') {
                $JFusionUser =& JFusionFactory::getUser($master->name);
                $changed = $JFusionUser->keepAlive();
                if(!empty($changed)){
                	$refresh = true;
                }
            }
            
            //slave plugins
            $plugins = JFusionFunction::getPlugins();
            foreach($plugins as $plugin) {
                $JFusionUser =& JFusionFactory::getUser($plugin->name);
                $changed = $JFusionUser->keepAlive();
                if(!empty($changed)){
                	$refresh = true;
                }
            }
        }
        
        /**
* Joomla Object language with the current information about the language loaded
* In the purpose to reduce the load charge of Joomla and the communication with the others
* integrated software the script is realized once the language is changed
**/
        $synclanguage = $this->params->get('synclanguage');
        if (!empty($synclanguage)) {
            $JLang = &JFactory::getLanguage();
            $session = &JFactory::getSession();
            $oldlang = $session->get('oldlang' );
            if (! isset($oldlang ) || $oldlang != $JLang->_lang) {
                $session->set('oldlang', $JLang->_lang );
                
                // The instance of the user is not obligatory. Without to be logged, the user can change the language of the integrated softwares
                // if those implement it.
                $userinfo = &JFactory::getUser();
                
                $master = JFusionFunction::getMaster();
                $JFusionMasterPublic =& JFusionFactory::getPublic($master->name );
                if (method_exists($JFusionMasterPublic, 'setLanguageFrontEnd' )) {
                    $status = $JFusionMasterPublic->setLanguageFrontEnd($userinfo );
                    
                    if (! empty($status ['error'] )) {
                        //could not set the language
                        JFusionFunction::raiseWarning($master->name . ' ' . JText::_('SET_LANGUAGEFRONTEND_ERROR' ), $status ['error'], 1 );
                    }
                } else {
                    $status ['debug'] [] = JText::_('METHOD_NOT_IMPLEMENTED' ) . ": " . $master->name;
                }
                
                $slaves = JFusionFunction::getSlaves();
                foreach($slaves as $slave ) {
                    $JFusionSlavePublic =& JFusionFactory::getPublic($slave->name );
                    if (method_exists($JFusionSlavePublic, 'setLanguageFrontEnd' )) {
                        $status = $JFusionSlavePublic->setLanguageFrontEnd($userinfo );
                        
                        if (! empty($status ['error'] )) {
                            //could not set the language
                            JFusionFunction::raiseWarning($slave->name . ' ' . JText::_('SET_LANGUAGEFRONTEND_ERROR' ), $status ['error'], 1 );
                        }
                    } else {
                        $status ['debug'] [] = JText::_('METHOD_NOT_IMPLEMENTED' ) . ": " . $slave->name;
                    }
                }
            }
        }
        //check if page refresh is needed
        if($refresh == true){
            $uri =& JURI::getInstance();
			//add a variable to ensure refresh
			$uri->setVar('time',time());
            $link= $uri->toString();    
            $mainframe = &JFactory::getApplication('site');
            $mainframe->redirect($link);                   
			exit();        	
        }
        
        //stop output buffer
        ob_end_clean();
    }
}
