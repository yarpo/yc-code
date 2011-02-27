<?php
// ***********************************************************************
// ** Title.........:    plugin spadaj: obrona przed atakiem SQL Injection
// ** Version.......:    1.5.1
// ** Author........:    Jolanta Surma <jolaass@tlen.pl>
// ** Filename......:    spadaj.php 
// ** Last changed..:    07.03.2010
// ***********************************************************************/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System Spadaj Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemSpadaj extends JPlugin
{

	function plgSystemSpadaj(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterRender()
	{
    $plugin			=& JPluginHelper::getPlugin('system', 'spadaj');
	  $pluginParams	= new JParameter( $plugin->params );
    $haslo_szyfr = $pluginParams->get( 'haslo_szyfr' );
    $komunikat = $pluginParams->get( 'komunikat' );
    $ukryj_user = $pluginParams->get( 'ukryj_user' );
    $plik = $pluginParams->get( 'plik' );
    $zapis = $pluginParams->get( 'zapis' );
    $id_uzytkownika = (int)$pluginParams->get( 'id_uzytkownika' );
    $id_admina = (int)$pluginParams->get( 'id_admina' );
    $where = '';
    
    switch ($haslo_szyfr){ // opcje podmiany hasła
      case '0': //otwarty tekst
        $komunikat = $komunikat;
      break;
      case '1': //zaszyfrowany tekst - fałszywy
          jimport('joomla.user.helper');
      		$salt		= JUserHelper::genRandomPassword(32);
		      $crypt		= JUserHelper::getCryptedPassword($komunikat, $salt);
		      $komunikat	= $crypt.':'.$salt;
      break;    
    }
    
    switch ($ukryj_user){ //opcje wyboru ochrony uzytkowników
      case '0':
        $where = ' where gid = 25 ';
      break;
      case '1':
        $where = ' where gid in (23, 24, 25, 30)';
      break;  
      case '2':
        $where = $where;
      break;    
    }
     $db	= & JFactory::getDBO(); 
     $query = 'select password from #__users'.$where;
     $db->setQuery($query);
		 $db->query();
		 $wyniki = $db->loadObjectList();
		 $buffer = JResponse::getBody();
		 $jest = 0;
		 
		 foreach( $wyniki as $wynik) {
		    
		   if (preg_match("/".$wynik->password."/i",$buffer)) $jest = 1; //próba ataku
		   if($jest)   $buffer = str_replace($wynik->password, $komunikat, $buffer); //podmień hasło
		 }
		 
		 if ($jest == 1){ //próba ataku
		 
		    switch ($zapis){
		      case '0': // zapis do pliku
		        $dane = date("Y.m.d G:i")." IP: ".$_SERVER['REMOTE_ADDR']." Referer: ".$_SERVER['HTTP_REFERER']." Metoda ".$_SERVER['REQUEST_METHOD']." Agent ".$_SERVER['HTTP_USER_AGENT']." Connection: ".$_SERVER['HTTP_CONNECTION']."\n";
            $file = $plik;
            $fp = fopen($file, "a");
            flock($fp, 2);
            fwrite($fp, $dane);
            flock($fp, 3);
            fclose($fp);
		      break;
		      case '1':// info do admina
		        $sql = "insert into #__messages values(null, ".$id_uzytkownika.", ".$id_admina.", 0, now(), 0, 0, 'Atak SQL Injection','Próba przejęcia hasła')";
            $db->setQuery($sql);
		        $db->query();
		      break;
		      case '2':// zapis do pliku i info do admina
            $dane = date("Y.m.d G:i")." IP: ".$_SERVER['REMOTE_ADDR']." Referer: ".$_SERVER['HTTP_REFERER']." Metoda ".$_SERVER['REQUEST_METHOD']." Agent ".$_SERVER['HTTP_USER_AGENT']." Connection: ".$_SERVER['HTTP_CONNECTION']."\n";
            $file = $plik;
            $fp = fopen($file, "a");
            flock($fp, 2);
            fwrite($fp, $dane);
            flock($fp, 3);
            fclose($fp);         
            $sql = "insert into #__messages values(null,".$id_uzytkownika.", ".$id_admina.", 0, now(), 0, 0, 'Atak SQL Injection','Próba przejęcia hasła')";
            $db->setQuery($sql);
		        $db->query();
		      break;
		    }
     }
		JResponse::setBody($buffer);

	}
}