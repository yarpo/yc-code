<?php
/**
 * @version 1.1 - GoogleDoc Embed Pulgin
 * 
 * @package plugins
 * @copyright Copyright (C) 2008 soeren. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die() ;

jimport( 'joomla.plugin.plugin' ) ;

class plgContentGoogleDocs extends JPlugin {
	function plgContentGoogleDocs( &$subject, $params ) {
		parent::__construct( $subject, $params ) ;
	}
	
	function onPrepareContent( &$article, &$params, $limitstart ) {
		global $mainframe ;
		;
		// simple performance check to determine whether bot should process further
		if( stristr( $article->text, '{GoogleDoc' ) === false ) {
			return true ;
		}
		
		// Get plugin info
		$plugin = & JPluginHelper::getPlugin( 'content', 'googledocs' ) ;
		$pluginParams = new JParameter( $plugin->params ) ;
		
		// define the regular expression for the bot
		$regex = '#{GoogleDoc(.*?)}#si' ;
		
		
		// check whether plugin has been unpublished
		if( ! $pluginParams->get( 'enabled', 1 ) ) {
			$article->text = preg_replace( $regex, '', $row->text ) ;
			return true ;
		}
		
		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $article->text, $matches ) ;
		
		// Number of plugins
		$count = count( $matches[0] ) ;
		
		// plugin only processes if there are any instances of the plugin in the text
		if( $count ) {
			// Get plugin parameters
			$style = $pluginParams->def( 'style', - 2 ) ;
			
			$this->plgContentProcessGoogleDocs( $article, $matches, $count, $regex, $pluginParams ) ;
		}
	
	}
	
	function plgContentProcessGoogleDocs( &$row, &$matches, $count, $regex, &$botParams ) {
		global $mainframe ;
		
		$allowed_sizes = array( 's' , 'm' , 'l' ) ;
		$allowed_types = array( 'presentation', 'spreadsheet', 'doc' ) ;
		
		$doctype = strtolower( $botParams->def( 'default_type', 'presentation' ) ) ;
		$docsize = $botParams->def( 'default_size', 's' ) ;
		$frameborder = (int)$botParams->def( 'frameborder', 0 ) ;
		$iframe_width_custom = (int)$botParams->def( 'iframe_width_custom' ) ;
		$iframe_height_custom = (int)$botParams->def( 'iframe_height_custom' ) ;
		
		for( $i = 0 ; $i < $count ; $i ++ ) {

			if( @$matches[1][$i] ) {
				$inline_params = $matches[1][$i] ;
				
				// get Google Docs ID
				$docid_matches = array() ;
				preg_match( '#docid="(.*?)"#si', $inline_params, $docid_matches ) ;
				if( isset( $docid_matches[1] ) )
					$docid = htmlspecialchars( trim( $docid_matches[1] ) ) ;
					
				// get size
				$size_matches = array() ;
				preg_match( '#size="(.*?)"#si', $inline_params, $size_matches ) ;
				if( isset( $size_matches[1] ) ) {
					if( in_array( $size_matches[1], $allowed_sizes ) )
						$thesize = $size_matches[1] ;
				} else {
					$thesize = $docsize ;
				}
				// get Google Docs Type
				$type_matches = array() ;
				preg_match( '#type="(.*?)"#si', $inline_params, $type_matches ) ;
				if( isset( $type_matches[1] ) ) {
					if( in_array( $type_matches[1], $allowed_types ) )
						$thetype = strtolower($type_matches[1]);
				} else {
					$thetype = $doctype ;
				}
				// get frameborder
				$border_matches = array() ;
				preg_match( '#frameborder="(.*?)"#si', $inline_params, $border_matches ) ;
				if( isset( $border_matches[1] ) ) {
					$frameborder = (int)$border_matches[1];
				}
				// get custom iframe height
				$height_matches = array() ;
				preg_match( '#height="(.*?)"#si', $inline_params, $height_matches ) ;
				if( isset( $height_matches[1] ) ) {
					$iframe_height = strtolower($height_matches[1]);
				}
				// get custom iframe width
				$width_matches = array() ;
				preg_match( '#width="(.*?)"#si', $inline_params, $width_matches ) ;
				if( isset( $width_matches[1] ) ) {
					$iframe_width = strtolower($width_matches[1]);
				}
			}

			if( !empty( $iframe_width_custom )) {
				$iframe_width = $iframe_width_custom;
			}
			if( !empty( $iframe_height_custom )) {
				$iframe_height = $iframe_height_custom;
			}
			$extra_url_params = '';
			switch( $thetype ) {
				case 'presentation':
					$uri = 'docs.google.com/EmbedSlideshow';
					$ident = 'docid='.$docid;
					switch( $thesize ) {
						case 's':		
							$iframe_width = 410;
							$iframe_height = 342;
							break;
						case 'm':		
							$iframe_width = 555;
							$iframe_height = 451;
							break;
						case 'l':		
							$iframe_width = 700;
							$iframe_height = 559;
							break;
					}
					break;
					
				case 'doc':
					$uri = 'docs.google.com/View';
					$ident = 'docID='.$docid;
					break;
				case 'spreadsheet':
					$uri = 'spreadsheets.google.com/pub';
					$ident = 'key='.$docid;
					$extra_url_params = '&amp;output=html&amp;widget=true';
					break;
			}
			$text = '<iframe frameborder="'.$frameborder.'" '
						.	'src="'.(isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$uri . '?' . $ident;
			
			$text .= $extra_url_params;
			
			if( empty( $iframe_width_custom ) && empty( $iframe_width_custom )) {
				$text .= '&amp;size='.$thesize.'"';
			} else {
				$text .= '"';
			}
			$text .= ' width="'.$iframe_width.'" height="'.$iframe_height.'"></iframe>';
			
			$row->text = str_replace( $matches[0][$i], $text, $row->text ) ;
		}
	
	}
}

?>