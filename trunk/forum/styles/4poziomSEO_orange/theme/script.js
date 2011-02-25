/*var clickMenu = function(menu) {
   var getEls = document.getElementsByTagName("LI");
   var getAgn = getEls;

   for (var i=0; i<getEls.length; i++) {
         getEls[i].onclick=function() {
            for (var x=0; x<getAgn.length; x++) {
            getAgn[x].className=getAgn[x].className.replace("click", "");
            }
            this.className+=" click";
            }
         }
      }

var stuHover = function() {
	var cssRule;
	var newSelector;
	for (var i=0; i< document.styleSheets.length; i++)
		for (var x=0; x< document.styleSheets[i].rules.length; x++)
			{
			cssRule = document.styleSheets[i].rules[x];
			if (cssRule.selectorText.indexOf("LI:hover") >= 0)
			{
				 newSelector = cssRule.selectorText.replace(/LI:hover/gi, "LI.iehover");
				document.styleSheets[i].addRule(newSelector , cssRule.style.cssText);
			}
		}
	var getElm = document.getElementById("nav").getElementsByTagName("LI");
	for (var i=0; i<getElm.length; i++) {
		getElm[i].onmouseover=function() {
			this.className+=" iehover";
		}
		getElm[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" iehover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", stuHover);*/

function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes,width=' + width);
	return false;
}

function jumpto()
{
	var page = prompt('{LA_JUMP_PAGE}:', '{ON_PAGE}');
	var per_page = '{PER_PAGE}';
	var base_url = '{A_BASE_URL}';

	if (page !== null && !isNaN(page) && page == Math.floor(page) && page > 0){
		if (base_url.indexOf('?') == -1){
			document.location.href = base_url + '?start=' + ((page - 1) * per_page);
		}
		else
		{
			document.location.href = base_url.replace(/&amp;/g, '&') + '&start=' + ((page - 1) * per_page);
		}
	}
}
/**
* Find a member
*/
function find_username(url){
	popup(url, 760, 570, '_usersearch');
	return false;
}

/**
* Mark/unmark checklist
* id = ID of parent container, name = name prefix, state = state [true/false]
*/
function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	if (!parent)
	{
		eval('parent = document.' + id);
	}

	if (!parent)
	{
		return;
	}

	var rb = parent.getElementsByTagName('input');
	
	for (var r = 0; r < rb.length; r++)
	{
		if (rb[r].name.substr(0, name.length) == name)
		{
			rb[r].checked = state;
		}
	}
}
