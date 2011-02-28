<?php
/**
* @version		1.05
* @package		Blog Calendar Reload
* @author		Juan Padial
* @authorweb	http://www.shikle.com
* @license		GNU/GPL
*
* modified from the icon.php file of the Blog Calendar 1.2.2.1 component by Justo Gonzalez de Rivera
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
class JHTMLIcon
{
	function create($article, $params, $access, $attribs = array())
	{
		
		$user =& JFactory::getUser();

		$url = 'index.php?task=new&id=0&sectionid='.$article->sectionid.'&option=com_content';

			$text = JHTML::_('image.site', 'new.png', '/images/M_images/', NULL, NULL, JText::_('New') );


		$attribs	= array( 'title' => JText::_( 'New' ));
		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	function pdf($article, $params, $access, $attribs = array())
	{
		
		if(!$params->get('show_pdf_icon')){ return; }
		
		$user =& JFactory::getUser();
		
		$url  = 'index.php?view=article;';
		$url .=  $article->catid ? '&catid='.$article->catid : '';
		$url .= '&id='.$article->id.'&format=pdf'.'&option=com_content' ;
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
			$text = JHTML::_('image.site', 'pdf_button.png', '/images/M_images/', NULL, NULL, JText::_('PDF'));
		} else {
			$text = JText::_('PDF').'&nbsp;';
		}

		$attribs['title']	= JText::_( 'PDF' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']     = 'nofollow';

		return JHTML::link("$url", $text, $attribs);
	}

	function email($article, $params, $access, $attribs = array())
	{
		if(!$params->get('show_email_icon')){ return; }
		
		$user =& JFactory::getUser();

		//$link	= JURI::base()."index.php?view=article&id=".$article->slug;
		$uri     =& JURI::getInstance();
		$base  = $uri->toString( array('scheme', 'host', 'port'));
		$link    = $base.JRoute::_( "index.php?view=article&id=".$article->slug, false );
		$url	= 'index.php?option=com_mailto&tmpl=component&link='.base64_encode( $link );

		$status = 'width=400,height=300,menubar=yes,resizable=yes';
		
		if ($params->get('show_icons')) 	{
			$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_('Email'));
		} else {
			$text = '&nbsp;'.JText::_('Email');
		}
		
		$attribs['title']	= JText::_( 'Email' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

		$output = JHTML::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

	function edit($article, $params, $access, $attribs = array())
	{
		$user =& JFactory::getUser();

		

		if ($article->state < 0) {
			return;
		}

		if (!$access->canEdit && !($access->canEditOwn && $article->created_by == $user->get('id'))) {
			return;
		}

		JHTML::_('behavior.tooltip');

		$url = 'index.php?view=article&id='.$article->slug.'&task=edit'.'&option=com_content';
		$icon = $article->state ? 'edit.png' : 'edit_unpublished.png';
		$text = JHTML::_('image.site', $icon, '/images/M_images/', NULL, NULL, JText::_('Edit'));

		if ($article->state == 0) {
			$overlib = JText::_('Unpublished');
		} else {
			$overlib = JText::_('Published');
		}
		$date = JHTML::_('date', $article->created);
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

		$overlib .= '<br />';
		$overlib .= JText::_($article->groups);
		$overlib .= '<br />';
		$overlib .= $date;
		$overlib .= '<br />';
		$overlib .= $author;

		$button = JHTML::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_( 'Edit Item' ).' :: '.$overlib.'">'.$button.'</span>';
		return $output;
	}


	function print_popup($article, $params, $access, $attribs = array())
	{
		if(!$params->get('show_print_icon')){ return; }
		
		$user =& JFactory::getUser();

		$url  = JURI::base().'index.php?view=article';
		$url .=  @$article->catslug ? '&catid='.$article->catslug : '';
		$url .= '&id='.$article->slug.'&tmpl=component&print=1&page='.@ $request->limitstart.'&option=com_content';

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ( $params->get( 'show_icons' ) ) {
			$text = JHTML::_('image.site',  'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ) );
		} else {
			$text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
		}

		$attribs['title']	= JText::_( 'Print' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

		return JHTML::_('link', $url, $text, $attribs);
	}

	function print_screen($article, $params, $access, $attribs = array())
	{
		$user =& JFactory::getUser();

		// checks template image directory for image, if non found default are loaded
		
			$text = JHTML::_('image.site',  'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ) );
		
		
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}
}
?>