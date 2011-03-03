// trasnlation module for fbchat
// http://www.2punti.eu/joomla-facebook-chat.html?lang=en
// @author: Patryk yarpo Jar [facebook.com/patryk.yarpo.jar]
// @data  : 01-03-2011 
// @uses  : jquery
// @uses  : google.language

var yTranslation = function( setup )
{
	setup = setup || {};

	var sMsg;

	function CONST( key )
	{
		var c = {
			msgBox           : '.jfbcchat_chatboxmessage',
			msgContent       : '.jfbcchat_chatboxmessagecontent',
			addedTranslation : '.jfbcchat_ycAddedTranslation',
			languageOut      : setup.languageOut || 'en'
		};

		return c[key] || 'no-such-key';
	}

	function fProccedAll( box )
	{
		//$( box || CONST('msgBox')).each(fProceedBox);
		console.log('wywoluje');
	}

	function fCreateTranslationBox( content )
	{
		var c = [];
		c.push('<span class="' + CONST('addedTranslation').substr(1) + '">');
		c.push(content);
		c.push('</span>');
		return c.join('');
	}

	function fProceedBox()
	{
		debug('fProceedBox');
		fProceedTheBox($(this));
	}

	function fProceedTheBox(box)
	{
		alert('fProceedTheBox');
		return;
		debug('fProceedTheBox');
		if (box.find(CONST('addedTranslation')).size() == 0)
		{
			debug('fProceedBox, wystapiec ' + box.find(CONST('addedTranslation')).size());
			var msg = box.find(CONST('msgContent')).text();
			debug('msg: ' + msg);
			google.language.detect(msg, function(detLang) { debug('lang: ' + detLang); fDetection(detLang, box, msg)});
		}
		else
		{
			debug('NIE TLUMACZE BO BYLO');
		}
		
	}

	function fDetection(detection, box, msg)
	{
		if (!detection.error && (detection.language != CONST('languageOut')))
		{
			google.language.translate(msg, detection.language, CONST('languageOut'), 
				function(result) { fTranslate(result, box); });
		}
	}

	function fTranslate(result, box)
	{
		if (!result.error)
		{
			debug(result.translation);
			box.html(box.html() + fCreateTranslationBox(result.translation));
		}
	}

	return {
		proceed : fProccedAll,
		proceedBox : fProceedTheBox
	};
};

function debug(v)
{
	if (console && console.log)
	{
		console.log(v);
	}
}

