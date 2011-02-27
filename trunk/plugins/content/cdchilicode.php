<?php
/**
 * Core Design Chili Code plugin for Joomla! 1.5
 * @author      Daniel Rataj, <info@greatjoomla.com>
 * @package     Joomla
 * @subpackage	Content
 * @category    Plugin
 * @version     1.0.3
 * @copyright   Copyright (C)  2007 - 2008 Core Design, http://www.greatjoomla.com
 * @license     http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
// end

class plgContentCdChilicode extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentCdChilicode(&$subject)
	{
		parent::__construct($subject);

		// load plugin parameters
		$plugin = &JPluginHelper::getPlugin('content', 'cdchilicode');
		$pluginParams = new JParameter($plugin->params);
	}

	/**
	 * Call the Chilicode function
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent(&$article, &$params, $limitstart)
	{
		// regular expression
		$languages = array();
		$languages []= 'js';
		$languages []= 'php';
		$languages []= 'mysql';
		$languages []= 'html';
		$languages []= 'java';
		$languages []= 'cplusplus';
		$languages []= 'csharp';
		$languages []= 'delphi';
		$languages []= 'lotusscript';
		$languages []= 'css';
		$languages []= 'none';
		
		$regex = "#{chilicode(?:\s?(" . implode("|", $languages) . ")?\s?(content|file)?)?}(.*?){/chilicode}#is";

		if (!preg_match($regex, $article->text)) {
			return;
		}
		
		// load language
		JPlugin::loadLanguage('plg_content_cdchilicode', JPATH_ADMINISTRATOR);
		
		// define language
		if (!defined('_JSCRIPTEGRATOR'))
		{
			JError::raiseNotice('', JText::_('CHILICODE_ENABLE_SCRIPTEGRATOR'));
			return;
		}

		// require Scriptegrator version 1.3.4 or higher
		$version = '1.3.4';
		if (!JScriptegrator::versionRequire($version))
		{
			JError::raiseNotice('', JText::sprintf('CHILICODE_SCRIPTEGRATOR_REQUIREVERSION', $version));
			return;
		}

		if (!JScriptegrator::checkLibrary('jquery', 'site'))
		{
			JError::raiseNotice('', JText::_('CHILICODE_MISSING_JQUERY'));
			return;
		}


		// Explication:
		// $match[1]	-> code

		// load head JS and CSS
		$this->cdChilicodeLoadScript();

		// replacement {chilicode}{/chilicode}
		$article->text = preg_replace_callback($regex, array($this, 'replacer'), $article->
		text);


	}

	/**
	 * Replacer
	 *
	 * Call Chilicode Replacer
	 *
	 * @access	public
	 * @return	string
	 */
	function replacer(&$match)
	{
		global $mainframe;

		$absolute_path = dirname(dirname(dirname(__FILE__))); // define absolute path

		$plugin = &JPluginHelper::getPlugin('content', 'cdchilicode');
		$pluginParams = new JParameter($plugin->params);

		// load database parameters
		$directory = $pluginParams->get('directory', 'images/stories');
		$default_lang = $pluginParams->get('default_lang', 'php');
		$default_source = $pluginParams->get('default_source', 'php');
		// end

		// define general regex
		if ($match[1])
		{
			$lang = $match[1];
		} else
		{
			$lang = $default_lang; // lang not defined
		}
		if ($match[2])
		{
			$source = $match[2];
		} else
		{
			$source = $default_source; // code not defined
		}
		if ($match[3])
		{
			$code = $match[3];
			$code = strip_tags(str_replace(array('<br />'), "\n", trim($code)));
			
		} else
		{
			$code = ''; // code not defined
		}
		// end
		
		switch ($source)
		{
			case 'content':
					
				if ($lang == 'none')
				{
					return '<pre class="cdchilicode_pre_block"><code>' . $code . '</code></pre>';
				} else
				{
					return '<pre class="cdchilicode_pre_block"><code class="' . $lang .
                    '">' . $code . '</code></pre>';
				}

				return '<pre class="cdchilicode_pre_block"><code class="' . $lang .
                '">' . $code . '</code></pre>';
				break;
			case 'file':

				// check if path to the file is defined
				if (!$code)
				{
					JError::raiseNotice('', JText::_('CHILICODE_NO_PATH'));
					return;
				}
				// end

				$source_path = JPath::clean($absolute_path . DS . $directory . DS . $code);
				
				if (JFile::exists($source_path))
				{
					$content = JFile::read($source_path);

					return '<pre class="cdchilicode_pre_block"><code class="' . $lang .
                    '">' . $content . '</code></pre>';
				} else
				{
					JError::raiseNotice('', JText::_('CHILICODE_NO_FILE'));
					return;
				}

				break;
			default:
				return '<pre class="cdchilicode_pre_block"><code class="' .
				$lang . '">' . $code . '</code></pre>';
				break;
		}
	}

	function cdChilicodeLoadScript()
	{
		global $mainframe;

		$live_path = JURI::base(true) . '/'; // define live site
		$document = &JFactory::getDocument();

		$cd_chili_jQuery_chili_script ='
		<script type="text/javascript">
		ChiliBook.automatic = true;
		ChiliBook.lineNumbers = true;
		ChiliBook.recipeFolder = "' . $live_path . 'plugins/content/cdchilicode/utils/js/jquery/";
		</script>';
		$document->addScript($live_path .
	            'plugins/content/cdchilicode/utils/js/jquery/jquery.chili.js');
		$document->addCustomTag($cd_chili_jQuery_chili_script);
		
		$document->addStyleSheet($live_path . 'plugins/content/cdchilicode/css/cdchilicode.css', 'text/css');
	}
}

?>
