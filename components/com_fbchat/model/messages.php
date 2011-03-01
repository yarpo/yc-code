<?php
//namespace components\com_fbchat\model;
/** 
 * Gestore dei messaggi e dei dati 
 * @package FBChat::components::com_fbchat 
 * @subpackage model
 * @author 2Punti - Marco Biagioni
 * @version $Id: messages.php 28 04/01/2011 13:51:40Z marco $     
 * @copyright (C) 2011 - 2PUNTI SRL
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html  
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
global $response, $messages;

function getStatus() {
	global $response, $messages;
	
	$database = JFactory::getDBO();
	$my = JFactory::getUser();
	 
	$sql = ("SELECT message, status FROM #__fbchat_status where userid = " . $database->Quote($my->id)); 
	$database->setQuery($sql); 
	$chatList = $database->loadAssocList(); 
	$chat = $chatList[0];
	
	if (empty($chat['status'])) {
		$chat['status'] = 'available';
	} else {
		if ($chat['status'] == 'offline') {
			$_SESSION['jfbcchat_sessionvars']['buddylist'] = 0;
		}
	}
	
	if (empty($chat['message'])) {
		$chat['message'] = "-";
	}

	$status = array('message' => $chat['message'], 'status' => $chat['status']);
	$response['userstatus'] = $status;
}

function getLastTimestamp() {
	$database = JFactory::getDBO();
	
	if (empty($_POST['timestamp'])) {
		$_POST['timestamp'] = 0;
	}

	if ($_POST['timestamp'] == 0) {
		foreach ($_SESSION as $key => $value) {
			if (substr($key,0,15) == "jfbcchat_user_") {
				$temp = end($_SESSION[$key]);
				if ($_POST['timestamp'] < $temp['id']) {
					$_POST['timestamp'] = $temp['id'];
				}
			}
		}

		if ($_POST['timestamp'] == 0) {
			$sql = ("SELECT id from #__fbchat order by id desc limit 1");
			$database->setQuery($sql);
			$chatList = $database->loadAssocList();
			$chat = $chatList[0];
			
			$_POST['timestamp'] = $chat['id'];
		}
	}
	
}


function getBuddyList(&$parms) {
	global $response, $messages;
	
	$database = JFactory::getDBO();
	$my = JFactory::getUser();

	//Prendiamo il time per eventuale aggiornamento lista utenti buddylist
	$time = time();

	if ((empty($_SESSION['jfbcchat_buddytime'])) || ($_POST['initialize'] == 1) ||
	       (!empty($_SESSION['jfbcchat_buddytime']) && ($time-$_SESSION['jfbcchat_buddytime'] > $parms->get('buddylistrefresh')))) {

	    /*Quello che conta per considerare un utente in stato offline � il tempo dall'ultimo messaggio inviato
	      Al logout completo verr� tolta ogni connessione al refresh di pagina*/
		$sql = ("SELECT u.id, u.username, ".
		"sess.time AS lastactivity, ccs.message, ccs.status, MAX( fb.sent) AS lastmessagetime FROM ".
		"#__users AS u JOIN #__session AS sess on sess.userid = u.id ".
		"LEFT JOIN #__fbchat_status AS ccs ON u.id = ccs.userid ".
		"LEFT JOIN #__fbchat AS fb ON u.id = fb.from ".
		"WHERE u.id <> " . $database->Quote($my->id) . " AND sess.client_id = 0 GROUP BY u.id ORDER BY u.username asc");

		$database->setQuery($sql);
		$rows = $database->loadAssocList();
		 
		foreach ($rows as $chat) {
			if ((($time-$chat['lastmessagetime']) < $parms->get('lastmessagetime')) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') {
				if ($chat['status'] != 'busy') {
					$chat['status'] = 'available';
				}
			} else {
				$chat['status'] = 'offline';
			}

			if ($chat['message'] == null) {
				$chat['message'] = '';
			}

			$buddyList[] = array('id' => $chat['id'],
								 'name' => $chat['username'],
								 'status' => $chat['status'],
								 'message' => $chat['message'],
								 'time' => $chat['lastactivity'], 
								 'lastmessagetime' => $chat['lastmessagetime'] );
	 	}
		
	 	//Riaggiorniamo il time in sessione dell'ultimo refresh lista utenti
		$_SESSION['jfbcchat_buddytime'] = $time;

		if (!empty($buddyList)) {
			$response['buddylist'] = $buddyList;
		}
	    if(!empty($parms))
		{ 
			//eccezione page_description e page_title per 1.5: cloniamo e lasciamo l'oggetto originale intatto poi eseguiamo unset sul nostro oggetto parametri
			$response['paramslist'] = clone($parms->_registry['_default']['data']);
			unset($response['paramslist']->page_title);
			unset($response['paramslist']->page_description); 
		}
		$response['my_username'] = $my->username;
		
	}
}

function fetchMessages() {
	global $response, $messages, $componentParams;
	
	$database = JFactory::getDBO();
	$my = JFactory::getUser();
	
	//Sezione Garbage
	if((bool)$componentParams->get('enabled')){
		require_once 'garbage.php';
		$gc = new garbage($componentParams);
		//Exec GC Probability
		$execGC = $gc->execGC();
		//Back to JS domain
		$response['execGC'] = $execGC;
	}
	
	$timestamp = 0;
	
	$sql = ("SELECT cchat.id, cchat.from, cchat.to, cchat.message, 
			cchat.sent, cchat.read FROM #__fbchat AS cchat WHERE 
			(cchat.to = ". $database->Quote($my->id) . " OR cchat.from = ".
			$database->Quote($my->id)." ) AND (cchat.id > ".
			$database->Quote($_POST['timestamp'])." OR (cchat.to = ".$database->Quote($my->id).
			" and cchat.read != 1)) order by cchat.id");
	
	$database->setQuery($sql);
 	$rows = $database->loadAssocList();
  
 	foreach ($rows as $chatmessage) {
 		$self = 0;
		$old = 0;
		if ($chatmessage['from'] == $my->id) {
			$chatmessage['from'] = $chatmessage['to'];
			$self = 1;
			$old = 1;
		}
		$messages[] = array( 'id' => $chatmessage['id'],
							 'from' => $chatmessage['from'],
							 'message' => stripslashes($chatmessage['message']),
							 'self' => $self,
							 'old' => $old);
		
		//Mette i nuovi messaggi provenienti dal mittente in sessione se non propri, vecchi e gi� letti
		if ($self == 0 && $old == 0 && $chatmessage['read'] != 1) {
			$_SESSION['jfbcchat_user_'.$chatmessage['from']][] = array('id' => $chatmessage['id'],
																		'from' => $chatmessage['from'],
																		'message' => stripslashes($chatmessage['message']),
																		'self' => 0,
																		'old' => 1);
		}

		$timestamp = $chatmessage['id'];
 	}
 	 
 	//Adesso aggiorna lo stato dei messaggi come letti
	if (!empty($messages)) {
		$sql = "UPDATE #__fbchat SET `read` = '1' where `to` = " .
			    $database->Quote($my->id) . " and `id` <= " . $database->Quote($timestamp); 
			 
		$database->setQuery($sql); 
		$database->Query();
	}
}


?>