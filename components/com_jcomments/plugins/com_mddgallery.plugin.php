<?php
/**
 * JComments plugin for MDDGallery photo objects support
 *
 * @version 2.0
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2006-2009 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class jc_com_mddgallery extends JCommentsPlugin
{
	function getObjectTitle($id)
	{
		$db = & JCommentsFactory::getDBO();
		$db->setQuery( 'SELECT imgtitle FROM #__mddgallery WHERE published = 1 AND id = ' . $id );
		return $db->loadResult();
	}

	function getObjectLink($id)
	{
		$_Itemid = JCommentsPlugin::getItemid( 'com_mddgallery' );
		$link = sefRelToAbs( "index.php?option=com_mddgallery&amp;task=view&amp;id=" . $id . "&amp;Itemid=" . $_Itemid );
		return $link;
	}
}
?>