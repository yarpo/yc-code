<?php
/**
 * Moron Solutions Comments for Joomla!  
 * @version 0.8.0b
 * @package ZiMB Comment
 * @copyright (C) 2008 ZiMB LLC. All rights reserved.
 * @license  GPL
 * 
 */

// No direct access.
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.plugin.plugin');

class plgContentzimbcomment extends JPlugin
{
	function plgContentzimbcomment(&$subject)//, $config)
	{
		parent::__construct($subject, "");
	}
	
	function onPrepareContent(& $article, & $params, $page = 0)
	{
		global $mainframe;
		
		$db = & JFactory::getDBO();
		$document = &JFactory::getDocument();
		$jsPath = '/plugins/content/JavaScript/zimbcomment.js';
		$document->addScript(JURI::base() . $jsPath);
		$css = JURI::base() . "administrator/components/com_zimbcomment/zimbcomment.css";
		$document->addStyleSheet($css);
		
		
		// Check if comments disabled for this article.
		$enabled = true;
		// Check Article
		$query = "SELECT id from " . $db->nameQuote('#__zimbComment_Disabled') . " WHERE " . $db->nameQuote('target_id') . " = " . $article->id . " AND " . $db->nameQuote('type') . " = 'article'";
		$db->setQuery($query);
		$found = $db->loadResult();
		if ($found) $enabled = false;
		// Check Category
		$query = "SELECT id from " . $db->nameQuote('#__zimbComment_Disabled') . " WHERE " . $db->nameQuote('target_id') . " = " . $article->catid . " AND " . $db->nameQuote('type') . " = 'category'";
		$db->setQuery($query);
		$found = $db->loadResult();
		if ($found) $enabled = false;
		// Check Section
		$query = "SELECT id from " . $db->nameQuote('#__zimbComment_Disabled') . " WHERE " . $db->nameQuote('target_id') . " = " . $article->sectionid . " AND " . $db->nameQuote('type') . " = 'section'";
		$db->setQuery($query);
		$found = $db->loadResult();
		if ($found) $enabled = false;
		
		// Get Parameters Necessary
		if ($article->id) // BLM's idea to keep non-article pages from displaying comments.  Works great, THANKS BRAD!
		{
		$params = & JComponentHelper::getParams ( 'com_zimbcomment' );
		$useCss = $params->get('useCss', 0);
		if ($enabled)
		{
		$query = "SELECT COUNT(*) FROM " . $db->nameQuote('#__zimbComment_Comment') . " WHERE " . $db->nameQuote('articleId') .  " = " . $article->id . " AND " . $db->nameQuote('published') . "= 1";
		$db->setQuery($query);
		$commentCount = $db->loadResult();
		$path = JURI::base() . "index2.php?option=com_zimbcomment";
		// Display initial comment piece.
		$sq = "'";
		if ($useCss)
		{
			$css = '"commentsButton"';
		}
		else 
		{
			$css = '"modifydate"';
		}
		$html = '<div id="COUNT' . $article->id . '" class=' . $css . ' onclick="expandComments(' . $sq . 'COMMENT' . $article->id . 
										  $sq . ', ' . $sq . $path . $sq . ', ' . $sq . $article->id . $sq . ');" onmouseover = "toHand();" onmouseout="toDefault();"><p>Comments (' .
										  $commentCount . ')</p></div><div id="COMMENT' . $article->id . '"></div>';
		//$html = '<div onclick="expandComments(1, 2, 3);">Comments Here</div>';
		$article->text = $article->text . $html;
		} 
		}
	}
	function onAfterDisplayContent(& $article, & $params, $page=0)
	{
		//return "Got here after";
	}
}
?>