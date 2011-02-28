function toggle_visibility(id)
{
	var e = $('#' + id);
	if (e.css('display') == 'block')
	{
		e.slideUp();
		$('#toggle-' +id).html('&#9658;');
	}
	else
	{
		e.slideDown();
		$('#toggle-' +id).html('&#9660;');
	}
}
