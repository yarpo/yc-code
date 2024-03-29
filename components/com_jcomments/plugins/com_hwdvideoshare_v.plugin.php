<?php
/**
 *    @version 2.1.2 Build 21201 Alpha [ Linkwater ]
 *    @package hwdVideoShare
 *    @copyright (C) 2007 - 2009 Highwood Design
 *    @license Creative Commons Attribution-Non-Commercial-No Derivative Works 3.0 Unported Licence
 *    @license http://creativecommons.org/licenses/by-nc-nd/3.0/
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class jc_com_hwdvideoshare_v extends JCommentsPlugin
{
	function getObjectTitle($id, $none)
	{
		$db = & JCommentsFactory::getDBO();

		$db->setQuery( 'SELECT title FROM #__hwdvidsvideos WHERE id = ' . $id );
		$title = $db->loadResult();

		if (!empty($title)) {
			return $title;
		} else {
			return "Unknown hwdVideoShare Content";
		}
	}

	function getObjectLink($id, $none)
	{
		static $_Itemid;

		if (!isset($_Itemid)) {
			if (JCOMMENTS_JVERSION == '1.5') {
				$needles = array('gid' => (int) $id);
				if ($item = jc_com_hwdvideoshare_v::_findItem($needles)) {
					$_Itemid = $item->id;
				} else {
					$_Itemid = '';
				}
			} else {
				$_Itemid = JCommentsPlugin::getItemid('com_hwdvideoshare_v');
			}
		}

		if (JCOMMENTS_JVERSION == '1.0') {
			$link = sefRelToAbs("index.php?option=com_hwdvideoshare&amp;task=viewvideo&amp;video_id=" . $id . "&amp;Itemid=" . $_Itemid);
		} else if (JCOMMENTS_JVERSION == '1.5'){
			$link = JRoute::_("index.php?option=com_hwdvideoshare&amp;task=viewvideo&amp;video_id=" . $id . "&amp;Itemid=" . $_Itemid);
		}

		return $link;
	}

	function getObjectOwner($id)
	{
		$db = & JCommentsFactory::getDBO();
		$db->setQuery('SELECT user_id FROM #__hwdvidsvideos WHERE id = ' . $id);
		$userid = $db->loadResult();
		return intval( $userid );
	}

	function _findItem($needles)
	{
		$component =& JComponentHelper::getComponent('com_hwdvideoshare');

		$menus	= & JSite::getMenu();
		$items	= $menus->getItems('componentid', $component->id);
		$user 	= & JFactory::getUser();
		$access = (int)$user->get('aid');

		if (count($items) == 0) { return false; }
		foreach ($needles as $needle => $id) {
			foreach ($items as $item) {
				if ($item->published == 1 && $item->access <= $access) {
					return $item;
				}
			}
		}

		return false;
	}

}
?>