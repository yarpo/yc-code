<?php
/**
* @version		1.2.2.1
* @package		Blog Calendar
* @author		Justo Gonzalez de Rivera
* @license		GNU/GPL
*/


	// no direct access

	// Include the syndicate functions only once
	require_once (dirname(__FILE__).DS.'helper.php');

	$ajax= JRequest::getVar('ajaxCalMod',0,'default','POST');
	$ajaxmod= JRequest::getVar('ajaxmodid',0,'default','POST');	
	
	if(!$params->get('cal_start_date')){
		$year	= JRequest::getVar('year',date('Y'));    /*if there is no date requested, use the current month*/
		$month	= JRequest::getVar('month',date('m'));
		$day	= $ajax? '' : JRequest::getVar('day');
	}
	else{
		
		$startDate=& new JDate($params->get('cal_start_date'));		
		$year	= JRequest::getVar('year', $startDate->toFormat('%Y'));
		$month	= JRequest::getVar('month', $startDate->toFormat('%m'));
		$day	= $ajax? '' : JRequest::getVar('day', $startDate->toFormat('%d'));		
	}
	$helper = new modBlogCalendarHelper;
	$doc = &JFactory::getDocument();
	
	if($ajax)
		$calendar = $helper->showCal($params,$year,$month,$day,$ajax,$module->id); 
	else{
		if(JRequest::getVar('option')=='com_content' && JRequest::getVar('view')=='article')
			list($year,$month,$day)=modBlogCalendarHelper::getDate_byId(JRequest::getVar('id')); /*if the user is viewing an article, the date to use is the date of that article*/

		if($params->get('show_what') == '1') /*a list*/
			list($dropdown,$articleCounter) = $helper->showDropDown($params,$year,$month,$day,$ajax);		
		else
			$calendar = $helper->showCal($params,$year,$month,$day,$ajax,$module->id);;
		
		JHTML::_('behavior.mootools');
		if (!defined('MOD_BLOG_CALENDAR'))
		{
			$doc->addScriptDeclaration('var month=' . intval($month) . ', year=' . intval($year) . ', calendar_baseurl=\''. JURI::base() . '\';');
			$doc->addScript( 'modules/mod_blog_calendar/js/blog_calendar.js' );
			$doc->addScript( 'modules/mod_blog_calendar/js/blog_list.js' );
			define('MOD_BLOG_CALENDAR', true);
		}
	}
	
require(JModuleHelper::getLayoutPath('mod_blog_calendar'));

?>
