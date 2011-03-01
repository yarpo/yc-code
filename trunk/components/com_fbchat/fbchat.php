<?php
//namespace components\com_fbchat;
/**
 * Entrypoint dell'application di frontend
 * @package FBChat::components::com_fbchat
 * @author 2Punti - Marco Biagioni
 * @version $Id: fbchat.php 38 05/01/2011 16:51:40Z marco $     
 * @copyright (C) 2011 - 2PUNTI SRL
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); 
global $response, $messages,$componentParams; 
//Task che funge da entrypoint controller
$entrypoint = JRequest::getVar('entrypoint','receive');
//Get dei parametri del componente
$componentParams = getConfig();
  
switch($entrypoint)
{
	case 'receive':
		require_once 'fbchat_receiver.php';
		break;
		
	case 'send':
		require_once 'fbchat_sender.php';
		break;
}
 
function &getConfig()
{

	$params = JComponentHelper::getParams('com_fbchat');
	return $params;
}
?>                                                        