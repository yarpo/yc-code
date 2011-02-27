<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.5
 */

defined('_JEXEC') or die( 'Restricted access' );

class highslide {
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

		$enable = (int)$params->get('highslide', 0); // enable Highslide JS

		$load = false;

		switch ($enable)
		{
			case 0:
				$load = false;
				break;
			case 1:
				if ($mainframe->isSite())
				{
					$load = true;
				}
				break;
			case 2:
				if ($mainframe->isAdmin())
				{
					$load = true;
				}
				break;
			case 3:
				$load = true;
				break;
			default:
				$load = false;
				break;
		}

		if (!$load) {
			return;
		}

		// load library
		JScriptegrator::library('highslide');

		// define database parameters
		$outlineType = $params->get('outlineType', 'rounded-white');
		$outlineWhileAnimating = (int) $params->get('outlineWhileAnimating', 1);
		$showCredits = (int) $params->get('showCredits', 1);
		$expandDuration = (int)  $params->get('expandDuration', 250);
		$anchor = $params->get('anchor', 'auto');
		$align = $params->get('align', 'auto');
		$transitions = $params->get('transitions', 'expand');
		$dimmingOpacity = $params->get('dimmingOpacity', '0');
		// end

		// define script parameters
		switch ($outlineWhileAnimating)
		{
			case 1:
				$outlineWhileAnimating = 'true';
				break;
			case 0:
				$outlineWhileAnimating = 'false';
				break;
			default:
				$outlineWhileAnimating = 'true';
				break;
		}

		switch ($showCredits)
		{
			case 1:
				$showCredits = 'true';
				break;
			case 0:
				$showCredits = 'false';
				break;
			default:
				$showCredits = 'true';
				break;
		}

		switch ($transitions)
		{
			case 'expand':
				$transitions = '["expand"]';
				break;
			case 'fade':
				$transitions = '["fade"]';
				break;
			case 'expand+fade':
				$transitions = '["expand", "fade"]';
				break;
			case 'fade+expand':
				$transitions = '["fade", "expand"]';
				break;
			default:
				$transitions = '["expand"]';
				break;
		}

		// end

		$default_core_script = "
	<script type=\"text/javascript\">
		hs.graphicsDir = '" . JScriptegrator::folder() . "/libraries/highslide/graphics/';
    	hs.outlineType = '" . $outlineType . "';
    	hs.outlineWhileAnimating = " . $outlineWhileAnimating . ";
    	hs.showCredits = " . $showCredits . ";
    	hs.expandDuration = " . $expandDuration . ";
		hs.anchor = '" . $anchor . "';
		hs.align = '" . $align . "';
		hs.transitions = " . $transitions . ";
		hs.dimmingOpacity = " . $dimmingOpacity . ";
		hs.lang = {
		   loadingText :     '" . JText::_('CDS_LOADING') . "',
		   loadingTitle :    '" . JText::_('CDS_CANCELCLICK') . "',
		   focusTitle :      '" . JText::_('CDS_FOCUSCLICK') . "',
		   fullExpandTitle : '" . JText::_('CDS_FULLEXPANDTITLE') . "',
		   fullExpandText :  '" . JText::_('CDS_FULLEXPANDTEXT') . "',
		   creditsText :     '" . JText::_('CDS_CREDITSTEXT') . "',
		   creditsTitle :    '" . JText::_('CDS_CREDITSTITLE') . "',
		   previousText :    '" . JText::_('CDS_PREVIOUSTEXT') . "',
		   previousTitle :   '" . JText::_('CDS_PREVIOUSTITLE') . "',
		   nextText :        '" . JText::_('CDS_NEXTTEXT') . "',
		   nextTitle :       '" . JText::_('CDS_NEXTTITLE') . "',
		   moveTitle :       '" . JText::_('CDS_MOVETITLE') . "',
		   moveText :        '" . JText::_('CDS_MOVETEXT') . "',
		   closeText :       '" . JText::_('CDS_CLOSETITLE') . "',
		   closeTitle :      '" . JText::_('CDS_CLOSETEXT') . "',
		   resizeTitle :     '" . JText::_('CDS_RESIZETITLE') . "',
		   playText :        '" . JText::_('CDS_PLAYTEXT') . "',
		   playTitle :       '" . JText::_('CDS_PLAYTITLE') . "',
		   pauseText :       '" . JText::_('CDS_PAUSETEXT') . "',
		   pauseTitle :      '" . JText::_('CDS_PAUSETITLE') . "',   
		   number :          '" . JText::_('CDS_NUMBER') . "',
		   restoreTitle :    '" . JText::_('CDS_RESTORETITLE') . "'
		};
		
	</script>\n";

		$document = &JFactory::getDocument(); // set document
		$document->addCustomTag($default_core_script);
		$document->addStyleSheet(JScriptegrator::folder() . '/libraries/highslide/css/cssloader.php', 'text/css');
	}
}

?>