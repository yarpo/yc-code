<?php
/**
 * @version 1.1.0 Beta
 * @package Joomla
 * @subpackage Show/Hide Content
 * @copyright	Copyright (C) 2010 webconstruction.ch. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * @contact		info@webconstruction.ch
 * @website		www.webconstruction.ch
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die ('Access denied!');

jimport('joomla.plugin.plugin');

class plgContentshowhidecontent extends JPlugin
{
    //Constructor
    function plgContentshowhidecontent( & $subject, $config)
    {
        parent::__construct( $subject, $config);
    }
    
    function onPrepareContent( & $article, & $params, $limitstart)
    {
	    
        $content = $article->text;
    	$mainframe =& JFactory::getApplication();
	    if ($mainframe->isAdmin()){
    		return;
    	}
        require_once(JPATH_PLUGINS.DS.'content'.DS.'showhidecontent'.DS.'classes'.DS.'showhidecontent.class.php');
        $showhideObject = new ShowHideContent($content);
		if ($showhideObject->content=='')return;
        $article->text = $showhideObject->content;
    }
    
}
