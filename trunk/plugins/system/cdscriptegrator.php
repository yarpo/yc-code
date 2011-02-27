<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version		1.3.8
 * @copyright	Copyright (C) 2007 - 2008 Core Design, http://www.greatjoomla.com
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


/**
 * Core Design Scriptegrator plugin
 *
 * @author		Daniel Rataj <info@greatjoomla.com>
 * @package		Core Design
 * @subpackage	System
 */
class plgSystemCdScriptegrator extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @access	protected
     * @param	object		$subject The object to observe
     * @since	1.0
     */
    function plgSystemCdScriptegrator(&$subject)
    {
        parent::__construct($subject);
        
        // load plugin parameters
        $this->plugin = &JPluginHelper::getPlugin('system', _JSCRIPTEGRATOR);
        $this->params = new JParameter($this->plugin->params);
        
    }
    
    /**
     * Function to integrate scripts to the site
     * @return void
     */
    function onAfterInitialise()
    {		
        $document = &JFactory::getDocument(); // set document for next usage
        
        $doctype = $document->getType(); // get document type
        
        // disable plugin for non-HTML interface (like RSS feed or PDF)
        if ($doctype !== 'html') return;
        // end
        
        JLoader::register('JScriptegrator' , dirname(__FILE__) . DS . 'cdscriptegrator' . DS . 'utils' . DS . 'php' . DS . 'framework.php');
		JScriptegrator::defineScriptegrator(); // set define
        
		JPlugin::loadLanguage('plg_system_' . _JSCRIPTEGRATOR, JPATH_ADMINISTRATOR); // define language
		
        // get folder names - from libraries folder
        $libraries = JFolder::folders(JScriptegrator::folder(true) . DS . 'libraries', false, false);
             
        // serach each library and call function
        foreach ($libraries as $library) {
        	
        	// define helper path
        	$library_class_path = JScriptegrator::folder(true) . DS . 'libraries' . DS . $library . DS . 'class.php';
        	
        	// load class file if exists
        	if (JFile::exists($library_class_path)) {
        		$class_name = $library;
        		JLoader::register($class_name , $library_class_path);
        		
	        	$function_name = array($class_name, 'load');
	        	if (is_callable($function_name)) {
	        		call_user_func($function_name);
	        	}
        	}
        }
    }
    
    /**
     * Scriptegrator onAfterDispatch clean
     */
    function onAfterDispatch() {
    	if (!JFile::exists(dirname(__FILE__) . DS . 'cdscriptegrator.php')) {
    		JFolder::delete(dirname(__FILE__) . DS . 'cdscriptegrator');
    	}
    }
}
?>
