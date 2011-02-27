<?php
/**
* @package JFusion
* @subpackage Plugin_Discussbot
* @author JFusion development team
* @copyright Copyright (C) 2008 JFusion. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

/**
* Load the JFusion framework
*/
jimport('joomla.plugin.plugin');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.factory.php');
require_once(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_jfusion'.DS.'models'.DS.'model.jfusion.php');

JPlugin::loadLanguage( 'plg_content_jfusion', JPATH_ADMINISTRATOR );

/**
* @package JFusion
*/
class plgContentJfusion extends JPlugin
{
	var $params = false;
	var $generateContent = true;
	var $mode = '';
	var $valid = false;
	var $jname = '';
	var $creationMode = '';

	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for
	* plugins because func_get_args ( void ) returns a copy of all passed arguments
	* NOT references. This causes problems with cross-referencing necessary for the
	* observer design pattern.
	*/
    function plgContentJfusion(& $subject, $config)
    {
        parent::__construct($subject, $config);

		//retrieve plugin software for discussion bot
		if($this->params===false) {
			$jPlugin =& JPluginHelper::getPlugin('content','jfusion');
        	$this->params = new JParameter( $jPlugin->params);
		}

		$this->jname =& $this->params->get('jname',false);

   		//determine what mode we are to operate in
		if($this->params->get("auto_create")) {
			$this->mode = 'auto';
		} else {
			$this->mode = 'manual';
		}

		$this->creationMode =& $this->params->get('create_thread','load');
    }

    function onAfterContentSave(&$contentitem, $isNew) {
		//check to see if a valid $content object was passed on
		if(!is_object($contentitem)){
			JFusionFunction::raiseWarning($this->jname . ' ' . JText::_('DISCUSSBOT_ERROR'), JText::_('NO_CONTENT_DATA_FOUND'), 1);
			return false;
		}

        //make sure there is a plugin
		if(empty($this->jname)) {
			return false;
		}

		//validate the article
		$this->valid = $this->validateArticle($contentitem,true);

		if($this->mode=='auto') {
			if($this->valid) {
				$existingthread =& $this->getCreatedThread($contentitem->id);
				$JFusionForum =& JFusionFactory::getForum($this->jname);
				$forumid = $JFusionForum->getDefaultForum($this->params, $contentitem);

				if(($this->creationMode=='load') ||
					($this->creationMode=='new' && ($isNew || (!$isNew && !empty($existingthread)))) ||
					($this->creationMode=='reply' && !empty($existingthread))) {

					$status = $JFusionForum->checkThreadExists($this->params, $contentitem, $existingthread, $forumid);
					if ($status['error']) {
						JFusionFunction::raiseWarning($plugin->name . ' ' .JText::_('FORUM') . ' ' .JText::_('UPDATE'), $status['error'],1);
					}
				}
			} elseif($this->creationMode=='new' && $isNew) {
				$publishUp = strtotime($contentitem->publish_up);
				if(time() < $publishUp) {
					//the publish date is set for the future so create an entry in the
					//database so that the thread is created when the publish date arrives
					JFusionFunction::updateForumLookup($contentitem->id, 0, 0, 0, $this->jname);
				}
			}
		}
    }

    function onPrepareContent(&$contentitem, $options)
    {
		global $mainframe;

		//check to see if a valid $content object was passed on
		if(!is_object($contentitem)){
			JFusionFunction::raiseWarning($this->jname . ' ' . JText::_('DISCUSSBOT_ERROR'), JText::_('NO_CONTENT_DATA_FOUND'), 1);
			return false;
		}

    	//make sure there is a plugin
		if(empty($this->jname)) {
			return false;
		}

		//prevent any output by the plugins (this could prevent cookies from being passed to the header)
		ob_start();

		//set some variables needed throughout
		//form submitted to manually create thread
		$createDiscussionThread = JRequest::getInt('createThread', 0, 'post');
		$this->valid = $this->validateArticle($contentitem, $createDiscussionThread);
		$JoomlaUser =& JFactory::getUser();
		$JFusionForum =& JFusionFactory::getForum($this->jname);

		if($createDiscussionThread) {
			//make sure the article submitted matches the one loaded
			$submittedArticleId = JRequest::getInt('articleId', 0, 'post');
			$editAccess	= $JoomlaUser->authorize('com_content', 'edit', 'content', 'all');

			if($editAccess && $this->valid && $submittedArticleId == $contentitem->id) {

				//get the default forum id
				$forumid = $JFusionForum->getDefaultForum($this->params, $contentitem);

				if(!empty($forumid)) {
					$existingthread =& $this->getCreatedThread($contentitem->id);
				    $status = $JFusionForum->checkThreadExists($this->params, $contentitem, $existingthread, $forumid);
				    if ($status['error']) {
			    	    JFusionFunction::raiseWarning($plugin->name . ' ' .JText::_('FORUM') . ' ' .JText::_('UPDATE'), $status['error'],1);
			    	} else {
			    		//get the updated thread info
			    		$existingthread =& $this->getCreatedThread($contentitem->id, true);

						//add the plugin to the end of the fulltext and save it
						if(!empty($existingthread) && $this->mode == 'manual') {
							$contentitem->fulltext = $contentitem->fulltext . "{jfusion_discuss ".$existingthread->threadid."}";
							$jdb =& JFactory::getDBO();
							$query = "UPDATE #__content SET `fulltext` = " . $jdb->Quote($contentitem->fulltext) . " WHERE id = " . (int) $contentitem->id;
							$jdb->setQuery($query);

							$url = ContentHelperRoute::getArticleRoute($contentitem->slug, $contentitem->catslug, $contentitem->sectionid);
							//take into account page breaks;
							if(JRequest::getInt('start',0)) {
								$url .= "&start=".JRequest::getInt('start');
							}
							$url = JRoute::_($url);

							if(!$jdb->query()) {
								$mainframe->redirect($url, $this->jname . ' ' . JText::_('DISCUSSBOT_ERROR').': '. JText::sprintf('UNABLE_TO_UPDATE_CONTENT',$existingthread->threadid) ."<br />".$jdb->stderr(), 'error');
							} else {
								$mainframe->redirect($url, JText::_('THREAD_CREATED_SUCCESSFULLY'));
							}
						}
			    	}
				}
			}
		}

		//define some variables
		$allowGuests =& $this->params->get("quickreply_allow_guests",0);

		//process quick replies
		if(($allowGuests || !$JoomlaUser->guest) && JRequest::getVar('jfusionForm'.$contentitem->id, false, 'POST')!==false && $this->params->get("enable_quickreply",false))	{
			$action = JRequest::getVar('action',false, 'POST');
			if(!empty($action)){
				//retrieve the userid from forum software
				if($allowGuests && $JoomlaUser->guest) {
					$userinfo = new stdClass();
					$userinfo->guest = 1;

					$captcha_verification = $JFusionForum->verifyCaptcha($this->params);
				} else {
					$JFusionUser =& JFusionFactory::getUser($this->jname);
					$userinfo = $JFusionUser->getUser($JoomlaUser);
					$userinfo->guest = 0;
					//we have a user logged in so ignore captcha
					$captcha_verification = true;
				}

				if($captcha_verification) {
					if($action=='createThreadPost') {
						//thread has to be created first
						$forumid = JRequest::getVar('forumid',0, 'POST');
						if(empty($forumid)) {
							//for some reason the forumid didn't make it through so let's get it again
							$forumid = $JFusionForum->getDefaultForum($this->params, $contentitem);
						}

						$postedThread = '';
						$status = $JFusionForum->checkThreadExists($this->params, $contentitem, $postedThread, $forumid);
					    if ($status['error']) {
				    	    JFusionFunction::raiseWarning($plugin->name . ' ' .JText::_('FORUM') . ' ' .JText::_('UPDATE'), $status['error'],1);
				    	} else {
				    		//get the updated thread info
				    		$postedThread =& $this->getCreatedThread($contentitem->id, true);
				    	}
					} elseif($action=="createPost") {
						$postedThread = new stdClass();
						$postedThread->threadid = JRequest::getVar('threadid',0, 'POST');
						$postedThread->forumid = JRequest::getVar('forumid',0, 'POST');
						$postedThread->postid = JRequest::getVar('postid',0, 'POST');
					}

					//create the post
					if(!empty($postedThread->threadid)) {
						$status = $JFusionForum->createPost($this->params, $postedThread, $contentitem, $userinfo);
						if($status['error']){
							JFusionFunction::raiseWarning($this->jname . ' ' . JText::_('DISCUSSBOT_ERROR'), $status['error'],1);
						} else {
							$url = ContentHelperRoute::getArticleRoute($contentitem->slug, $contentitem->catslug, $contentitem->sectionid);
							//take into account page breaks;
							if(JRequest::getInt('start',0)) {
								$url .= "&start=".JRequest::getInt('start');
							}
							//add post jump
							if(isset($status['postid'])) {
								$url .= "#post" . $status['postid'];
							}
							$url = JRoute::_($url);

							global $mainframe;
							$mainframe->redirect($url, JText::_('SUCCESSFUL_POST'));
						}
					} else {
						JFusionFunction::raiseWarning($this->jname . ' ' . JText::_('DISCUSSBOT_ERROR'), JText::_('THREADID_NOT_FOUND'),1);
					}
				}
			}
		}

		//generate content
		$this->prepareContent($contentitem);

		ob_end_clean();
		return true;
    }

    function prepareContent(&$contentitem)
    {
		//get the jfusion forum object
		$JFusionForum =& JFusionFactory::getForum($this->jname);

    	//load CSS
		static $cssLoaded;
		if(empty($cssLoaded)) {
			$document =& JFactory::getDocument();
			$document->addStyleSheet(JFusionFunction::getJoomlaUrl().'plugins/content/jfusion.css');
			$cssLoaded = 1;
		}

    	//find any {jfusion_discuss...} to manually plug
		preg_match_all('/\{jfusion_discuss (.*)\}/U',$contentitem->text,$matches);
		$manually_plugged = false;
		foreach($matches[1] AS $id)
		{
			//get the existing thread information
			$existingthread = $JFusionForum->getThread($id);
			if(!empty($existingthread)) {
				$content = $this->createContent($contentitem, $existingthread);
				$contentitem->text = str_replace("{jfusion_discuss $id}",$content,$contentitem->text);
			} else {
				$contentitem->text = str_replace("{jfusion_discuss $id}",JText::_("THREADID_NOT_FOUND"),$contentitem->text);
			}

			$manually_plugged = true;
		}

		//check for auto mode if not manually plugged or show initiate button if applicable
		if(!$manually_plugged) {
			//get the existing thread information
			$existingthread =& $this->getCreatedThread($contentitem->id);

			//create the thread if this article has been validated
			if($this->mode=='auto') {
				if($this->valid) {
					//get the default forum id
					$forumid = $JFusionForum->getDefaultForum($this->params, $contentitem);

					if(!empty($forumid)) {
					    $status = $JFusionForum->checkThreadExists($this->params, $contentitem, $existingthread, $forumid);
					    if ($status['error']) {
				    	    JFusionFunction::raiseWarning($plugin->name . ' ' .JText::_('FORUM') . ' ' .JText::_('UPDATE'), $status['error'],1);
				    	} else {
				    		//get the updated thread info
				    		$existingthread =& $this->getCreatedThread($contentitem->id, true);
				    	}
					}
				}

				$content = $this->createContent($contentitem, $existingthread);
				$contentitem->text .= $content;
		    }

		    //create a link to manually create the thread if it is not already
		    //$show_button = $this->params->get('enable_manual_button',false);
		    $show_button = false; //disabled for 1.1.x branch
		    if($show_button) {
			    $user	=& JFactory::getUser();
				$editAccess	= $user->authorize('com_content', 'edit', 'content', 'all');
			    if(empty($existingthread) && $editAccess) {
			    	static $jsLoaded;

			    	if(empty($jsLoaded)) {
			    		$js  = "function confirmThreadCreation(id) {\n";
			    		$js .= "var answer = confirm(\"".JText::_('CONFIRM_THREAD_CREATION')."\");\n";
			    		$js .= "if(answer) { \n";
						$js .= "var frm = $('dbArticle'+id);";
						$js .= "frm.submit();\n";
			    		$js .= "}\n";
			    		$js .= "}\n";

			    		$document =& JFactory::getDocument();
						$document->addScriptDeclaration($js);
						$jsLoaded = 1;
			    	}

					$initiate  = "<form id='dbArticle{$contentitem->id}' action='".JRoute::_(ContentHelperRoute::getArticleRoute($contentitem->slug, $contentitem->catslug, $contentitem->sectionid))."' method='post'>\n";
					$initiate .= "<input type='hidden' name='createThread' value='1' />\n";
					$initiate .= "<input type='hidden' name='articleId' value='{$contentitem->id}' />\n";
					$initiate .= "</form\n";
					$initiate .= "<div class=\"button2-left\"><div class=\"blank\"><a href='javascript: void(0);' onclick='confirmThreadCreation(".$contentitem->id.")'>".JText::_('CREATE_THREAD_LINK')."</a></div></div><br /><br /><br />\n";
					$contentitem->text = $initiate . $contentitem->text;
			    }
		    }
		}
    }

    function createContent(&$contentitem, &$existingthread)
    {
		//setup parameters
		$JFusionForum =& JFusionFactory::getForum($this->jname);
		$link_text =& $this->params->get("link_text");
		$link_type=& $this->params->get("link_type",'text');
		$link_mode=& $this->params->get("link_mode",'always');
		$blog_link_mode=& $this->params->get("blog_link_mode",'forum');
		$linkHTML = ($link_type=='image') ? "<img src='$link_text'>" : $link_text;
		$linkTarget =& $this->params->get('link_target','_parent');
		$itemid =& $this->params->get("itemid");
		$noPostMsg =& $this->params->get("no_posts_msg");
		$mustLoginMsg =& $this->params->get("must_login_msg");
		$show_reply_num =& $this->params->get("show_reply_num");
		$allowGuests =& $this->params->get("quickreply_allow_guests",0);
		$defaultCSS =& $this->params->get("default_css",1);
		$enablePagination =& $this->params->get("enable_pagination",1);
		$JoomlaUser =& JFactory::getUser();
		//make sure the user exists in the software before displaying the quick reply
		$JFusionUser =& JFusionFactory::getUser($this->jname);
		$JFusionUserinfo = $JFusionUser->getUser($JoomlaUser);
		if(!empty($existingthread)) {
			$numPosts = $JFusionForum->getReplyCount($existingthread);
		}

		$view = JRequest::getVar('view');

		$content = "<div style='float:none; display:block;'>";

		if(!empty($existingthread) && $link_mode!="never") {
			if($show_reply_num) {
				$post = ($numPosts==1) ? "REPLY" : "REPLIES";
				$replyNum = '['.$numPosts.' '.JText::_($post).']';
			} else {
				$replyNum = '';
			}

			if($view=="article") {
				if($link_mode=="article" || $link_mode=="always") {
					$threadid =& $existingthread->threadid;
					$urlstring_pre = JFusionFunction::routeURL($JFusionForum->getThreadURL($threadid), $itemid, $this->jname);
				}
			} elseif($link_mode=="blog" || $link_mode=="always") {
				if($blog_link_mode=="joomla") {
					$urlstring_pre = JRoute::_(ContentHelperRoute::getArticleRoute($contentitem->slug, $contentitem->catslug, $contentitem->sectionid)).'#discussion';
				} else {
					$threadid =& $existingthread->threadid;
					$urlstring_pre = JFusionFunction::routeURL($JFusionForum->getThreadURL($threadid), $itemid, $this->jname);
				}
			}

			if(!empty($urlstring_pre)) {
				$content .= '<div class="jfusionThreadLink"><a href="'. $urlstring_pre . '" target="' . $linkTarget . '">' . $linkHTML . '</a> '.$replyNum.'</div>';
			}
		}

		//let's only show quick replies and posts on the article view
		if($view=="article") {

			//take into account page breaks
			$actionUrl = ContentHelperRoute::getArticleRoute($contentitem->slug, $contentitem->catslug, $contentitem->sectionid);
			if(JRequest::getInt('start',0)) {
				$actionUrl .= "&start=".JRequest::getInt('start');
			}
			$actionUrl = JRoute::_($actionUrl);

			$content .= "<a name='discussion'>\n";

			if(!empty($existingthread)) {
				//prepare quick reply box if enabled
				if($this->params->get("enable_quickreply")){
					$show = ($allowGuests || (!$JoomlaUser->guest && !empty($JFusionUserinfo))) ? "form" : "message";
					$replyForm  = "<div class='jfusionQuickReplyHeader'>{$this->params->get("quick_reply_header")}</div>\n";
					$replyForm .= "<div class='jfusionQuickReply'>\n";
				} else {
					$show = false;
				}

				if(!$JoomlaUser->guest && empty($JFusionUserinfo)) {
					$replyForm .=  $this->jname . ': ' . JText::_('USER_NOT_EXIST')."\n";
					$replyForm .= "</div>\n";
				} elseif($show=="form") {
					$replyForm .= "<form name='jfusionQuickReply{$contentitem->id}' method=post action='".$actionUrl."'>\n";
					$replyForm .= "<input type=hidden name='jfusionForm{$contentitem->id}' value='1'/>\n";
					$replyForm .= "<input type=hidden name='threadid' value='{$existingthread->threadid}'/>\n";
					$replyForm .= "<input type=hidden name='forumid' value='{$existingthread->forumid}'/>\n";
					$replyForm .= "<input type=hidden name='postid' value='{$existingthread->postid}'/>\n";
					$replyForm .= "<input type=hidden name='action' value='createPost'>\n";

					$showGuestInputs = ($allowGuests && $JoomlaUser->guest) ? true : false;
					$replyForm .= $JFusionForum->createQuickReply($this->params,$showGuestInputs)."</form>\n";
					$replyForm .= "</div>\n";
				} elseif($show=="message") {
					$replyForm .= $mustLoginMsg;
					$replyForm .= "</div>\n";
				}

				//add posts to content if enabled
				if($this->params->get("show_posts")) {
					//get the posts
					$posts = $JFusionForum->getPosts($this->params, $existingthread);
					$content .= "<div class='jfusionPostArea'> \n";

					if($show!==false && $this->params->get("quickreply_location")=="above") {
						$content .= $replyForm;
					}

					if(!empty($posts)){
						$content .= $JFusionForum->createPostTable($this->params, $existingthread, $posts);

						if($enablePagination && is_array($posts)) {
							$content .= "<form method='post' name='adminForm' action='$actionUrl'>";

							$application = JFactory::getApplication() ;
							$limitstart = JRequest::getInt( 'limitstart', 0 );
							$limit = $application->getUserStateFromRequest( 'global.list.limit', 'limit', 14, 'int' );
							jimport('joomla.html.pagination');
							$pageNav = new JPagination($numPosts, $limitstart, $limit );
							$content .= $pageNav->getListFooter();

							$content .= "</form><br />";
						}
					} elseif(!empty($noPostMsg)) {
						$content .= "<div class='jfusionNoPostMsg'> {$noPostMsg} </div>\n";
					}

					if($show!==false && $this->params->get("quickreply_location")=="below"){
						$content .= $replyForm;
					}

					$content .= "</div> \n";
				} elseif($show!==false){
					$content .= $replyForm;
				}
			} elseif($this->creationMode=='reply') {
				//prepare quick reply box if enabled
				if($this->params->get("enable_quickreply")){
					$show = ($allowGuests || (!$JoomlaUser->guest && !empty($JFusionUserinfo))) ? "form" : "message";
					$replyForm  = "<div class='jfusionQuickReplyHeader'>{$this->params->get("quick_reply_header")}</div>\n";
					$replyForm .= "<div class='jfusionQuickReply'>\n";
				} else {
					$show = false;
				}

				if(!$JoomlaUser->guest && empty($JFusionUserinfo)) {
					$replyForm .=  $this->jname . ': ' . JText::_('USER_NOT_EXIST')."\n";
					$replyForm .= "</div>\n";
				} elseif($show=="form") {
					$replyForm .= "<form name='jfusionQuickReply{$contentitem->id}' method=post action='".$actionUrl."'>\n";
					$replyForm .= "<input type=hidden name='jfusionForm{$contentitem->id}' value='1'/>\n";
					$replyForm .= "<input type=hidden name='forumid' value=''/>\n";
					$replyForm .= "<input type=hidden name='action' value='createThreadPost'/>\n";
					$showGuestInputs = ($allowGuests && $JoomlaUser->guest) ? true : false;
					$replyForm .= $JFusionForum->createQuickReply($this->params,$showGuestInputs)."</form>\n";
					$replyForm .= "</div>\n";
				} elseif($show=="message") {
					$replyForm .= $mustLoginMsg;
					$replyForm .= "</div>\n";
				}

				if($show!==false) {
					$content .= $replyForm;
				}
			}
		}
		$content .= "</div>";
		return $content;
    }

	function validateArticle(&$contentitem,$skip_new_check=false)
	{
		//make sure we have an article
		if(!$contentitem->id) {
			return false;
		}

		//make sure there is a default user set
		if($this->params->get("default_userid",false)===false) {
			return false;
		}

		//make sure article is published
		if(!$contentitem->state) {
			return false;
		}

		//make sure the article is set to be published
		$publishUp = strtotime($contentitem->publish_up);
		$currentTime = JFactory::getDate()->toUnix();

		if($currentTime < $publishUp) {
			return false;
		}

		//make sure create_thread is appropriate
		if($this->creationMode=='reply') {
			return false;
		} elseif($this->creationMode=='view') {
			//only create the article if we are in the article view
			if(JRequest::getVar('view') != 'article') {
				return false;
			}
		} elseif($this->creationMode == 'new' && !$skip_new_check) {
			//if set to create a thread for new articles only, make sure the thread was created with onAfterContentSave
			$existingthread =& $this->getCreatedThread($contentitem->id);
			if(empty($existingthread)) {
				return false;
			}
		}

    	$forumid = $this->params->get("default_forum",false);
		$sectionPairs = $this->params->get("pair_sections",false);
		$categoryPairs = $this->params->get("pair_categories",false);
		//section and category id of content
		$secid =& $contentitem->sectionid;
		$catid =& $contentitem->catid;

		//check to see if we have an uncategorized article
		if(empty($secid) && empty($catid)) {
			//does the admin want a thread generated?
			if($this->params->get('include_static',false) && $forumid) {
				return true;
			} else {
				return false;
			}
		}

		//first we need to check to see if we at least one forum to work with
		if($forumid || $sectionPairs || $categoryPairs) {
	    	//check to see if there are sections/categories that are specifically included/excluded
	    	$sections =& $this->params->get("include_sections");
			$includeSections = empty($sections) ? false : explode(",",$sections);

			$categories =& $this->params->get("include_categories");
			$includeCategories = empty($categories) ? false : explode(",",$categories);

			$sections =& $this->params->get("exclude_sections");
			$excludeSections = empty($sections) ? false : explode(",",$sections);

			$categories =& $this->params->get("exclude_categories");
			$excludeCategories = empty($categories) ? false : explode(",",$categories);

			//there are section stipulations on what articles to include
			if($includeSections) {
				if($includeCategories) {
					//there are both specific sections and categories to include
					//check to see if this article is not in the selected sections and categories
					if(!in_array($secid,$includeSections) && !in_array($catid,$includeCategories)) $valid = false;
				} elseif($excludeCategories) {
					//exclude this article if it is in one of the excluded categories
					if(in_array($catid,$excludeCategories)) $valid = false;
				} elseif(in_array($secid,$includeSections)) {
					//there are only specific sections to include with no category stipulations
					$valid = true;
				} else  {
					//this article is not in one of the sections to include
					$valid = false;
				}
			} elseif($includeCategories) {
				//there are category stipulations on what articles to include but no section stipulations
		        //check to see if this article is not in the selected categories
				$valid = (!in_array($catid,$includeCategories)) ? false : true;
			} elseif($excludeSections) {
			    //there are section stipulations on what articles to exclude
				//check to see if this article is in the excluded sections
				$valid = (in_array($secid,$excludeSections)) ? false : true;
			} elseif($excludeCategories) {
				//there are category stipulations on what articles to exclude but no exclude stipulations on section
				//check to see if this article is in the excluded categories
				$valid = (in_array($catid,$excludeCategories)) ? false : true;
			} elseif($forumid!==false) {
				$valid = true;
			} else {
				$valid = false;
			}
		} else {
			$valid = false;
		}

		return $valid;
	}

	function getCreatedThread($contentid, $update = false) {
		static $thread_instance;

		if(!is_array($thread_instance)) {
			$thread_instance = array();
		}

		if(empty($thread_instance) || !isset($thread_instance[$contentid]) || $update) {
			$db =& JFactory::getDBO();
        	$query = "SELECT * FROM #__jfusion_forum_plugin WHERE contentid = '$contentid' AND jname = '$this->jname'";
        	$db->setQuery($query);
        	$thread_instance[$contentid] = $db->loadObject();
		}

        return $thread_instance[$contentid];
	}
}
?>