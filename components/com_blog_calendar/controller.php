<?php
/**
* @version		1.05
* @package		BlogCalendar Reload
* @author		Juan Padial
* @authorwebsite	http://www.shikle.com
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );	
class BlogCalendarController extends JController
{		
		function editCSS(){
		jimport('joomla.filesystem.path');
		$cname = JPATH_ROOT.DS.'modules'.DS.'mod_blog_calendar'.DS.'tmpl'.DS.'style.css';
                $c = fopen($cname,"r");
	        $content = fread($c, filesize($cname));
	        $content = htmlspecialchars($content);


		echo '
		<form action="index.php" method="post" name="adminForm">
					<table class="adminform">
		<tr>
			<th>
				Editing '.JPATH_ROOT.DS.'modules'.DS.'mod_blog_calendar'.DS.'tmpl'.DS.'style.css'.'
			</th>
		</tr>
		<tr>
			<td>
				<textarea style="width:80%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox">'.
				$content.
				'</textarea>
			</td>
		</tr>
		</table>
		<input type=\'hidden\' name=\'task\' value=\'save_css\'>
		<input type=\'hidden\' name=\'option\' value=\'com_blog_calendar\'>
		<input type=\'submit\' value=\'Save\'>
		';
		}
		
		function saveCSS(){
		global $mainframe;
		

		// Initialize some variables
		$filename		= JPATH_ROOT;
		$filename	   .= '\modules\mod_blog_calendar\tmpl\style.css';
		$filecontent	= JRequest::getVar('filecontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$file = $filename;
		jimport('joomla.filesystem.file');
		$return = JFile::write($file, $filecontent);
		if($return) echo 'Success saving the file';		
		}
		
		function viewGuide(){
                echo '<center><img src=\'administrator\components\com_blog_calendar\blogcalendar.gif\'></img></center>';
		echo '<ul><li><h2><a href=\'?option=com_blog_calendar&task=edit_css\'>Edit CSS</a></h2><br>';
		echo '</li><li>Guide at <a href=\'http://www.shikle.com/blogcalendar.htm/\'>Blog Calendar page</a></li>';
		echo '<br><br><br><br><center>Blog Calendar Reload by Juan Padial based on original files of the Blog Calendar by Justo de Rivera, licensed under GPL. Feel free to modify it.</center>';
		}

}
?>