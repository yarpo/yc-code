<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

defined('_JEXEC') or die( 'Restricted access' );

class JScriptegrator {
	
	/**
	 * Set properties for Scriptegrator plugin
	 * 
	 * @param $property 
	 * @return string
	 */
	function properties($property = 'name') {
	
		$name = 'cdscriptegrator';
		$version = '1.3.8';
		$folder = '/plugins/system/cdscriptegrator';
		
		switch ($property) {
			case 'name':
				$property = $name;
				break;
			case 'version':
				$property = $version;
				break;
			case 'folder':
				$property = $folder;
				break;
			default:
				$property = $name;
				break;
		}
		return $property;
	}
	
	/**
	 * Define Scriptegrator
	 * 
	 * @return void
	 */
	function defineScriptegrator() {
		if (!defined('_JSCRIPTEGRATOR')) define('_JSCRIPTEGRATOR', JScriptegrator::properties('name'));
	}
	
	/**
	 * Routine to check Scriptegrator plugin
	 * 
	 * @param $version_number
	 * @param $library
	 * @param $place
	 * @return string	Error message if some option is missing.
	 */
	function check($version_number = '1.3.8', $library = 'jquery', $place = 'site') {
		$message = '';
		// check if Scriptegrator is enabled
		if (defined('_JSCRIPTEGRATOR'))
		{
			// check version
			$version = $version_number;
			if (!JScriptegrator::versionRequire($version))
			{
				$message = JText::sprintf('CDS_SCRIPTEGRATOR_REQUIREVERSION', $version);
			} else {
				// check place of library
				if (!JScriptegrator::checkLibrary($library, $place)) $message = JText::_('CDS_MISSING_' . strtoupper($library));
			}
		}
		return $message;
	}

	/**
	 * Return Scriptegrator folder path
	 * 
	 * @param $absolute
	 * @return string
	 */
	function folder($absolute = false) {
		global $mainframe;

		$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		
		$path = JURI::root(true) . JScriptegrator::properties('folder');
		
		if ($absolute) $path = JPath::clean($root . JScriptegrator::properties('folder'));

		return $path;
	}

	/**
	 * Get actual Scriptegrator version
	 * 
	 * @return string
	 */
	function getVersion() {
		return JScriptegrator::properties('version');
	}

	/**
	 * Check version compatibility
	 * 
	 * @param $min_version
	 * @return boolean
	 */
	function versionRequire($min_version) {
		return (version_compare( JScriptegrator::getVersion(), $min_version, '>=' ) == 1);
	}
	
	/**
	 * Library JS loader
	 * 
	 * @param $library
	 * @return void
	 */
	function library($library = 'jquery') {
		$document = &JFactory::getDocument(); // set document for next usage
		$document->addScript(JURI::root(true) . JScriptegrator::properties('folder') . "/libraries/$library/js/jsloader.php");
	}
	
	/**
	 * jQuery UI loader - USE importUI() function
	 * 
	 * @param $compress
	 * @param $file
	 * @return void
	 */
	function UILoader($compress = 0, $file = 'ui.core') {
		JScriptegrator::importUI($file);
	}
	
	/**
	 * Import UI script
	 * 
	 * @param $file
	 * @return void
	 */
	function importUI($uis = '') {
		$document = &JFactory::getDocument(); // set document for next usage
		
		$dir = JScriptegrator::folder(true) . DS . 'libraries' . DS . 'jquery' . DS . 'js'. DS . 'ui';
		
		if (!JFolder::exists($dir)) return false;
		
		$files = JFolder::files($dir, '\.js', false, false);
		
		$uifolder = $dir . DS . 'ui' . '.zip';
		
		// ui folder is not unpacked
		if (!$files) {
			jimport('joomla.filesystem.archive');
			JArchive::extract($uifolder, $dir);
		}
		
		$uis = array_map('trim', explode('|', $uis));
		array_unshift($uis, 'ui.core'); // unshift necessary UI core script
		$uis = array_unique($uis);
		foreach ($uis as $ui) {
			// UI script
			$document->addScript(JURI::root(true) . JScriptegrator::properties('folder') . "/libraries/jquery/js/ui/jsloader.php?file=$ui");
		}
	}
	
	/**
	 * Import UI CSS theme
	 * 
	 * @param $theme
	 * @param $file
	 * @return void
	 */
	function importUITheme($theme = 'smoothness', $file = '') {
		$document = &JFactory::getDocument(); // set document for next usage
		
		$dir = JScriptegrator::folder(true) . DS . 'libraries' . DS . 'jquery' . DS . 'theme' . DS . $theme;
		
		if (!JFolder::exists($dir)) return false;
		
		$files = JFolder::files($dir, '\.css', false, false);
		
		$themepath = $dir . DS . $theme . '.zip';
		
		// theme is not unpacked
		if (!$files) {
			jimport('joomla.filesystem.archive');
			JArchive::extract($themepath, $dir);
		}
		
		$uis = array_map('trim', explode('|', $file));
		array_unshift($uis, 'ui.theme'); // unshift necessary UI style - must be first
		array_unshift($uis, 'ui.core'); // unshift necessary UI style
		$uis = array_unique($uis);
		
		foreach ($uis as $ui) {
			// UI style
			$document->addStyleSheet(JURI::root(true) . JScriptegrator::properties('folder') . "/libraries/jquery/theme/$theme/cssloader.php?file=$ui", 'text/css');
		}
		
		
	}
		
	/**
	 * jQuery UI CSS loader - USE importUITheme() function
	 * 
	 * @param $compress
	 * @param $theme
	 * @param $file
	 * @return void
	 */
	function UICssLoader($compress = 0, $theme = 'smoothness', $file = 'ui.base') {
		JScriptegrator::importUITheme($theme, $file);
	}
	

	/**
	 * Check if library is enabled (jQuery, Highslide...)
	 * 
	 * @param $library
	 * @param $interface
	 * 
	 * @return boolean
	 */
	function checkLibrary($library = 'jquery', $interface = 'site') {
		global $mainframe;

		$plugin = &JPluginHelper::getPlugin('system', _JSCRIPTEGRATOR);
		$pluginParams = new JParameter($plugin->params);

		$pluginParams = (int)$pluginParams->get($library, 0);
		
		$library = false;
		
		switch ($interface) {
			case 'site':
				switch ($pluginParams) {
					case 1:
					case 3:
						$library = true;
						break;
					default:
						$library = false;
						break;
				}
				break;
			case 'admin':
				switch ($pluginParams) {
					case 2:
					case 3:
						$library = true;
						break;
					default:
						$library = false;
						break;
				}
				break;
			default:
				return false;
				break;
		}
		
		return $library;
	}
	
	/**
	 * Return list of available themes
	 * 
	 * @return array
	 */
	function themeList() {
		jimport('joomla.filesystem.folder');
		$path = JScriptegrator::folder(true) . DS . 'libraries' . DS . 'jquery' . DS . 'theme';
		$files = array();
		$files = JFolder::folders($path, '.', false, false);
		return $files;
	}
}

?>
