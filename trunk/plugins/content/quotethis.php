<?php
/**
 * @version		$Id: quothis.php 10094 2009-10-01  $
 * @package		QuotThis
 * @copyright	Copyright (C) 2008-2009 - Bernard Gilly. All rights reserved.
 * @license		GNU/GPL
 * @Website     http://www.alphaplug.com
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentQuotethis extends JPlugin
{

	function plgContentQuotethis( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;
		
		$document	= & JFactory::getDocument();
		$view		= JRequest::getCmd('view');
		
		if ( $view != 'article' ) return;		

		// Get plugin info
		$plugin              =& JPluginHelper::getPlugin('content', 'quotethis');
		$pluginParams        = new JParameter( $plugin->params );
		$excludeSectionID    = $pluginParams->get( 'excludeSectionID',  '' );		
		$excludeCategoryID   = $pluginParams->get( 'excludeCategoryID', '' );
		$excludeID           = $pluginParams->get( 'excludeID',         '' );		
		$listexcludeSection  = @explode ( ",",  $excludeSectionID );	
		$listexcludeCategory = @explode ( ",", $excludeCategoryID );	
		$listexclude 		 = @explode ( ",",         $excludeID );		
		
		if ( $params->get( 'intro_only' ) || in_array ( $article->id, $listexclude ) || in_array ( $article->sectionid, $listexcludeSection ) || in_array ( $article->catid, $listexcludeCategory ) ) return;	
		
		JPlugin::loadLanguage( 'plg_quotethis', JPATH_ADMINISTRATOR );
		
		$loadMootools	 = $pluginParams->def( 'loadMootools',           1 );
		$preview   		 = $pluginParams->def( 'preview',                1 );
		$txtcols         = $pluginParams->def( 'txtcols',               54 );
		$txtrows         = $pluginParams->def( 'txtrows',                7 );	
		$rowtitle        = $pluginParams->def( 'title',            'title' );		
		$intro           = $pluginParams->def( 'intro',                '0' );
		$limitcharintro  = $pluginParams->def( 'limitcharintro',     '150' );
		$showfirstimage  = $pluginParams->def( 'showfirstimage',       '0' );
		$widthimage		 = $pluginParams->def( 'widthimage',         '120' );		
		$copyright       = $pluginParams->def( 'copyright',            '1' );
		$showdate        = $pluginParams->def( 'showdate',             '1' );	
		$width           = $pluginParams->def( 'width',                400 );
		$colorborder     = $pluginParams->def( 'colorborder',     '456B8F' );
		$backgroundcolor = $pluginParams->def( 'backgroundcolor', 'FFFFFF' );
		$fontsizetitle   = $pluginParams->def( 'fontsizetitle',       '13' );
		$colortitle      = $pluginParams->def( 'colortitle',      '003399' );
		$colortitlehover = $pluginParams->def( 'colortitlehover', 'FF9900' );
		$fontsize        = $pluginParams->def( 'fontsize',            '10' );
		$fontcolor       = $pluginParams->def( 'fontcolor',       '222222' );			
		
		if ( $loadMootools ) $document->addScript(JURI::base(true).'/plugins/content/quotethis/js/mootools.js');				
		
		$uri             =& JURI::getInstance();
		$base            = $uri->toString( array('scheme', 'host', 'port'));		
		$thequote        = $base.JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));		

		$copyNow         = date('Y');
		$title           = stripslashes( $article->$rowtitle );
		$copyright       = ( $copyright ) ? "&copy; " . $copyNow . " - " : "" ;
		$thedate         = ( $showdate )  ? "<br />" . JHTML::_( 'date', $article->created, JText::_('DATE_FORMAT_LC')) : "" ;
		$introtext		 = ( $intro )     ? "<br />" . plgContentQuotethis::prepareIntroQuoteThis( $article->text, $limitcharintro, "" ) : "" ;
		$image 			 = "";
		$getimage		 = "";
		
		if ( $showfirstimage ) {
			$getimage = plgContentQuotethis::findIMGquotethis( $article->text );
			if ( $getimage ) {
				$image = "<div style=\"float:left;padding:5px;\"><img src=\"" . $getimage . "\" width=\"" . $widthimage . "\" alt=\"\" /></div>";
			}
		}
		
		$style4quote = "<style type=\"text/css\"><!--"
		.".quote {width:".$width."px; padding: 6px; border: solid 1px #".$colorborder."; font: ".$fontsize."px helvetica, verdana, sans-serif; color: #".$fontcolor."; background-color: #".$backgroundcolor."}"
		.".quote a {font: ".$fontsizetitle."px arial, serif; color: #".$colortitle."; text-decoration: underline}"
		.".quote a:hover {color: #".$colortitlehover."; }"
		."//--></style>";

		$javascript = "\n<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n"
			."window.addEvent('domready', function(){\n"
			."var mySlide = new Fx.Slide('quotethisexpand').hide();	\n"
			."$('toggle').addEvent('click', function(e){\n"
			."e = new Event(e);\n"
			."mySlide.toggle();\n"
			."e.stop();\n"
			."});\n"
			."}); \n//-->"
			."</script>\n";
			
		if ( $preview ) $document->addCustomTag( $style4quote );
			
		$html  = $javascript;		
		$html .= "<a id=\"toggle\" href=\"#\">" . JText::_('QUOTETHISARTICLEONYOURSITE') . "</a>";
		$html .= "<br /><div id=\"quotethisexpand\"><br />";
		$html .= "<strong>".JText::_('CREATELINKTOWARDSTHISARTICLE')."</strong>";
		$html .= "<br /><br />";		
		$html .= "<textarea name=\"textarea\" cols=\"$txtcols\" rows=\"$txtrows\">".$style4quote.$image."<div class=\"quote\">"
		."<a href=\"" . $thequote ."\" target=\"_blank\">" . $title . "</a>" . $thedate . $introtext
		."<div align=\"right\" style=\"width:".$width."px\"><p style=\"text-align:right;\">".$copyright."<a href=\"".JURI::base()."\" target=\"_blank\">".$mainframe->getCfg('sitename')."</a></p></div></div></textarea>";
		$html .= "<br /><br />";
		
		if ( $preview ) {
			$html .= JText::_('PREVIEWQUOTE');
			$html .= "<br /><br />";
			$html .= $style4quote;
			$html .= $image . "<div class=\"quote\">\n<a href=\"$thequote\" target=\"_blank\">$title</a>"
			.$thedate.$introtext
			."\n<div align=\"right\" style=\"width:".$width."px\"><p style=\"text-align:right;\">$copyright<a href=\"".JURI::base()."\" target=\"_blank\">".$mainframe->getCfg('sitename')."</a></p></div></div>";
			$html .= "<br />";		
		}
		$html .= "<div class=\"small\" align=\"center\">";
		// This software is copyrighted: don't remove the copyright notice
		$html .= "Powered by <a href=\"http://www.alphaplug.com\">QuoteThis</a> &copy; 2008";
		$html .= "</div>";
		$html .= "</div>";
			
		$article->text = $article->text . $html;

	}
	
	function prepareIntroQuoteThis( $text, $length=150, $tags='' ) {
		// strips tags won't remove the actual jscript
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = preg_replace( '/{.+?}/', '', $text);
		// replace line breaking tags with whitespace
		$text = preg_replace( "'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text );
	
		return html_entity_decode(plgContentQuotethis::smartSubstrQuoteThis( strip_tags( $text, $tags ), $length ));
	}
	
	function smartSubstrQuoteThis($text, $length=150) {
		if ( strlen($text) > $length ) {     
			$text = substr( $text, 0, $length );
			$blankpos = strrpos( $text, ' ' );    
			$text = substr( $text, 0, $blankpos );    
			$text .= "...";
		}  
		return $text;  
	}
	
	function findIMGquotethis( $contenttext ) {	
		$image = "";
		if ( preg_match_all('#src="(.*)"#Uis', $contenttext, $match ) ) {
			if ( count($match) ) {
				$image = $match[1][0];
			}
		}
		return $image;
	}	
}
?>