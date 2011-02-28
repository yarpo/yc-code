<?php
/**
* @version		1.2.2.1
* @package		Blog Calendar
* @author		Justo Gonzalez de Rivera
* @license		GNU/GPL
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once (dirname(__FILE__).DS.'calendarClass.php');


class modBlogCalendarHelper
{


	
function showCal(&$params,$year,$month,$day='',$ajax=0,$modid) //this function returns the html of the calendar for a given month
	{
	global $mainframe;
	$offset= $mainframe->getCfg('offset');
	
	$language=& JFactory::getLanguage(); //get the current language
	$language->load( 'mod_blog_calendar' ); //load the language ini file of the module
	$article= $language->_('VALUEARTICLE');
	$articles= $language->_('VALUEARTICLES'); //this strings are used for the titles of the links
	$article2= $language->_('VALUEARTICLE2');
	
	
	$rows= $this->setTheQuery($params,$year,$month,$day,$ajax,0);
	
		
	$cal = new MyCalendar; //this object creates the html for the calendar
	$dayNamLen= $params->get('cal_length_days');
	
	$cal->dayNames = array(substr(JText::_( 'SUN' ),0,$dayNamLen), substr(JText::_( 'MON' ),0,$dayNamLen),
	substr(JText::_( 'TUE' ),0,$dayNamLen), substr(JText::_( 'WED' ),0,$dayNamLen),
	substr(JText::_( 'THU' ),0,$dayNamLen),	substr(JText::_( 'FRI' ),0,$dayNamLen),
	substr(JText::_( 'SAT' ),0,$dayNamLen));
	
    $cal->monthNames = array(JText::_( 'JANUARY' ), JText::_( 'FEBRUARY' ), JText::_( 'MARCH' ), 
							JText::_( 'APRIL' ), JText::_( 'MAY' ), JText::_( 'JUNE' ),
                            JText::_( 'JULY' ), JText::_( 'AUGUST' ), JText::_( 'SEPTEMBER' ), 
							JText::_( 'OCTOBER' ), JText::_( 'NOVEMBER' ), JText::_( 'DECEMBER' ) );
							
	$cal->startDay = $params->get('cal_start_day'); //set the startday (this is the day that appears in the first column). Sunday = 0
													//it is loaded from the language ini because it may vary from one country to another, in Spain
													//for example, the startday is Monday (1)
	
	//set the link for the month, this will be the link for the calendar header (ex. December 2007)
	$cal->monthLink=JRoute::_(SEO_URL . '&year=' . $year .
					'&month=' . $month . '&modid=' . $modid);
	$cal->modid= $modid;
	
	
		foreach ( $rows as $row )
		{
		$created=& new JDate($row->created, -$offset);
		
		$counter= Array();
		
		$createdYear=$created->toFormat('%Y');
		$createdMonth=$created->toFormat('%m');
		$createdDay=$created->toFormat('%d'); //have to use %d because %e doesn't works on windows
		$createdDate=$createdYear . $createdMonth . $createdDay; //this makes an unique variable for every day
		$counter[$createdDate]+=1; //$counter[$date] counts the number of articles in each day, to display it as a title in the link of the day
		
		//linklist is the array that stores the link strings for each day
		$cal->linklist[$createdDate]=	JRoute::_('index.php?option=com_blog_calendar'.
								'&year=' . $createdYear . '&month=' . $createdMonth . '&day=' . 
								$createdDay . '&modid=' . $modid);
		$cal->linklist[$createdDate].="\" title=\""; //the calendar class sets the links this way: <a href=" . THE LINK STRING . ">
											 //so, the easiest way to add a title to that link is by setting THE LINK STRING = the link" title="the title
											 //the result link would be <a href="the link" title="the title">
		$cal->linklist[$createdDate].= $counter[$createdDate] . ' ';
		$cal->linklist[$createdDate].= ($counter[$createdDate] > 1)? $articles : $article;
		$cal->linklist[$createdDate].= ' ' . $article2;
		//the above 3 lines output something like: 3 articles on this day. Or: 1 article on this day
		
		}

	return $cal->getMonthView($month,$year,$day);

	}
	
		function getDate_byId($id){
		
		global $mainframe;
		$offset= $mainframe->getCfg('offset');
		
		$query=	' SELECT created' .
			' FROM #__content'.
			' WHERE id=\'' . $id . '\'';
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$row=& $db->loadObjectList();
		
		
		jimport('joomla.utilities.date');
		$created=new JDate($row[0]->created, -$offset);
		
		
		$createdYear=$created->toFormat('%Y');
		$createdMonth=$created->toFormat('%m');
		$createdDay=$created->toFormat('%d');
		
		$createdDate=Array($createdYear,$createdMonth,$createdDay);
		
		return $createdDate;
	}
	
	function showDropDown($params,$year,$month,$day,$ajax=0){
	
		$results= $this->setTheQuery($params,$year,$month,$day,$ajax,1);		
		
		foreach($results as $key => $result){
		$created=new JDate($results[$key]->created);
		$createdYear= $created->toFormat('%Y');
		$createdMonth= $created->toFormat('%m');
		$results[$key]->link=JRoute::_(ContentHelperRoute::getArticleRoute($results[$key]->slug, $results[$key]->catslug, $results[$key]->sectionid));
		
		$results[$key]->year = $createdYear; $results[$key]->month = $createdMonth;
		
		$createdYear==$year? $articleCounter[$createdYear]['now']= true : '';
		$createdMonth==$month? $articleCounter[$createdYear][$createdMonth]['now']= true : '';
		
		$articleCounter[$createdYear][$createdMonth]['total']++;
		$articleCounter[$createdYear]['total']++;
		
		}
	
	return array($results,$articleCounter);
	}
	
	function setTheQuery($params,$year,$month,$day='',$ajax=0,$type){
	
		global $mainframe;
		$offset= $mainframe->getCfg('offset');
		
		$db =& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');
		
		$secid		= $params->get('section_ids') ; //get the specific categories list from the parameters
		$catid		= $params->get('category_ids') ; //get the specific sections list from the parameters
		
		$aid		= $user->get('aid', 0);
		
		$contentConfig = &JComponentHelper::getParams( 'com_content' );
		$access		= !$contentConfig->get('shownoauth');
		
		
		jimport('joomla.utilities.date');
		
		$nullDate	= $db->getNullDate();
		
		$date[0]= mktime(0,0,0,$month, 1,$year, 0);
		$date[1]= mktime(0,0,0, $month+1, 1, $year, 0);		
		
		$date[0]=& new JDate($date[0], -$offset);
		$date[1]=& new JDate($date[1], -$offset);
			
		$date[0]= $date[0]->toMySQL();
		$date[1]= $date[1]->toMySQL();
		
		
		$dateNow = new JDate();
		$now = $dateNow->toMySQL();
		
		//if there are specific sections selected, the variable $secCondition will be added to the query, to get only the articles of this sections
		if ($secid) {
			$ids = explode( ',', $secid );
			JArrayHelper::toInteger( $ids );
			$secCondition = ' AND (sectionid=' . implode( ' OR sectionid=', $ids ) . ')';
		}
		
		//if there are specific categories selected, the variable $catCondition will be added to the query, to get only the articles of this categories
		if($catid) {
			$ids= explode(',',$catid);
			JArrayHelper::toInteger( $ids );
			$catCondition = ' AND (catid='. implode( ' OR id=', $ids ) . ')';
		}
		
		
		if($type == 0){ //query for the calendar		
		$where		= 'state = 1'
			. ' AND  created >= '. $db->Quote($date[0]) .' AND created <= '. $db->Quote($date[1])
			. ' AND ( publish_up = '.$db->Quote($nullDate).' OR publish_up <= '.$db->Quote($now).' )'
			. ' AND ( publish_down = '.$db->Quote($nullDate).' OR publish_down >= '.$db->Quote($now).' )'
			;
		$query ='SELECT id,sectionid,catid,created,publish_up,publish_down,state,access'.
				' FROM #__content'.
				' WHERE '. $where .
				($access ? ' AND access <= ' .(int) $aid : ''). //select only the content that the current user is allowed to see
				' AND sectionid > 0'.
				($secid ? $secCondition : '' ). //add the $secCondition if $secid exists
				($catid ? $catCondition : '' ); //add the $catCondition if $catid exists				
		}
	
		elseif($type == 1){ //query for the list		
		$where		= 'a.state = 1'
			. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
			. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
			;
			
		$query ='SELECT a.id,a.sectionid,a.catid,a.created,a.publish_up,a.publish_down,a.state,a.access,a.title, '.
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
				' FROM #__content AS a'.
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' WHERE '. $where . ' AND s.id > 0' .
				($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
				' AND s.published = 1' . //this 3 lines: check that the selected articles are published and that the current user can get access to them
				' AND cc.published = 1' .  
				($secid ? $secCondition : '' ). //add the $secCondition if $secid exists
				($catid ? $catCondition : '' ). //add the $catCondition if $catid exists
				' ORDER BY created DESC'; //order by date created descending				
		}
	//set the query and load the results
	$db->setQuery($query);
	$results = $db->loadObjectList();
	
	return $results;
	}
}


class MyCalendar extends Calendar
{
	//this variable will be an array that contains all the links of the month
	var $linklist;

	//this function is called from getMonthView(month,year) to get the link of the given day
	//if this function returns nothing (""), then getMonthView wont put a link on that day
	function getDateLink($day, $month, $year) 
	{
		$link = "";
		if (strlen($month)<2)
		{
			$month = '0'.$month;
		}
		if (strlen($day)<2)
		{
			$day = '0'.$day;
		}

		$url = '';
		$date= $year . $month . $day;
		if(isset($this->linklist[$date]))
		{
			// $link = $this->linklist[$date];  //$this->linklist[$date] was set for every date in the foreach bucle at lines 50-83
			$link = SEO_URL . '?year=' . $year . '&amp;month=' . $month . '&amp;day=' . $day;
		}

		return $link;
	}

  

  //Return the URL to link to in order to display a calendar for a given month/year.
  //this function is called to get the links of the two arrows in the header.
    function getCalendarLink($month, $year)
    {
        $getquery = JRequest::get('GET'); //get the GET query
		//$calendarLink= JURI::current().'?'; //get the current url, without the GET query; and add "?", to set the GET vars

		$calendarLink = SEO_URL . '?';

		foreach($getquery as $key => $value){  /*this bucle goes through every GET variable that was in the url*/
			if($key!='month' AND $key!='year' AND $key!='day' AND $value){ /*the month,year, and day Variables must be diferent of the current ones, because this is a link for a diferent month */
				$calendarLink.= $key . '=' . $value . '&amp;';
			}
		}
		
		$calendarLink.='month='.$month.'&amp;year='.$year; //add the month and the year that was passed to the function to the GET string
		return $calendarLink;
    }
}
?>
