<?php
//namespace components\com_fbchat;
/**
 * Receiver/Responder delle richieste AJAX 
 * @package FBChat::components::com_fbchat 
 * @author 2Punti - Marco Biagioni
 * @version $Id: fbchat_receiver.php 56 02/01/2011 21:21:20Z marco $     
 * @copyright (C) 2011 - 2PUNTI SRL
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html  
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
//Id utente corrente
$userObj = JFactory::getUser();
$userid = $userObj->id; 
//Inizializzazione variabili CGI
global $response, $messages, $componentParams;

$response = array();
$messages = array();
$forceParams = JRequest::getVar('getParams');
$chatbox = JRequest::getVar('chatbox');
$buddylist = JRequest::getVar('buddylist');
$initialize = JRequest::getVar('initialize');
$update_session = JRequest::getVar('updatesession');
$post_sessionvars = JRequest::getVar('sessionvars');

//Inizializzazione variabili di sessione
$fbchat_user = $_SESSION['jfbcchat_user_'.$chatbox];
$fbchat_sessionvars = $_SESSION['jfbcchat_sessionvars'];
//$openChatBoxId � l'id dell'utente dell'ultimo box aperto
$openChatBoxId = $_SESSION['jfbcchat_sessionvars']['openChatboxId'];
//$openChatBoxIdUserMessages � un array contentente i messaggi dell'utente dell'ultimo box aperto mantenuti in sessione
$openChatBoxIdUserMessages = $_SESSION['jfbcchat_user_'.$openChatBoxId];
  
require_once JPATH_COMPONENT . '/model/messages.php';

if ($userid != 0) {
	if (!empty($chatbox)) {
		if (!empty($fbchat_user)) {                 
			$messages = $fbchat_user;
		}
		getLastTimestamp(); 
		sendResponse();
	} else {
		if (!empty($buddylist) && $buddylist == 1) { 
			getBuddyList($componentParams); }
		if (!empty($initialize) && $initialize == 1) { 
			getStatus(); 
			if (!empty($fbchat_sessionvars)) {
				$response['initialize'] = $fbchat_sessionvars;
			
				if (!empty($openChatBoxId) && !empty($openChatBoxIdUserMessages)) {
					$messages = array_merge($messages,$openChatBoxIdUserMessages);
				}
			}
		} else {
			
			if (empty($fbchat_sessionvars)) {
				$fbchat_sessionvars = array();
			}

			if (!empty($post_sessionvars)) {
				ksort($post_sessionvars);
			} else {
				$post_sessionvars= '';
			}

			if (!empty($update_session) && $update_session == 1) { 
				$_SESSION['jfbcchat_sessionvars'] = $post_sessionvars;
			}

			if ($_SESSION['jfbcchat_sessionvars'] != $_POST['sessionvars']) {
				$response['updatesession'] = $_SESSION['jfbcchat_sessionvars'];
			}
			
			if($forceParams){
				$response['paramslist'] = clone($componentParams->_registry['_default']['data']);
			}
		}
		
		//Otteniamo l'id dell'ultimo messaggio inviato
		getLastTimestamp();
		//Otteniamo la lista messaggi
		fetchMessages();
		sendResponse();
	} 
} else {
	$response['loggedout'] = '1'; 
	if($forceParams){
		$response['paramslist'] = clone($componentParams->_registry['_default']['data']);
	}
	sendResponse();
}

function sendResponse()
{
	global $messages, $response;
	
	if (!empty($messages)) {
		$response['messages'] = $messages;
	}

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($response);
	exit;
}

?>