/**
* Author:	Omar Muhammad
* Email:	admin@omar84.com
* Website:	http://omar84.com
* Plugin:	Component as Content
* Version:	1.5.6
* Date:		12/6/2010
* License:	http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Copyright:Copyright (C) 2007 - 2010 Omar's Site. All rights reserved.
**/

function applycss(id,file,file2,fheight,bgcolor)
	{
	var oIframe = document.getElementById(id);
	var oDoc = oIframe.contentWindow || oIframe.contentDocument;
	if (oDoc.document)
		{oDoc = oDoc.document;}
	addcss(oDoc,file);
	if (file2!="") 	addcss(oDoc,file2);
	if (bgcolor!="") oDoc.body.style.backgroundColor = bgcolor;
	oIframe.height = (fheight != "auto") ? fheight : (oDoc.body.offsetHeight !== undefined) ? oDoc.body.offsetHeight : oDoc.height;
	var oldstuff= oDoc.body.innerHTML;
	var newstuff=oldstuff.replace(/<a /g, "<a target='_top'");
	oDoc.body.innerHTML=newstuff;
	}

function addcss(item,file)
	{
	var link = item.createElement('link');
	link.setAttribute('rel', 'stylesheet');
	link.setAttribute('type', 'text/css');
	link.setAttribute('href', file);
	item.getElementsByTagName('head')[0].appendChild(link);
	}
