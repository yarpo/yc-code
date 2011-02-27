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

class plgContentsysplgaup_reader2author extends JPlugin
{

	function plgContentsysplgaup_reader2author( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}


	function onAfterDisplayContent( &$article, &$params, $limitstart=0 )
	{
		$app = JFactory::getApplication();
		
		$user = & JFactory::getUser();	
		
		$authorid  = $article->created_by;
		$articleid = $article->id;
		
		if ($app->isAdmin() || $user->id==$authorid || !$articleid ) return;		
		
		$option = JRequest::getCmd('option', '');
		$view   = JRequest::getVar('view',   '');		
		
		JPlugin::loadLanguage( 'com_alphauserpoints', JPATH_SITE );
		
		switch ( $view ) {
			case 'article' :
				if ( $option=='com_content' && $limitstart==0 ) {
					
					require_once (JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php');
					
					// Rule reader to author (guest and registered)
					$authorarticle = ($article->created_by_alias) ? $article->created_by_alias : $article->author;

					$uri =& JURI::getInstance();
					
					$uri->delVar('invitekey');      // remove var used by alpharecommend pro -> no need in the url in data reference
					$uri->delVar('referreruser');   // remove var used by alphauserpoints    -> no need in the url in data reference
					$uri->delVar('keyreference');   // remove var used by alphauserpoints    -> no need in the url in data reference
					$uri->delVar('datareference');  // remove var used by alphauserpoints    -> no need in the url in data reference
					
					$url = $uri->toString();
					
					AlphaUserPointsHelper::reader2author($authorid, $authorarticle, $articleid, $article->title, $url);					
					
					// Rule read article (only for registered not guest user)
					if ( $user->id ) {
						$datareference = '<a href="' . $url . '">' . $article->title . '</a>' ;

						if ( AlphaUserPointsHelper::userpoints( 'sysplgaup_readarticle', '', 0, $articleid, $datareference, '', true )===false )
						{
							$app->redirect('index.php');
						}
					}
				}
				break;
			default:					
		}	
	}
}
?>