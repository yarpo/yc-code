<?php
/*
 * @component AlphaUserPoints
 * @copyright Copyright (C) 2008-2010 Bernard Gilly
 * @license : GNU/GPL
 * @Website : http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * AlphaUserPoints Content Plugin
 *
 * @package		Joomla
 * @subpackage	AlphaUserPoints
 * @since 		1.5
 */

class plgContentsysplgaup_content extends JPlugin
{

	function plgContentsysplgaup_content( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}


	function onPrepareContent( &$article, &$params, $limitstart )
	{
		$app = JFactory::getApplication();
		
		$user 	= & JFactory::getUser();
		
		$authorid  = $article->created_by;
		$articleid = $article->id;
		
		$option = JRequest::getCmd('option', '');
		$view   = JRequest::getVar('view',   '');
		$print	= JRequest::getVar('print');
		$format	= JRequest::getVar('format');
		
		if ($app->isAdmin()) return;
		
		if ( !$user->id || $print || $format=='pdf' || !$articleid ) {
			$article->text = preg_replace( " |{AUP::CONTENT=(.*)}| ", "", $article->text );
			$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
			$article->text = preg_replace( " |{AUP::CONTENTAUTHOR=(.*)}| ", "", $article->text );
			return;
		}
		
		JPlugin::loadLanguage( 'com_alphauserpoints' );
		require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');	
		
		// ******************************************
		// * add/remove points to read this article *
		// ******************************************
		if ( preg_match('#{AUP::CONTENT=(.*)}#Uis', $article->text, $m) )
		{
				// not available for the author of this article
				if ( $user->id==$authorid  ) return;
				 
				$points = $m[1];
				
				$uri =& JURI::getInstance();
				$url = $uri->toString();
				
				$datareference = '<a href="' . $url . '">' . $article->title . '</a>' ;
				
				// com_content
				if ( $option=='com_content') {
					switch ( $view ) {
						case 'article' :
							if ( $option=='com_content' && $limitstart==0 ) {						
								// Rule content plugin
								if ( AlphaUserPointsHelper::userpoints( 'sysplgaup_content', '', 0, $articleid, $datareference, $points, true )===false )
								{
									$app->redirect('index.php');
								}							
							}
							break;
						default:
					}
				} else {
					// other components
					if ( !$article->title || $article->title==NULL ) $article->title = $option;
					if ( AlphaUserPointsHelper::userpoints( 'sysplgaup_content', '', 0, $articleid, $datareference, $points, true )===false )
					{
						$app->redirect('index.php');
					}			
				}
				
				$article->text = preg_replace( " |{AUP::CONTENT=(.*)}| ", "", $article->text );
		}
		
		
		// ********************************************************
		// * Paid points to the author to allow read this article *
		// ********************************************************
		if ( preg_match('#{AUP::CONTENTAUTHOR=(.*)}#Uis', $article->text, $m) )
		{
				// not available for the author of this article
				if ( $user->id==$authorid  ) return;
				 
				$points = $m[1];
				
				$uri =& JURI::getInstance();
				$url = $uri->toString();
				
				$datareference = '<a href="' . $url . '">' . $article->title . '</a>' ;				
				
				// get refer ID of the author of this article
				$author_referreid = AlphaUserPointsHelper::getAnyUserReferreID($article->created_by);
				
				if ( $author_referreid ) 
				{
					// com_content
					if ( $option=='com_content') {
						switch ( $view ) {
							case 'article' :
								if ( $option=='com_content' && $limitstart==0 ) {						
									// Rule content author plugin
									// remove points to the reader
									if ( AlphaUserPointsHelper::userpoints( 'sysplgaup_contentauthor', '', 0, $articleid, JText::_( 'AUP_POINTSGIVENTO') . ' ' . $article->author . ' (' . $datareference. ')', $points*(-1), true )===false )
									{
										$app->redirect('index.php');
									} else {
										// donate points to the author
										AlphaUserPointsHelper::userpoints( 'sysplgaup_contentauthor', $author_referreid, 0, $articleid,  JText::_( 'AUP_THESEPOINTSWEREDONATEDBY'). ' ' . $user->username . ' (' . $datareference. ')', $points);
									}						
								}
								break;
							default:
						}
					} else {
						// other components
						if ( !$article->title || $article->title==NULL ) $article->title = $option;
						// remove points to the reader
						if ( AlphaUserPointsHelper::userpoints( 'sysplgaup_contentauthor', '', 0, $articleid, $datareference, $points*(-1), true )===false )
						{
							$app->redirect('index.php');
						} else {
							// donate points to the author
							AlphaUserPointsHelper::userpoints( 'sysplgaup_contentauthor', $author_referreid, 0, $articleid,  JText::_( 'AUP_THESEPOINTSWEREDONATEDBY') . $user->username . ' (' . $datareference. ')', $points);
						}			
					}
				}
				$article->text = preg_replace( " |{AUP::CONTENTAUTHOR=(.*)}| ", "", $article->text );
		}
		
		
		// *******************************************
		// * show current points of the current user *
		// *******************************************
		if ( preg_match('#{AUP::SHOWPOINTS}#Uis', $article->text, $m) )
		{
			$show = $m[0];
			if ( $show && @$_SESSION['referrerid'] ) {
				$currentpoints = AlphaUserPointsHelper::getCurrentTotalPoints( @$_SESSION['referrerid'] );
				if ( !$article->title || $article->title==NULL ) $article->title = $option;					
				$article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", $currentpoints, $article->text );
			} else $article->text = preg_replace( " |{AUP::SHOWPOINTS}| ", "", $article->text );
		} 
		
	}
}
?>