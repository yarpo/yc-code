<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

defined('_JEXEC') or die( 'Restricted access' );

class jquery {
	/**
	 * Load library
	 * @return void
	 */
	function load()
	{
		global $mainframe;

		// load plugin parameters
		$params = &JPluginHelper::getPlugin('system', _JSCRIPTEGRATOR);
		$params = new JParameter($params->params);

		$enable = (int)$params->get('jquery', 0); // enable jQuery JS
		$theme = $params->get('jquery_ui_theme', 'smoothness');
		
		if ($theme == '-1') {
			$theme = 'smoothness';
		}
		
		$load = false;

		switch ($enable)
		{
			case 0:
				$load = false;
				break;
			case 1:
				if ($mainframe->isSite()) $load = true;
				break;
			case 2:
				if ($mainframe->isAdmin()) $load = true;
				break;
			case 3:
				$load = true;
				break;
			default:
				$load = false;
				break;
		}

		if (!$load) return;

		// load library
		$document = &JFactory::getDocument(); // set document for next usage
		
		// attach jQuery JS
		JScriptegrator::library('jquery');
		
		// attach jQuery UI core script
		JScriptegrator::importUI('ui.core');
	}
}

?>