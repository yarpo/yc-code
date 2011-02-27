<?php
/**
* @version		$Id: vote.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent( 'onBeforeDisplayContent', 'plgContentVote' );

function plgContentVote( &$row, &$params, $page=0 )
{
	$uri = & JFactory::getURI();

	$id 	= $row->id;
	$html 	= '';

	if (isset($row->rating_count) && $params->get( 'show_vote' ) && !$params->get( 'popup' ))
	{
		JPlugin::loadLanguage( 'plg_content_vote' );
		$html .= '<form method="post" action="' . $uri->toString( ) . '">';
		$html .= '<script type="text/javascript" src="/includes/js/jquery/plugins/jquery.rating.pack.js"></script>';
		$html .= '<script type="text/javascript">$(function(){$("input.rate").rating();});</script>';

		if (!$params->get( 'intro_only' ))
		{
			$html .= '<span class="content_vote">';
			$rate = intval($row->rating);
			for($i = 1; $i <= 5; $i++)
			{
				$html .= '<input type="radio" class="rate" name="user_rating" value="'. $i. '" ';
				if ($rate == $i) 
				{
					$html .= 'checked="checked"';
				}
				$html .= ' />';
			}

			$html .= '&nbsp;<input type="image" name="submit_vote" src="/templates/redevo_beep/images/confirm-smallico.png" />';
			$html .= '<input type="hidden" name="task" value="vote" />';
			$html .= '<input type="hidden" name="option" value="com_content" />';
			$html .= '<input type="hidden" name="cid" value="'. $id .'" />';
			$html .= '<input type="hidden" name="url" value="'.  $uri->toString( ) .'" />';
			$html .= '</span>';
		}
		$html .= '</form>';
	}
	return $html;
}
