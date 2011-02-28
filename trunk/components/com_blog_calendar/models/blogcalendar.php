<?php
/**
* @version		1.05
* @package		Blog Calendar Reload
* @author		Juan Padial
* @authorweb	http://www.shikle.com
* @license		GNU/GPL
*
* modified from the blogcalendar.php file of the Blog Calendar 1.2.2.1 component by Justo Rivera
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );


require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');



class BlogCalendarModelBlogCalendar extends JModel
{
	//this functions gets the articles on a given date
	function getContent($params,$year,$month,$day)
	{
		global $mainframe;
		
		
		$fullmonth= false;
		
		if(!$day)
		$fullmonth=true;
		
		
		if($fullmonth && !$month)
		$fullyear=true;
		
		if($fullyear && !$year)
		$full=true;

		
		$secid= $params->get('section_ids');
		$catid= $params->get('category_ids');
		
		$limitstart=(int) JRequest::getVar('limitstart');
		$count=(int) $params->get('count');
		
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');
		$aid		= $user->get('aid', 0);
		
		$contentConfig = &JComponentHelper::getParams( 'com_content' );
		$access		= !$contentConfig->get('shownoauth');
		
		
		$db =& JFactory::getDBO();
		
		$nullDate	= $db->getNullDate();
		jimport('joomla.utilities.date');
		$dateNow = new JDate();
		$now = $dateNow->toMySQL();
		
		
		$offset= $mainframe->getCfg('offset');		
		
		if($fullmonth){
		$date[0]= mktime(0,0,0,$month, 1,$year);
		$date[1]= mktime(0,0,0, $month+1, 1, $year);
		}
		else{
		$date[0]= mktime(0,0,0, $month, $day, $year);
		$date[1]= mktime(0,0,0, $month, $day+1, $year);
		}
		
		if($fullyear){
		$date[0]= mktime(0, 0, 0, 1, 1, $year);
		$date[1]= mktime(0, 0, 0, 1, 1, $year+1);
		}
		
		
		$date[0]=& new JDate($date[0], -$offset);
		$date[1]=& new JDate($date[1], -$offset);
		
				
		$date[0]= $date[0]->toMySQL();
		$date[1]= $date[1]->toMySQL();
		
		
		
		
		//this expression selects all the articles that match the date, and that are published at the moment
		$where		= 'a.state = 1'
			. ($full? '' : ' AND  a.created >= '. $db->Quote($date[0]) .' AND a.created <= '. $db->Quote($date[1]))
			. ' AND ( a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' )'
			. ' AND ( a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )'
			;
		
		
		
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
			$catCondition.= ' AND (catid='. implode( ' OR catid=', $ids ) . ')';
		}
				
			
		$query = 'SELECT a.id,a.catid,a.sectionid '.
				' FROM #__content AS a'.
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' WHERE '. $where . ' AND s.id > 0' .
				($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
				' AND s.published = 1' . //this 3 lines: check that the selected articles are published and that the current user can get access to them
				' AND cc.published = 1' .  
				($secid ? $secCondition : '' ). //add the $secCondition if $secid exists
				($catid ? $catCondition : '' ); //add the $catCondition if $catid exists
				
		$db->setQuery($query); //this query is used to get the total number of articles
		
		$total= count($db->loadObjectList()); //the total number of articles
		
		$query ='SELECT a.*, '.
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'.
				' s.title as secTitle,'. //this will be used to display the section and category title below the article name
				' cc.title as catTitle,'.
				' auth.name as author'.
				' FROM #__content AS a'.
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' INNER JOIN #__users AS auth ON auth.id = a.created_by' .
				' WHERE '. $where . ' AND s.id > 0' .
				($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
				' AND s.published = 1' . //this 3 lines: check that the selected articles are published and that the current user can get access to them
				' AND cc.published = 1' .  
				($secid ? $secCondition : '' ). //add the $secCondition if $secid exists
				($catid ? $catCondition : '' ). //add the $catCondition if $catid exists
				' ORDER BY created DESC'; //order by date created descending
				
		//set the query 
		$db->setQuery( $query, $limitstart, $count );
		//and load the results
		$results = $db->loadObjectList();
		
		$daycount= $day; $monthcount= $month; $yearcount= $year;
		$i=1; $j=1;
		
		//this foreach adds a specific link to each result
		foreach($results as $key=>$result){
		
		$createdDate=& new JDate( $results[$key]->created, -$offset);		
		$results[$key]->created_new_day=""; //empty if it's not a new day in the list
		
		//this checks if the created day is a new day in the list
		if($createdDate->toFormat('%d')!=$daycount || $createdDate->toFormat('%m')!= $monthcount){ 
		 $results[$key]->created_new_day = $createdDate->toFormat('%A %d %B %Y');
		 }
		 
		 $results[$key]->date = $createdDate->toFormat(JText::_('DATE_FORMAT_LC2'));		 
		 $daycount  = $createdDate->toFormat('%d');
		 $monthcount= $createdDate->toFormat('%m');
		 $yearcount = $createdDate->toFormat('%Y');
		 
		$results[$key]->link=JRoute::_(ContentHelperRoute::getArticleRoute($results[$key]->slug, $results[$key]->catslug, $results[$key]->sectionid));
		
		$results[$key]->text = $results[$key]->introtext.($params->get('show_fulltext')? $results[$key]->fulltext : '');

		$j++;
		$i++;
		
		}
		
		
		$dateFormat="%A, %d %B %Y"; //this is like Monday, 09 december 2007
		
		if($fullmonth){
		$day='15'; $dateFormat="%B %Y"; //if it's displaying the whole month, then the style is like december 2007
										//the day is set to any value so that mktime wont give any errors
	}
		if($fullyear){
		$month='06'; $dateFormat="%Y"; //if it's displaying the whole year, then the style is like 2007
									   //the month is set to any value so that mktime wont give any errors
		}
		
		if($full){
		$year='2008'; $dateFormat=""; //if it's displaying all the articles, there is no date
									  //the year is set to any value so that mktime wont give any errors
			}
			
		$date=new JDate(mktime(12,30,30, $month, $day, $year)); 
		
		/*$results['date'] = JText::_($i>1? 'ARTICLES' : 'NOARTICLES') . ' '; //Articles published on OR No articles published on*/
		$results['date'] .= $date->toFormat($dateFormat);  				  //the date
		
		$full? $results['date']=JText::_('ALLARTICLES') : ''; //if it's displaying all the articles */
		
		$results['total']     = $total; 
		$results['limitstart']= $limitstart;	//Pagination variables 
		$results['limit']     = $count;
		
		return $results;
}
}
?>