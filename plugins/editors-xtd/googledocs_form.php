<?php
// Set flag that this is a parent file
// polish version by jjk Youthcoders.net
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

		
JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

 // CREATE THE APPLICATION
$app = JRequest::getString('app', 'site');

$app = $app == 'site' ? 'site' : ($app == 'administrator' ? 'administrator' : 'site');
$app_path = $app == 'site' ? '../..' : '../../administrator';

$mainframe =& JFactory::getApplication($app);
$doc =& JFactory::getDocument();

/** @var jlanguage $lang */
$lang =& JFactory::getLanguage();
$lang->load('plg_editors-xtd_googledocs', realpath(JPATH_BASE.DS.'administrator'));

JHTML::_('behavior.mootools');
jimport( 'joomla.plugin.plugin' ) ;
jimport( 'joomla.plugin.helper' ) ;
$plugin = & JPluginHelper::getPlugin( 'content', 'googledocs' ) ;
$pluginParams = new JParameter( $plugin->params ) ;

$doctypes[] =  JHTML::_('select.option',  'presentation', JText::_( 'Prezentacje' ));
$doctypes[] =  JHTML::_('select.option',  'spreadsheet', JText::_( 'Arkusz kalkulacyjny' ));
$doctypes[] =  JHTML::_('select.option',  'document' , JText::_( 'Doc' ));
$doctypelist = JHTML::_('select.genericlist', $doctypes, 'doctype', 'onchange="doGDocRequest()"', 'value', 'text', $pluginParams->get('default_type') );

$sizes[] =  JHTML::_('select.option',  's', JText::_( 'mały' ));
$sizes[] =  JHTML::_('select.option',  'm', JText::_( 'średni' ));
$sizes[] =  JHTML::_('select.option',  'l' , JText::_( 'duży' ));
$sizelist = JHTML::_('select.genericlist', $sizes, 'size', null, 'value', 'text', $pluginParams->get('default_size') );

$frameborderlist = JHTML::_('select.integerlist', 0, 4, 1, 'frameborder', null, $pluginParams->get('frameborder'));
$juri = JURI::getInstance();
 ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="<?php echo $app_path ?>/templates/<?php echo $mainframe->getTemplate() ?>/css/template.css" />
<title>GoogleDocs Plugin Helper</title>
		<script type="text/javascript" src="../../media/system/js/mootools.js"></script>
		<script type="text/javascript" src="<?php echo $juri->getScheme() ?>://www.google.com/jsapi"></script>
		<script type="text/javascript">
		// Load the Google data JavaScript client library
      	google.load("gdata", "1.x");
		// Set init() to be called after JavaScript libraries load
      google.setOnLoadCallback(init);

      // Object for storing constants and global variables
      var gDocListAPI = {
        CURRENT_DOCUMENT: null,
        SELECTED_DOCTYPE: null,
        AUTHENTICATION_URL: '<?php echo $juri->getScheme() ?>://docs.google.com/feeds/documents',
        PERSONAL_DOC_LIST_URL: '<?php echo $juri->getScheme() ?>://docs.google.com/feeds/documents/private/full',
        
        AUTHENTICATION_LINK: 'authenticationLink',
        MAIN_CONTENT_TABLE: 'mainContentTable',
        STATUS_CONTAINER: 'statusContainer',
        DOCTYPE_SELECT: 'doctype',
        DOCUMENT_SELECT: 'document_entries'
      };

      /**
       * Onload handler: calls two initialization subroutines
       */
      function init() {
        // Create service object for accessing private feeds using AuthSub
        gDocListAPI.service =
            new google.gdata.client.GoogleService("writely", "JoomlaPlugin-GoogleDocEmbed");
        initializeUI();
    } 
    /**
    * Resets interface and retrieves user's private blog feed if user is
    * signed in
    */
    function initializeUI() {
        var authenticationLink = $(gDocListAPI.AUTHENTICATION_LINK);
        
        $("authenticationDiv").setStyle( "display", "" );

        if (google.accounts.user.checkLogin(gDocListAPI.AUTHENTICATION_URL)) {
        	if( opener ) { opener.initializeUI(); window.close(); }
        	$("document_entries_row").setStyle( "display", "" );
			doGDocRequest();
          	authenticationLink.setHTML("<?php echo JText::_('Sign out', true) ?>");
          	authenticationLink.onclick = signOut;
        } else {
          	$("document_entries_row").setStyle( "display", "none" );
          	$(gDocListAPI.STATUS_CONTAINER).setStyle("display", "none");
          	authenticationLink.setHTML("<?php echo JText::_('Sign in', true) ?>");
          	authenticationLink.onclick = signIn;
        }

    }
	/**
	* Function to retrieve the document feed from Google
	* Automatically filters by selected Document Type
	*/
    function doGDocRequest() {
    	if (google.accounts.user.checkLogin(gDocListAPI.AUTHENTICATION_URL)) {
    		$(gDocListAPI.DOCUMENT_SELECT).setStyle("display", "none");
    		$(gDocListAPI.STATUS_CONTAINER).setStyle("display", "");
			gDocListAPI.service.getFeed(gDocListAPI.PERSONAL_DOC_LIST_URL + "/-/" + $(gDocListAPI.DOCTYPE_SELECT).getValue(),
                                          getDocFeedHandler, errorHandler, google.gdata.atom.Feed);
        }
	}
      /**
       * Requests an AuthSub token for interaction with the Blogger service
       */
      function signIn() {
      	var token = google.accounts.user.checkLogin(gDocListAPI.AUTHENTICATION_URL);
      	if( !token ) {
      		var authSubWin = window.open("<?php echo $juri->getScheme() ?>://www.google.com/accounts/AuthSubRequestJS?session=1&scope=http://docs.google.com/feeds/documents&next=<?php echo urlencode( JURI::base() ."googledocs_form.php?app=site&e_name=text" ) ?>", "authsub", "width=600,height=400,scrollbars" );
      		authSubWin.onclose = initializeUI;
      	}
      }

      /**
       * Revokes the AuthSub token and resets the interface
       */
      function signOut() {
        google.accounts.user.logout();
        initializeUI();
      }

      /**
       * Called after successful retrieval of document feed; populates drop-down
       * menu with titles of user's documents
       *
       * @param {Object} docFeedRoot Feed object containing collection of 
       * 							document entries
       */
      function getDocFeedHandler(docFeedRoot) {
      	$(gDocListAPI.STATUS_CONTAINER).setStyle("display", "none");
      	$(gDocListAPI.DOCUMENT_SELECT).setStyle("display", "block");
        var docArr = docFeedRoot.feed.getEntries();
        var documentSelect = $(gDocListAPI.DOCUMENT_SELECT);
        
        for (var i = 0; i < docArr.length; i++) {
          var newOption = document.createElement("option");
          var docTitle = docArr[i].getTitle().getText();
          linkArr = docArr[i].getLinks();
          var docLocation = linkArr[0].getHref();

          newOption.value = docLocation;
          newOption.innerHTML = docTitle;
          documentSelect.appendChild(newOption);
        }
        
      }

      /**
       * Called if Google Docs service is unable to retrieve a feed or insert an
       * entry properly; creates a popup alert notifying user of error of cause
       * 
       * @param {Object} e Object containing error information
       */
      function errorHandler(e) {
        alert(e.cause.status ? e.message + " (" + e.cause.status + ")" : e.message);
      }
function getDocId( link ) {
	lastPos = link.indexOf("&") > 10 ? null : link.indexOf("&");
	return link.substring(  link.indexOf("=")+1  );
}
function setDocId( id ) {
	$("docid").value = id;
}
function insertGoogleDoc(editor) {
	// Get the pagebreak title
	var docid = $("docid").getValue();
	if( docid == "" ) {
		alert( "<?php echo JText::_( 'Document ID is required', true ); ?>" );
		$("docid").focus();
		return false;
	}
	var type = $("doctype").getValue();
	if( type == "document" ) type = "doc";
	var size = $("size").getValue();
	var frameborder = $("frameborder").getValue();
	var width = $("width").getValue();
	var height = $("height").getValue();
	
	if (width != '') {
		width = " width=\""+width+"\" ";
	}
	if (height != '') {
		height = " height=\""+height+"\" ";
	}
	
	var tag = " {GoogleDoc docid=\""+docid+"\" type=\"" + type + "\"  size=\"" + size + "\" frameborder=\"" + frameborder + "\""+ width + height + "} ";

	window.parent.jInsertEditorText(tag, "<?php echo preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name') ); ?>");
	window.parent.document.getElementById('sbox-window').close();
	return false;
}
/**
*	Debug Function, that works like print_r for Objects in Javascript
*/
function var_dump(obj) {
	var vartext = "";
	for (var prop in obj) {
		if( isNaN( prop.toString() )) {
			vartext += "\t->"+prop+" = "+ eval( "obj."+prop.toString()) +"\n";
		}
    }
   	if(typeof obj == "object") {
    	return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "") + "\n" + vartext;
   	} else {
      	return "Type: "+typeof(obj)+"\n" + vartext;
	}
}
</script>
		
</head>
<body>
	<div style="width:30%;" align="left" style="display:none;" id="authenticationDiv"><img src="../../administrator/images/checked_out.png" alt="lock" align="left" />
		<a href="#" id="authenticationLink" title="<?php echo JText::_('Zaloguj się w GoogleDocs') ?>"></a></div>
<h1>&nbsp;&nbsp;&nbsp;<img src="googledocs.gif" alt="GoogleDocs" align="middle" />&nbsp;&nbsp;
	Wstaw Dokument GoogleDocs</h1>
<form>
		<table style="margin-left: 15px;width:90%;" class="admintable" id="mainContentTable">
			<tr>
				<td width="30%" class="key" align="right">
					<label for="title"><?php echo JText::_( 'Typ Dokumentu' ); ?></label>
				</td>
				<td width="70%"><?php echo $doctypelist ?>	</td>
			</tr>
			<tr style="display:none;" id="document_entries_row">
				<td class="key" align="right"><?php echo JText::_( 'Wybierz Dokument' ); ?></td>
				<td>
					<select name="document_entries" id="document_entries" onchange="setDocId(getDocId(this.getValue()))"><option value=""><?php echo JText::_( 'Wybierz...' ); ?></option></select>
					<span id="statusContainer"><img src="googledocs_indicator.gif" alt="-*-"  align="middle" /> <?php echo JText::_( 'Ładowanie...') ?></span>
				</td>
			</tr>
			<tr>
				<td class="key" align="right">
					<label for="alias"><?php echo JText::_( 'ID Dokumentu' ); ?></label>
				</td>
				<td>
					<input type="text" id="docid" name="docid" class="inputbox" />
				</td>
			</tr>
			<tr>
				<td class="key" align="right"><label for="size"><?php echo JText::_( 'Wielkość' ); ?></label></td>
				<td><?php echo $sizelist ?></td>
			</tr>
			<tr>
				<td class="key" align="right"><?php echo JText::_( 'Wysokość' ). ' x '.JText::_( 'szerokość' ); ?></td>
				<td><input type="text" id="height" name="height" size="5" class="inputbox" value="<?php echo $pluginParams->get('iframe_height_custom') ?>" /> x
				<input type="text" id="width" name="width" size="5" class="inputbox" value="<?php echo $pluginParams->get('iframe_width_custom') ?>" /></td>
			</tr>
			<tr>
				<td class="key" align="right"><label for="frameborder"><?php echo JText::_( 'Obramowanie' ); ?></label></td>
				<td><?php echo $frameborderlist ?></td>
			</tr>
			<tr>
			<td colspan="2">
			<br /><br />
		<input type="button" class="button" onclick="insertGoogleDoc();" value="<?php echo JText::_( 'Wstaw kod Joomla generujący wyświeltanie dokumentu' ); ?>" />
		</td>
		</table>
		</form>
		
</body>
</html>