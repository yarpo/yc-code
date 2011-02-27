<?php
/**
 * @version		$Id: example.php 10714 2008-08-21 10:10:14Z eddieajau $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Example Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentMoocasa extends JPlugin
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
	function plgContentMoocasa( &$subject, $params )
	{
		parent::__construct( $subject, $params );
 
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'content', 'moocasa' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

	}

	/**
	 * Example after display title method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayTitle( &$article, &$params, $limitstart )
	{
		global $mainframe;

		return '';
	}

	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe, $option, $moocasaParams;
		
		$moocasaCount = substr_count($article->text, "{moocasa");
		if ($moocasaCount == 0) {
			return;
		}
		
		if ($moocasaCount > 1) {
			JError::raiseNotice(400, JText::_("ONLY ONE OCCURENCE OF PICASA ALLOWED"));		
			return '';
		}
		 
		// content edito fix
		// thanks to ralf
		$article->text = ereg_replace("&quot;","\"",$article->text);
		preg_match('#{moocasa url="(.+)"}#',$article->text,$resultat);		
		if (($moocasaCount == 1) && (count($resultat) == 2)) {
			// fire PHP
			$firePhpClassFile1 = JPATH_PLUGINS.DS.'system'.DS.'firephp'.DS.'firePHP.class.php';
			$firePhpClassFile2 = JPATH_PLUGINS.DS.'system'.DS.'firephp'.DS.'fb.php';
			
			// on test sir firePhp est installé
			if (file_exists($firePhpClassFile1 ) && file_exists($firePhpClassFile2 )) {
				require_once($firePhpClassFile1);
				require_once($firePhpClassFile2);
				
				$firephp = FirePHP::getInstance(true); /* @var $firephp FirePHP */
				$options = array('maxObjectDepth' => 5, 'maxArrayDepth' => 10, 'useNativeJsonEncode' => true, 'includeLineNumbers' => true);
				$firephp->setOptions($options);
				$firephp->registerErrorHandler();
				$firephp->registerExceptionHandler();
			} else {
				require_once(JPATH_ROOT.DS.'components'.DS.'com_anotherpicasa'.DS.'classes'.DS.'fake_firephp.php');
			}
		
			$albumUrl = $resultat[1];
			
			$view = JRequest::getCmd('view',null);
			$id = JRequest::getInt('id',null); 
			$layout = JRequest::getCmd('layout',null);
			
			JRequest::setVar('view', 'album');
			JRequest::setVar('layout','default');
			
			require_once (JPATH_SITE.DS.'components'.DS.'com_anotherpicasa'.DS.'controller.php');
			$controllerClass = new PicasaController(array('base_path'=>JPATH_ROOT.DS.'components'.DS.'com_anotherpicasa'));
			
			$this->_params->set("url_web_album", $albumUrl);
			$moocasaParams = $this->_params;
			
			ob_start();
			$controllerClass->display(); 
			$pluginHtml = ob_get_contents(); 
			ob_end_clean();
			
			$article->text = str_replace($resultat[0], $pluginHtml, $article->text);
			
			if($view != null) {
				JRequest::setVar('view', $view);
			}
			
			if($id != null) {
				JRequest::setVar('id', $id);
			}
			
			if($layout != null) {
				JRequest::setVar('layout', $layout);
			}
		} else {
			JError::raiseNotice(400, JText::_("MOOCASA SYNTAX ERROR"));		
		}

		return '';
	}

	/**
	 * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

		return '';
	}

	/**
	 * Example before save content method
	 *
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * 	You can set the error by calling $article->setError($message)
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	bool		If false, abort the save
	 */
	function onBeforeContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	void
	 */
	function onAfterContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

}
