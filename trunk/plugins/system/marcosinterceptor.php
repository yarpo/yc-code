<?php
/*
 * A plugin that sanitize input on all external request
 * Plugin for Joomla 1.5 - Version 0.98.0
 * License: http://www.gnu.org/copyleft/gpl.html
 * Authors: marco maria leoni
 * Copyright (c) 2010 marco maria leoni web consulting - http: www.mmleoni.net
 * Project page at http://www.mmleoni.net/sql-iniection-lfi-protection-plugin-for-joomla
 * *** Last update: May 30th, 2010 ***
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
class plgSystemMarcosinterceptor extends JPlugin{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemMarcosinterceptor( &$subject, $config ){
		parent::__construct( $subject, $config );
	}

	function onAfterInitialise(){
		global $mainframe;
		$p_dbprefix = $mainframe->getCfg('dbprefix');
		$p_raiseError = $this->params->get('raiseerror', 1);
		$p_errorCode = intval($this->params->get('errorcode', 500));
		$p_errorMsg = $this->params->get('errormsg', 'Internal Server Error');
		$p_strictLFI = $this->params->get('strictlfi', 1);
		$p_levelLFI = intval($this->params->get('levellfi', 1));
		$p_frontEndOnly = $this->params->get('frontendonly', 1);
		$p_ignoredExts = $this->params->get('ignoredexts','');
		$p_sendNotification = $this->params->get('sendnotification',0);
		$p_nameSpaces = $this->params->get('namespaces','GET,POST');
		
		if (($p_frontEndOnly) AND (strpos($_SERVER['REQUEST_URI'], '/administrator') === 0)) return;
		
		$p_ignoredExts = explode(',', preg_replace('/\s*/', '', $p_ignoredExts));
		if (isset($_REQUEST['option']) AND in_array($_REQUEST['option'], $p_ignoredExts)) return;

		$wr=array();
		foreach(explode(',', $p_nameSpaces) as $nsp){
			switch ($nsp){
				case 'GET':
					$nameSpace =& $_GET;
					break;
				case 'POST':
					$nameSpace =& $_POST;
					break;
				case 'COOKIE':
					$nameSpace =& $_COOKIE;
					break;
				case 'REQUEST':
					$nameSpace =& $_REQUEST;
					break;
			}
			foreach($nameSpace as $k => &$v){
			
				if(is_numeric($v)) continue;
				if(is_array($v)) continue;
			
				/* SQL injection */
				// strip /* comments */
				$a = preg_replace('!/\*.*?\*/!s', ' ', $v); 
				/* union select ... jos_users */
				if (preg_match('/UNION(?:\s+ALL)?\s+SELECT/i', $a)){
					$wr[] = "** Union Select [$nsp:$k] => $v"; 
					if($p_raiseError){
						JError::raiseError($p_errorCode, $p_errorMsg);
						return;
					}else{
						$v = preg_replace('/UNION(?:\s+ALL)?\s+SELECT/i', '--', $a);
					}
				}

				/* table name */
				$ta = array ('/\s`?+(#__)/', '/\s+`?(jos_)/i', "/\s+`?({$p_dbprefix}_)/i");
				foreach ($ta as $t){
					if (preg_match($t, $v)){
						$wr[] = "** Table name in url [$nsp:$k] => $v";
						if($p_raiseError){
							JError::raiseError($p_errorCode, $p_errorMsg);
							return;
						}else{
							$v = preg_replace($t, ' --$1', $v);
						}
					}
				}
				
				/* LFI */
				if ($p_strictLFI){
					if (!in_array($k, array('controller', 'view', 'model', 'template'))) continue;
				}
				$recurse = str_repeat('\.\.\/', $p_levelLFI+1);
				$i=0;
				while (preg_match("/$recurse/", $v)){
					if(!$i) $wr[] = "** Local File Inclusion [$nsp:$k] => $v";
					if($p_raiseError){
						JError::raiseError($p_errorCode, $p_errorMsg);
						return;
					}else{
						$v = preg_replace('/\.\.\//', '', $v);
					}
					$i++;
				}
				unset($v);
			} // namespace
		} //namespaces
		if(($p_sendNotification) AND ($wr)) $this->sendNotification($wr);

	}
	function sendNotification($warnings){
		global $mainframe;
		$p_sendTo = $this->params->get('sendto','');
		if(!$p_sendTo) $p_sendTo = $mainframe->getCfg('mailfrom');
		
		$warning = implode("\r\n", $warnings);
		$warning .= "\r\n\r\n";

		$warning .= "**PAGE / SERVER INFO\r\n";
		$warning .= "\r\n\r\n";
		foreach(explode(',', 'REMOTE_ADDR,HTTP_USER_AGENT,REQUEST_METHOD,QUERY_STRING,HTTP_REFERER') as $sg){
			if(!isset($_SERVER[$sg])) continue;
			$warning .= "*{$sg} :\r\n{$_SERVER[$sg]}\r\n\r\n";
		}
		$warning .= "\r\n\r\n";
		
		$warning .= "** SUPERGLOBALS DUMP (sanitized)\r\n";
		
		$warning .= "\r\n\r\n";
		$warning .= '*$_GET DUMP';
		$warning .= "\r\n";
		foreach($_GET as $k => $v){
			$warning .= " -[$k] => $v\r\n";
		}

		$warning .= "\r\n\r\n";
		$warning .= '*$_POST DUMP';
		$warning .= "\r\n";
		foreach($_POST as $k => $v){
			$warning .= " -[$k] => $v\r\n";
		}

		$warning .= "\r\n\r\n";
		$warning .= '*$_COOKIE DUMP';
		$warning .= "\r\n";
		foreach($_COOKIE as $k => $v){
			$warning .= " -[$k] => $v\r\n";
		}

		$warning .= "\r\n\r\n";
		$warning .= '*$_REQUEST DUMP';
		$warning .= "\r\n";
		foreach($_REQUEST as $k => $v){
			$warning .= " -[$k] => $v\r\n";
		}
		
		jimport('joomla.mail.mail');
		$mail = new JMail();
		$mail->setsender($mainframe->getCfg('mailfrom'));
		$mail->addRecipient($p_sendTo);
		$mail->setSubject($mainframe->getCfg('sitename') . ' Marco\'s interceptor warning ' );
		$mail->setbody($warning);
		//$mail->useSMTP(true, "smtpserver", "smtpusername", "smtppassword");
		$mail->send();		
	}
}