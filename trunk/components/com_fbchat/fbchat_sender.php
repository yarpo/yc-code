<?php
//namespace components\com_fbchat;
/**
 * Sender dei messaggi 
 * @package FBChat::components::com_fbchat 
 * @author 2Punti - Marco Biagioni
 * @version $Id: fbchat_sender.php 42 04/01/2011 18:30:40Z marco $     
 * @copyright (C) 2011 - 2PUNTI SRL
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html  
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$database = JFactory::getDBO();
$my = JFactory::getUser();
	
//Inizializzazione variabili CGI
$status = JRequest::getVar('status');
$statusmessage = JRequest::getVar('statusmessage');
$message = JRequest::getVar('message');
$to = JRequest::getVar('to');

//Settaggio status dell'utente
if (!empty($status)) { 
	$sql = ("INSERT INTO #__fbchat_status (userid,status) values ($my->id, " . $database->Quote($status) . ") " . 
			"ON DUPLICATE KEY UPDATE status = " . $database->Quote($status));
    $database->setQuery($sql);
	$database->query();
	
	if ($status == 'offline') {
		$_SESSION['jfbcchat_sessionvars']['buddylist'] = 0;
	}

	echo "1";
	exit(0);
}

//Settaggio messaggio status personalizzabile da keyevent 'invio' dal campo testo
if (!empty($statusmessage)) { 
	$sql = ("INSERT INTO #__fbchat_status (userid,message) values ($my->id, ". $database->Quote($statusmessage).
			") ON DUPLICATE KEY UPDATE message = ".$database->Quote($statusmessage));
    $database->setQuery($sql);
	$database->query();
	
	echo "1";
	exit(0);                               
}
 
if (!empty($to) && !empty($message)) {  
	if ($my->id != '') {
		$sql = ("INSERT INTO #__fbchat (#__fbchat.from,#__fbchat.to,#__fbchat.message,#__fbchat.sent,#__fbchat.read) values (".
				$database->Quote($my->id).", ".$database->Quote($to).",".$database->Quote($message). ",UNIX_TIMESTAMP(NOW()),0)");
	    $database->setQuery($sql);
		$database->query();

		if (empty($_SESSION['jfbcchat_user_'.$to])) {
			$_SESSION['jfbcchat_user_'.$to] = array();
		}
		//Memorizziamo in sessione locale mittente il messaggio inviato al to destinatario
		 $_SESSION['jfbcchat_user_'.$to][] = array("id" => $database->insertid(), "from" => $to, "message" => $message, "self" => 1, "old" => 1) ;
		 
		echo $database->insertid();
		exit(0);
	}

}
 