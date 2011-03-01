// trasnlation module for fbchat
// http://www.2punti.eu/joomla-facebook-chat.html?lang=en
// @author: Patryk yarpo Jar
// @data  : 01-03-2011 

var yTranslation = function( setup )
{
	setup = setup || {};
	google.load("language", "1");

	var sMsg;

	function CONST( key )
	{
		var c = {
			msgBox           : '.jfbcchat_chatboxmessage',
			msgContent       : '.jfbcchat_chatboxmessagecontent',
			addedTranslation : '.jfbcchat_ycAddedTranslation',
			translationFreq  : setup.frequency || 1000,
			languageOut      : setup.languageOut || 'en'
		};

		return c[key] || 'no-such-key';
	}

	function fProccedAll( frequency )
	{
		window.setTimeout(function() {
			$(CONST('msgBox')).each(fProceedBox);
		}, CONST('translationFreq'));
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
		fProceedTheBox($(this));
	}

	function fProceedTheBox(box)
	{
		if (box.find(CONST('addedTranslation')).size() == 0)
		{
			var msg = box.find(CONST('msgContent')).text();
			google.language.detect(msg, function(detLang) { fDetection(detLang, box, msg)});
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
			box.html(box.html() + fCreateTranslationBox(result.translation));
		}
	}

	return {
		proceed : fProccedAll,
		proceedBox : fProceedTheBox
	};
};

$(document).ready(yTranslation().proceed);

