var modid;
var key;

window.addEvent('domready', function(){
	var links = document.getElementsByTagName('a');

	for(key in links){

		if(typeof(links[key]) == "object"){
			if(links[key].id.substring(0,10)=='prevMonth-')
			{
				modid= links[key].id.substring(10);
				links[key].onclick = function()
				{
					month--;
					newDate(month,year,modid);
				}
				links[key].href='javascript:void(0)';
			}


			if(links[key].id.substring(0,10)=='nextMonth-')
			{
				links[key].onclick = function()
				{
					month++;
					newDate(month,year,modid);
				}
				links[key].href='javascript:void(0)';
			}
		}
	}
});

function newAjax()
{
	/* THIS CREATES THE AJAX OBJECT */
	var xmlhttp=false; 
	try 
	{ 
		// ajax object for non IE navigators
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
	}
	catch(e)
	{
		try
		{
			// ajax object for IE 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!="undefined") { xmlhttp=new XMLHttpRequest(); } 

	return xmlhttp; 
}

function newDate(month,year,modid)
{
		var myFx = new Fx.Style('tableCalendar-'+modid, 'opacity').start(0);
		loadHtml  = "<p id='loadingDiv-"+modid+"' style='margin-left: 1cm; margin-top: -2cm; margin-bottom: 2cm;'>";
		loadHtml += "<img src='"+calendar_baseurl+"modules/mod_blog_calendar/img/loading.gif'>";
		loadHtml += "Loading...</p>";
		document.getElementById( 'calendar-'+modid ).innerHTML +=  loadHtml ;
		var myFx = new Fx.Style('tableCalendar-'+modid, 'opacity').start(1,0.05);
		
		if(month<=0){
			month+=12;
			year--;
		}
		if(month>12){
			month-=12;
			year++;
		}

		var ajax = newAjax();
		ajax.open("POST", location.href, true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send('year='+year+'&month='+month+'&ajaxCalMod=1'+'&ajaxmodid='+modid);
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4)
			{
			
				var response = ajax.responseText;
				var start = response.indexOf('<!--calendar-'+modid+' start-->');
				var finish = response.indexOf('<!--calendar-'+modid+' end-->');

				justTheCalendar= response.substring(start,finish);
				var myFx = new Fx.Style('tableCalendar-'+modid, 'opacity').start(0.3,1);
				document.getElementById( 'calendar-'+modid ).innerHTML=justTheCalendar;

				linkPrev= document.getElementById('prevMonth-'+modid);
				linkNext= document.getElementById('nextMonth-'+modid);

				linkPrev.onclick= function(){month--; newDate(month,year,modid);}
				linkNext.onclick= function(){month++; newDate(month,year,modid);}

				//linkNext.href= linkPrev.href= 'javascript:void(0)';
			}
		}
	}
