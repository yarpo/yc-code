<?php
/**
 * JComments plugin for RSGallery2 photo objects support
 *
 * @version 1.4
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2006-2008 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class jc_com_rsgallery2 extends JCommentsPlugin
{
	function getObjectTitle($id)
	{
		$db = & JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT title FROM #__rsgallery2_files WHERE published = 1 AND id = ' . $id );
		return $db->loadResult();
	}

	function getObjectLink($id)
	{
		if (JCOMMENTS_JVERSION == '1.0') {
			$_Itemid = JCommentsPlugin::getItemid( 'com_rsgallery2' );

			$db = & JCommentsFactory::getDBO();
			$db->setQuery( 'SELECT gallery_id FROM #__rsgallery2_files WHERE id = ' . $id );
			$catid = $db->loadResult();

			$link = sefRelToAbs( "index.php?option=com_rsgallery2&amp;page=inline&amp;catid=".$catid."&amp;id=" . $id . "&amp;limistart=0&amp;Itemid=" . $_Itemid );
		} else {
			$link = 'index.php?option=com_rsgallery2&page=inline&id=' . $id;

			require_once(JPATH_SITE.DS.'includes'.DS.'application.php');

			$component = & JComponentHelper::getComponent('com_rsgallery2');
			$menus = & JSite::getMenu();
			$items = $menus->getItems('componentid', $component->id);

			if (count($items)) {
				$link .= '&Itemid=' . $items[0]->id;
			}

			$link = JRoute::_( $link );
		}

		return $link;
	}
}
?>