<?php
/**
* @version		1.2.2.1
* @package		Blog Calendar
* @author		Justo Gonzalez de Rivera
* @license		GNU/GPL
*/


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementincludeJS extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'includeJS';

	function fetchElement($name, $value, &$node, $control_name)
	{
	$doc = &JFactory::getDocument(); //this is to add the scripts of the calendar
	$doc->addScriptDeclaration("

	function blocking(nr, show)
{
	if (document.layers)
	{
		current = show ? 'block' : 'none';
		document.layers[nr].display = current;
	}
	else if (document.all)
	{
		current = show ? 'block' : 'none';
		document.all[nr].style.display = current;
	}
	else if (document.getElementById)
	{
		vista = show ? 'block' : 'none';
		document.getElementById(nr).style.display= vista;
	}
}

function showCalendarParams(show)
{
blocking('paramscal_start_day-lbl',show);
blocking('cal_start_day', show);
blocking('paramscal_length_days-lbl', show);
blocking('cal_length_days', show);
blocking('paramscal_start_date-lbl', show);
blocking('paramscal_start_date', show);
blocking('paramscal_start_date_img', show);
}

function showListParams(show)
{
blocking('paramsshow_list_years-lbl', show);
blocking('show_list_years', show);
blocking('paramsshow_list_months-lbl', show);
blocking('show_list_months', show);
blocking('paramsshow_list_articles-lbl', show);
blocking('show_list_articles', show);
blocking('paramsshow_list_sublevels-lbl', show);
blocking('show_list_sublevels', show);
}

window.addEvent('load',function(){

var links = document.getElementsByTagName('input');

for(key in links){

if(links[key].id=='paramsshow_what0'){
	if(links[key].checked){
	showListParams(0);
	showCalendarParams(1);
	}
	links[key].onclick= function(){
	showListParams(0);
	showCalendarParams(1);
	}
}

if(links[key].id=='paramsshow_what1'){
	if(links[key].checked){
	showListParams(1);
	showCalendarParams(0);
	}
	links[key].onclick= function(){
	showListParams(1);
	showCalendarParams(0);
	}
}

if(links[key].id=='paramsshow_what2'){
	if(links[key].checked){
	showListParams(1);
	showCalendarParams(1);
	}
	links[key].onclick= function(){
	showListParams(1);
	showCalendarParams(1);
	}
}
			}
			
			})

			");
	}
}