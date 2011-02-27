<?php
/**
 * Scontent plugin for Joomla! 1.5
 * @package    Joomla
 * @subpackage Content Plugin
 * @license    GNU/GPL
 * @author Juan Padial <http://www.shikle.com>
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');
jimport('joomla.utilities.arrayhelper');

class plgContentScontent extends JPlugin
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
	function plgContentScontent(&$subject)
	{
		parent::__construct($subject);

			$this->plugin = &JPluginHelper::getPlugin('content', 'scontent');
			$this->params = new JParameter($this->plugin->params);		
			// load language
			JPlugin::loadLanguage('plg_content_scontent', JPATH_ADMINISTRATOR);
			
	        $document = &JFactory::getDocument();
                $uribase = 'var uribase = "'.JURI::base().'";';
                $document->addScriptDeclaration($uribase);
	}

	/**
	 * Joomla! onBeforeDisplayContent() function
	 */
	function onBeforeDisplayContent(&$article, &$params, $limitstart=0) {
			if ($this->params->get('display_content') == 0 && !$params->get( 'intro_only' )) {
				
				return $this->setTemplate($article, $params, $limitstart);
				
			} else if ($this->params->get('display_content') == 2) {
				echo $this->setTemplate($article, $params, $limitstart); 
			}		
	}

	function onPrepareContent( &$article, &$params ) {
		$check_dbtable = $this->params->get('check_dbtable');
		$drop_dbtable = $this->params->get('drop_dbtable');
		/** /CHECKING FOR DATABASE INTEGRITY **/
		if ($drop_dbtable) {
			$this->s_dropscontentTable();		
		}
				
		if ($check_dbtable) {
			$this->s_checkDatabase();		
		}
		if (isset($article->id)) {	
			if ($this->params->get('display_content') == 1) {
	    		$article->text .= '<br />'.$this->setTemplate($article, $params);
			}	
	    }
	}	
	
	function setTemplate($article, $params, $limitstart=0) {
		global $mainframe;
			$document = &JFactory::getDocument(); // set document for next usage
               
            $db = &JFactory::getDBO();
			$document->addStyleSheet(JURI::base().'plugins/content/scontent/css/scontent.css');

			//enable blog, article, fronpage or all if required
			$view  = JRequest::getVar( 'view', false );
			$layout = JRequest::getVar( 'layout', false );
			$itemID = JApplication::getItemid($article->id);
			$enable_view[0] = $this->params->get('enable_view');
			
			if ($view == 'frontpage') {$layout = 'frontpage';}
			if ($view == 'article') {$layout = 'article';}
			if ($enable_view[0] !== 'all'){			
				if (!in_array($layout, $enable_view)) return;
			} else {
				if (in_array($layout, $enable_view)) return;
			}	

			//disable section, category or article if required			
			$disablesection = explode(',', trim($this->params->get('disablesection' , '')));
			JArrayHelper::toInteger($disablesection);
	
			$disablecategory = explode(',', trim($this->params->get('disablecategory' , '')));
			JArrayHelper::toInteger($disablecategory);
	
			$disablearticle = explode(',', trim($this->params->get('disablearticle' , '')));
			JArrayHelper::toInteger($disablearticle);
		        (int)$id = $article->id;
			if ($article->sectionid && in_array($article->sectionid, $disablesection)) return;
			if ($article->catid && in_array($article->catid, $disablecategory)) return;
			if ($article->id && in_array($id, $disablearticle)) return;
			$load_jquery = $this->params->get('load_jquery','yes');
                        if($load_jquery == 'yes'){
                          $load_jquery = JURI::base().'plugins/content/scontent/js/jquery-1.4.2_min.js';
                          $document->addScript($load_jquery);
                        }

                       $ajax =JURI::base().'plugins/content/scontent/js/scontent.js';
                       $document->addScript($ajax);
                
			$use_sharingcode = $this->params->get('enable_sharing','1');
			if($use_sharingcode == 1) {
			 $article_link =  ltrim(JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid)),"/");
			 $addthis_account = $this->params->get('addthis_account','');
			 $sharingcode = '<a href="http://addthis.com/bookmark.php?v=250&amp;username='.$addthis_account.'" rel="nofollow,noindex" class="addthis_button" addthis:url="'.JURI::base().''.$article_link.'"><span class="addthis">'.JTEXT::_(SHARE).'</span></a>';
			 $addthisjs = 'http://s7.addthis.com/js/250/addthis_widget.js#username='.$addthis_account.'';
			 $document -> addScript($addthisjs);
			}

			$show_tags = $this->params->get('enable_tags','1');
			if($show_tags == 1) {
			 if($article->metakey != '') {
			  $tags = explode(',',$article->metakey);
			  $Itemid = $this->params->get('search_itemid');
			  $taglist = '<img class="tags" src="'.JURI::base().'plugins/content/scontent/images/tags.png"></img>';
			   foreach($tags as $tag) {
			    $taglist .= '<a href="'.JRoute::_('index.php?option=com_search&searchword='.$tag.'&ordering=newest&searchphrase=all&Itemid='.$Itemid.'').'">'.$tag.'</a> - ';
			   }
			   $taglist = JString::rtrim($taglist,' - ');
			 }
			}
			$query = 'SELECT *' . ' FROM #__scontent_votes' . ' WHERE content_id = ' . $id;
            		$db->setQuery($query);
		        $votes = $db->loadObjectList();

			if (!$db->query()) {
                		$msg = JText::_('PLEASE ENABLE CHECK DB TABLE OPTIONS! (SELECT YES)');
                		$total_count = 0;				
			}
                	if (!$votes) { 
                		$up_count = 0;
                		$down_count = 0;
                		$total_count = 0;
                	} else {
                		foreach ($votes as $v) {
                			$up_count = $up_count + $v->up_count;
                			$down_count = $down_count + $v->down_count;
                		}
                		$total_count = $up_count - $down_count;
                	}
                                        	   $html ='<div class="scontent">'.	
                	            '<div class="vote_result-'.$id.'">';
                	            if($total_count>=0){
		                     if($total_count==0){
		                      $html .= '<div class="totalvotes-'.$id.' neutral">'.$total_count.'</div>';
		                     } else {
                                      $html .= '<div class="totalvotes-'.$id.' up">+'.$total_count.'</div>';
                                     }
                                    } else {
                                     $html .= '<div class="totalvotes-'.$id.' down">'.$total_count.'</div>';
                                     }
                	            
                           $html .= '</div>';
                           
                           if($sharingcode!='' || $taglist!='') {						
				       $html .= '<div class="scontent_buttons">'.
				       '<p class="scu">'.
				       '<a rel="nofollow,noindex" href="#" class="voteup_button" onclick="scontent_up('.$id.'); return false;"><span class="voteup">'.JTEXT::_('VOTE UP').'</span></a>'.
				       '<a rel="nofollow,noindex" href="#" class="votedown_button" onclick="scontent_down('.$id.'); return false;"><span class="votedown">'.JTEXT::_('VOTE DOWN').'</span></a>'.
				       $sharingcode.
				       '</p><p class="scd">'.
				       $taglist.
				       '</p></div>';
		           }
		           $html .= '</div>'.
           			       '<div class="clear"></div>'.
           			       '<div class="status-'.$id.'"></div>'.
           			       '<div class="clear"></div>';  
					return $html;		
	}	

function s_checkDatabase() {
	$db  = &JFactory::getDBO();
	$query = " CREATE TABLE IF NOT EXISTS `#__scontent_votes` ( "
  			." `id` int(11) NOT NULL auto_increment, "
  			." `content_id` int(11) NOT NULL, "
  			." `user_id` int(11) NOT NULL default '0', "
  			." `up_count` int(11) NOT NULL default '0', "
  			." `down_count` int(11) NOT NULL default '0', "
  			." `last_ip` varchar(254) NOT NULL, "
  			." `date` int(11) NOT NULL, "
 	 		." PRIMARY KEY  (`id`) "
			." ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; " ;
	$db->setQuery($query);
	if($db->query()) {
		$query = " CREATE TABLE IF NOT EXISTS `#__scontent_votes_totals` ( "
  			." `content_id` int(11) NOT NULL, "
  			." `total_count` int(11) NOT NULL default '0', "
 	 		." PRIMARY KEY  (`content_id`) "
			." ) ENGINE=MyISAM DEFAULT CHARSET=utf8; " ;
	$db->setQuery($query);
	}
	
	return $db->query();
}
	
function s_dropscontentTable() {
	global $db;
	$db  = &JFactory::getDBO();
	$query = " DROP TABLE IF EXISTS #__scontent_votes,#__scontent_votes_totals";
	$db->setQuery($query);	
	return $db->query();
}
		
}
?>