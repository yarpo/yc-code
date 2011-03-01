<?php
/**
 * @version $Id: plgContentTags.php 1.5
 * @copyright JoomlaTags.org
 * @license GNU/GPLv2,
 * @author http://www.joomlatags.org
 */
defined( '_JEXEC' ) or  die('Restricted access');
jimport( 'joomla.event.plugin' );

//ini_set( 'display_errors', 'On' ); 
//error_reporting( E_ALL );

require_once JPATH_SITE.DS.'components'.DS.'com_tag'.DS.'helper'.DS.'helper.php';
require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
class plgContentTags extends JPlugin
{
	/* where to paste code of tags */
	const BEFORE = 1;
	const BEFORE_AND_AFTER = 2;
	const TAG_LISTING_SITE = 'tag/';

	function plgContentTags( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		$app = &JFactory::getApplication();
		if ($app->getName() != 'site' || (isset($article) && empty($article->id)))
		{
			return true;
		}

		$FrontPageTag = JoomlaTagsHelper::param('FrontPageTag');
		$BlogTag = JoomlaTagsHelper::param('BlogTag');
		$view = JRequest :: getVar('view');
		$layout = JRequest :: getVar('layout');
		if ((('frontpage' === $view) && !$FrontPageTag) ||
			('blog' === $layout && !$BlogTag) ||
			(('blog' !== $layout) && 
				in_array($view, array('category', 'section'))))
		{
			return true;
		}

		$lang = &JFactory::getLanguage();
		$lang->load('com_tag', JPATH_SITE);

		$query='select t.id,t.name,t.hits from #__tag_term as t left join #__tag_term_content as c  on c.tid=t.id where c.cid='.$article->id.' order by t.weight desc,t.name';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$terms = $db->loadObjectList();

		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'components/com_tag/css/tagcloud.css');

		$ShowRelatedArticles = JoomlaTagsHelper::param('RelatedArticlesByTags', 0);
		$termIds = array();
		
		if (!empty($terms))
		{
			$HitsNumber = JoomlaTagsHelper::param('HitsNumber');
			$SuppresseSingleTerms = JoomlaTagsHelper::param('SuppresseSingleTerms');
			$MaxTagsNumber = JoomlaTagsHelper::param('MaxTagsNumber', 10);
			$links = '';

			for($i = 0, $n = count($terms); $i < $n && $i <= $MaxTagsNumber; $i++)
			{
				if ($ShowRelatedArticles || $SuppresseSingleTerms)
				{
					$countQuery = 'select count(cid) as ct from jos_tag_term_content where tid='.$terms[$i]->id;
					$db->setQuery($countQuery);
					$ct = $db->loadResult();

					if ($ct <= 1 && $SuppresseSingleTerms)
					{
						continue;
					}
					$termIds[] = $terms[$i]->id;
				}

				$link = $this->getLinkForTagResultList($terms[$i]->name);
				$terms[$i]->name = JoomlaTagsHelper::ucwords($terms[$i]->name);
				$name = $this->escapeHtml($terms[$i]->name);
				$title = $name;

				if ($HitsNumber)
				{
					$title .= $this->escapeAttributes(';Hits:'.$terms[$i]->hits);
				}
				$links .= '<li><a href="'.$link.'" rel="tag" title="'.$title.'" >'.$name.'</a></li>';
			}

			$this->insertTagsCodeInProperPlaceToArticle($article, $links);
		}

		if (JoomlaTagsHelper::param('ShowAddTagButton'))
		{
			$canEdit = $this->canCurrentUserAddTagsToArticle($article->id);
			if ($canEdit)
			{
				$Itemid = JRequest::getVar( 'Itemid', false);
				$Itemid = is_numeric($Itemid) ? intval($Itemid) : 1;
				$article->text .= $this->addTagsButtonsHTML($article->id, $Itemid, $havingTags);
			}
		}

		if ($ShowRelatedArticles && !empty($termIds) && 'article' == $view) 
		{
			$article->text .= $this->showReleatedArticlesByTags($article->id, $termIds);
		}

		return true;
	}

	private function insertTagsCodeInProperPlaceToArticle(&$article, $links)
	{
		if ('' !== $links)
		{
			$tagResult = '<div class="clearfix"></div><div class="tag">'.JText::_('TAGS:').'<ul>'.$links.'</ul></div>';
			switch (JoomlaTagsHelper::param('TagPosition')) 
			{
				case self::BEFORE : 
					$article->text = $tagResult . $article->text;
					return;
				case self::BEFORE_AND_AFTER:
					$article->text = $tagResult . $article->text . $tagResult;
					return;
			}
			$article->text .= $tagResult;
		}
	}

	private function canCurrentUserAddTagsToArticle( $articleId )
	{
		$user	=& JFactory::getUser();
		return $this->canUserAddTags($user, $articleId);
	}

	/** Dont let to inject any code by tags, eg.
	 *  in:  $attr = '"><script></script>' => injection possible
	 *  out: $attr = '\"><script></script>' => safe
	 *  
	 * */
	private function escapeAttributes( $attr )
	{
		return addslashes($attr);
	}

	/**Dont let to inject any code
	 * */
	private function escapeHtml( $code )
	{
		return htmlspecialchars(trim($code));
	}

	private function escapeUrl( $url )
	{
		return urlencode($url);
	}

	private function getLinkForTagResultList( $tag )
	{
		return JRoute::_(self::TAG_LISTING_SITE . $this->escapeUrl($tag));
	}

	function showReleatedArticlesByTags($articleId,$termIds){
		$count=JoomlaTagsHelper::param('RelatedArticlesCountByTags',10);		
		$relatedArticlesTitle=JoomlaTagsHelper::param('RelatedArticlesTitleByTags',"Related Articles");
		$max=max(intval($relatedArticlesCount),array_count_values($termIds));
		$termIds=array_slice($termIds,0,$max);
		$termIdsCondition=@implode(',',$termIds);
		//find the unique article ids
		$query=' select distinct cid from #__tag_term_content where tid in ('.$termIdsCondition.') and cid<>'.$articleId;
		$db			=& JFactory::getDBO();
		$db	->setQuery($query);

		$cids=$db->loadResultArray(0);
	
			
		$nullDate	= $db->getNullDate();
		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$where		= ' a.id in('.@implode(',',$cids).') AND a.state = 1'
		. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
		. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
		;

		// Content Items only
		$query = 'SELECT a.id,a.title, a.alias,a.access,a.sectionid, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE '. $where .' AND s.id > 0' .			
			' AND s.published = 1' .
			' AND cc.published = 1';
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();

		if(empty($rows)){
			return '';
		}
		$user =& JFactory::getUser();
		$aid = $user->get('aid', 0);

		$html = $this->insertJsCode();
		$html .= '<div class="relateditemsbytags"><h3>'.$relatedArticlesTitle.'</h3><ul class="relateditems">';

		foreach ( $rows as $row )
		{
			if($row->access <= $aid)
			{
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			} else {
				$link = JRoute::_('index.php?option=com_user&view=login');
			}
			$html.='<li> <a href="'.$link.'">'.htmlspecialchars( $row->title ).'</a></li>';
		}
		$html.='</ul></div>';
		return $html;

	}

	private function insertJsCode()
	{
		return '<script type="text/javascript">$(document).ready(function()
		{
			$(".relateditemsbytags").click(function() {
				$(this).find(".relateditems").slideDown().end().find("h3").html("Podobne artykuły:");
			}).find(".relateditems").hide();
		});</script>';
	}

	function canUserAddTags($user, $article_id)
	{
		// A user must be logged in to add attachments
		if ( $user->get('username') == '' ) {
			return false;
		}

		// If the user generally has permissions to add content, they qualify.
		// (editor, publisher, admin, etc)
		// NOTE: Exclude authors since they need to be handled separately.
		$user_type = $user->get('usertype', false);
		if ( ($user_type != 'Author') &&
		$user->authorize('com_content', 'add', 'content', 'all') ) {
			return true;
		}

		// Make sure the article is valid and load its info
		if ( $article_id == null || $article_id == '' || !is_numeric($article_id) ) {
			return false;
		}
		$db =& JFactory::getDBO();
		$query = "SELECT created_by from #__content WHERE id='" . $article_id . "'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ( count($rows) == 0 ) {
			return false;
		}
		$created_by = $rows[0]->created_by;

		//the created author can add tags.
		if($user->get('id') == $created_by){
			return true;
		}


		// No one else is allowed to add articles
		return false;
	}
	function addTagsButtonsHTML($article_id, $Itemid, $havingTags)
	{
		$document = & JFactory::getDocument();
		$document->addScript( JURI::root(true).'/media/system/js/modal.js' );
		JHTML::_('behavior.modal', 'a.modal');

		// Generate the HTML for a  button for the user to click to get to a form to add an attachment
		$url = "index.php?option=com_tag&task=add&refresh=1&article_id=".$article_id;

		$url = JRoute::_($url);
		$icon_url = JURI::Base() . 'components/com_tag/images/logo.png';

		$add_tag_txt;
		if($havingTags){
			$add_tag_txt = JText::_('EDIT TAGS');
		}else{
			$add_tag_txt = JText::_('ADD TAGS');
		}
		$ahead = '<a class="modal" type="button" href="' . $url . '" ';
		$ahead .= "rel=\"{handler: 'iframe', size: {x: 500, y: 260}}\">";
		$links = "$ahead<img src=\"$icon_url\" /></a>";
		$links .= $ahead.$add_tag_txt."</a>";
		return "\n<div class=\"addtags\">$links</div>\n";

	}
	/**
	 * Auto extract meta keywords as tags
	 *
	 * @param $article
	 * @param $isNew
	 * @return unknown_type
	 */
	function onAfterContentSave( &$article, $isNew )
	{
		$autoMetaKeywordsExtractor=$FrontPageTag=JoomlaTagsHelper::param('autoMetaKeywordsExtractor');
		if($autoMetaKeywordsExtractor){

			if($isNew){
				$tags=$article->metakey;
				$id = $article->id;
				$combined = array();
				$combined[$id]=$tags;

				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tag'.DS.'models'.DS.'tag.php');
				$tmodel = new TagModelTag();
				$tmodel->batchUpdate($combined);
			}
		}

		return true;
	}



}
//end class
?>
