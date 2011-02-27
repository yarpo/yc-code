<?php
/**
* @version		$Id: emailcloak.php 13360 2009-10-28 04:30:10Z ian $
* @package		Joomla
* @copyright	Copyright (C) 2010 DesignCompass corp. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'plgContentPlaceArticle');


function plgContentPlaceArticle(&$row, &$params, $page=0)
{
	if (is_object($row)) {
		return plgPlaceArticle($row->text, $params);
	}
	return plgPlaceArticle($row, $params);
}


function plgPlaceArticle(&$text, &$params)
{
	$offset=0;
	do{
		$ps=strpos($text, '{article=', $offset);
		if($ps===false)
			break;
		
		$pe=strpos($text, '}', $offset+$ps);
		if($pe===false)
			break;
		
		$notestr=substr($text,$ps,$pe-$ps+1);
		$articleid=substr($text,$ps+9,$pe-$ps-9);
		
		$articletext=getArticle($articleid);
		
		if(strlen($articletext)>0)
			$text=str_replace($notestr,$articletext,$text);
			
		$offset=$ps+1;
	
	}while(!($pe===false));
	
	//$text=replace('s';

	return true;
}

function getArticle($articleid)
{
	// get database handle
				$db=& JFactory::getDBO();
				
				$query='SELECT introtext FROM #__content WHERE id='.(int)$articleid.' LIMIT 1';
				
				$db->setQuery($query);
                if (!$db->query())    die( $db->stderr());
				
				$rows=$db->loadObjectList();
				
				if(count($rows)!=1)
						return "";
					
				return $rows[0]->introtext;
}

