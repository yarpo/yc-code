/**
 * Classe che gestisce l'istanziazione dell'API SM2 e la registrazione degli eventi per gli alert sonori dei messaggi
 * @author 2Punti - Marco Biagioni 02/01/2011
 * @version 1 : marco 02/01/2010
 * @package FBChat::components::com_fbchat
 * @subpackage js
 */
//Dichiariamo l'oggetto come globale per poter accedere da fbchat.js
var msgNotify = {} || null; 

/*
 * Patch per microsoft IE
 */
if(jQuery.browser.msie){
soundManager.url='components/com_fbchat/sounds/swf/';
soundManager.debugMode = false; 
}
 

function messagesNotifier(mp3Msg,mp3Cli){
	/**
	 * Closure oggetto javascript
	 * @access private
	 * @var int
	 */
	var bindThis = this;
	/**
	 * Oggetto SoundManager 
	 * @access private
	 * @var int
	 */
	var SM2 = soundManager;
	/** 
	 * @access private
	 * @var int
	 */
	var testSoundDomSelector = 'div.fbchat_testaudio';
	/** 
	 * @access private
	 * @var int
	 */
	var pathToSwf = 'components/com_fbchat/sounds/swf/';
	/** 
	 * @access private
	 * @var int
	 */
	var pathToMp3 = 'components/com_fbchat/sounds/mp3/';
		
	//INIT SM2 API Object
	SM2.debugMode = false;
	SM2.url = pathToSwf;
	SM2.onready(function(){
		//Alert messaggi
		SM2.createSound({
 			id:'msgAlert',
 			url:pathToMp3 + mp3Msg
		}); 
		//Alert clients
		SM2.createSound({
 			id:'msgClients',
 			url:pathToMp3 + mp3Cli
		});
	});
	 
	/**
	 * Riproduce il suono all'arrivo dei messaggi 
	 * @access public
	 */
	this.playMessageAlert = function(){
		SM2.play('msgAlert'); 
	}
	/**
	 * Riproduce il suono all'arrivo degli utenti
	 * @access public
	 */
	this.playClientAlert = function(){ 
		SM2.play('msgClients');
	}
	/**
	 * Registra gli event handler sugli eventi della chat: messaggi e nuovi clients
	 * @access public 
	 */
	this.registerEvents = function(){
		/*N.B. il contesto (this) entro l'anonymous callback è l'oggetto su cui si richiama il metodo
		bind che viene passato con apply alla callback forzando il contesto*/
		//Register events onMessage
		jQuery(this).bind("onMessage",function(){
			this.playMessageAlert(); 
		});
		//Register events onClient
		jQuery(this).bind("onClient",function(){
			this.playClientAlert(); 
		});
		//Register events onClient
		jQuery('div.fbchat_testaudio').bind("click",function(){
			bindThis.playClientAlert(); 
		});
	}
}
