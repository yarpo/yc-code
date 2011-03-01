var modid;

(function() 
{
	if (!window.jQuery)
	{
		setTimeout(arguments.callee, 1000);
	}
	else
	{
		$(document).ready(setupBlogTable);
	}
})();


function setupBlogTable()
{
	$('a[id^="prevMonth-"]').each(function() 
	{
		modid = this.id.substring(10);
		this.href='javascript:void(0)';

		this.onclick = function()
		{
			month--;
			newDate(month,year,modid);
			return false;
		}
	});

	$('a[id^="nextMonth-"]').each(function() 
	{
		modid = this.id.substring(10);
		this.href='javascript:void(0)';

		this.onclick = function()
		{
			month++;
			newDate(month,year,modid);
			return false;
		}
	});
}

function newDate(month,year,modid)
{
	if (month <= 0)
	{
		month += 12;
		year--;
	}

	if (month > 12)
	{
		month -= 12;
		year++;
	}

	var tableDiv = $('#calendar-' + modid )
		.html('<img src="'+calendar_baseurl+'modules/mod_blog_calendar/img/loading.gif"> proszę poczekać');

	$.ajax({
		url: location.href,
		type: 'POST',
		data: {
			'year'  : year,
			'month' : month,
			'ajaxCalMod' : 1,
			'ajaxmodid' : modid
		},
		success: function(msg)
		{
			var start = msg.indexOf('<!--calendar-'+modid+' start-->');
			var finish = msg.indexOf('<!--calendar-'+modid+' end-->');
			justTheCalendar = msg.substring(start,finish);
			tableDiv.hide().html(justTheCalendar).fadeIn();
			setupBlogTable();
		}
	});
}
