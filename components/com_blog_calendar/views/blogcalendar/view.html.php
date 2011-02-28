<?php
/**
* @version		1.05
* @package		Blog Calendar Reload
* @author		Juan Padial
* @authorweb	http://www.shikle.com
* @license		GNU/GPL
*
* modified from the view.html file of the Blog Calendar 1.2.2.1 component by Justo Gonzalez de Rivera
*/

jimport( 'joomla.application.component.view');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_blog_calendar'.DS.'helpers'.DS.'icon.php');

class BlogCalendarViewBlogCalendar extends JView
{

	function display($tpl = null)
	{
		global $mainframe;
		$model =& $this->getModel();
		
		$year= JRequest::getVar('year', false,'default','GET');
		$month= JRequest::getVar('month', false,'default','GET');  //get the date requested
		$day= JRequest::getVar('day', false,'default','GET');
		
		
		$user		=& JFactory::getUser();
		
		$params=& JComponentHelper::getParams('com_content');
		
		$modid = JRequest::getVar('modid');
		
		$db =& JFactory::getDBO();
		
		if($modid){ //if the component is called from the a Blog Calendar module, load the parameters of that module
		$query = 'SELECT params'
	            . ' FROM #__modules'
				. ' WHERE id = ' . $modid;
	            
		
		$db->setQuery( $query );
		
		$param= $db->loadObjectList();
		
		$this->params= preg_split("[\n]", $param[0]->params);
		}
		
		else{ //the component is being called from a menu item, get the parameters of that menu item
		
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		
		$this->params = preg_split("[\n]", $menu->params);
		
		}
		
		
		foreach($this->params as $param){
		$param = explode('=',$param);
												
		if($param[0] && $param[1]!='globals') //if the parameter is not set to Use globals, 
		$params->set($param[0], $param[1]);	  //replace its previous value (loaded from com_content in line 31) with the selected value
		
		}
		
		
		if($params->get('date')) //if there is a date set in the parameters, use that date for the list
		list($year,$month,$day) = split('-',$params->get('date'));
		
		
									 //call the function that returns the arrays of articles
		$this->assignRef('contents', $model->getContent($params,$year,$month,$day)); 
				
		$access				= new stdClass();
		$access->canEdit	= $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn	= $user->authorize('com_content', 'edit', 'content', 'own');
		$access->canPublish	= $user->authorize('com_content', 'publish', 'content', 'all');

		$this->assignRef('params', $params);
		$this->assignRef('user', $user);
		$this->assignRef('access', $access);
		
		jimport('joomla.html.pagination');
		$this->pagination=& new JPagination($this->contents['total'], $this->contents['limitstart'], $this->contents['limit']);
		
		
		unset($this->contents['total']); 		//unset the pagination entries in the array, 
		unset($this->contents['limitstart']); 	//  so they dont interfere with the 
		unset($this->contents['limit']);		//  foreach bucle used in default.php
		
		$this->date= $this->contents['date'];
		
		
		unset($this->contents['date']); //unset this variable so that the numbers of elements in the array $this->contents
										//is exactly the number of articles found. This is needed because tmpl/default.php
										//works with a foreach bucle to output the article data

		parent::display($tpl);
	} 
	
	function aname($article,$params){
	
	         $user=& JFactory::getUser($article->created_by);
	         
	         if ($params->get('show_author_username')==1){
	              $s=$user->name;
	              }
	
	         if ($params->get('show_author_username')==2) {
	             $user=& JFactory::getUser($article->created_by); 
	             $s=$user->username;
	             }
	             
                if ($params->get('cbintegration')) {
                    $database =& JFactory::getDBO();
	            $query = "SELECT id FROM #__menu WHERE link='index.php?option=com_comprofiler' AND published='1'";
                    $database->SetQuery($query);
                    $menid = $database->loadResult();
	            $cburi='index.php?option=com_comprofiler&task=userProfile&user=';
	            $cburi.=$user->id;
	            $cburi.='&Itemid=';
	            $cburi.=$menid;
	            $s=JHTML::_('link',$cburi,$s,null);
	            }
	            
	            return JText::_('Written by').' '.$s;
	}
	
	function acomm($article){                                        
                   $id = $article->id;
                   $database =& JFactory::getDBO();
	           $query = "SELECT COUNT(*) FROM #__comment WHERE contentid='$id' AND published='1'";
                   $database->SetQuery($query);
                   $number = $database->loadResult();
                   if (!$number) $number = 0;
                   return JText::_('Comments').' '.'('.$number.')'; 
       }
       function mh(){
                  $ur='http://www.shikle.com/blogcalendar.htm';
                  $urm='Blog Calendar';
                  return $mh=JHTML::_('link',$ur,$urm,null);
       }
       function gentruncatedcontent($article,$params){
		  $str=$article->text;
		  if($params->get('clean_xhtml')){$str=strip_tags($article->text);}
		     $body = explode(' ',preg_replace("/\s+/",' ',preg_replace("/(\r\n|\r|\n)/"," ",$str)));
                     $output = $body[0];
                     $i=1;
                       while((strlen($output))<=$params->get('truncate_words') && $body[$i]){
                       $output .= " ".$body[$i];
                       $i++;
                       }
                     $output=$output.'...';
                  return $output;
      } 
}
?>