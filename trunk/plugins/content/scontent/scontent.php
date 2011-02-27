<?php
/**
 * Scontent plugin for Joomla! 1.5
 * @package    Joomla
 * @subpackage Content Plugin
 * @license    GNU/GPL
 * Based on WMT Like It plugin
*/

// Set flag that this is a parent file
define('_JEXEC', 1);

// no direct access
defined('_JEXEC') or die('Restricted access');

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');
jimport('joomla.plugin.plugin');
jimport( 'joomla.plugin.helper' );
        
	$mainframe = &JFactory::getApplication('site');
	$mainframe->initialise();
	
	// load language
	JPlugin::loadLanguage('plg_content_scontent', JPATH_ADMINISTRATOR);
	
	$aid = JRequest::getInt('article_id', 0, 'get');
	$task = JRequest::getVar('task');
	$user = &JFactory::getUser();	
	$site_email = & JFactory::getConfig('mailfrom');
	$plugin = &JPluginHelper::getPlugin('content', 'scontent');
	$params = new JParameter($plugin->params);
						 	
	$disable_guests = $params->get('disable_guests', 'no');	
	$voting_period = $params->get('voting_period','once');
        $report_to = $params->get('report_to',$site_email);
        
	if($task=='votedown' && (int)$aid>0){
          $status_code = storeVotes($aid, $disable_guests, $voting_period, 'down_count' ); 
        } elseif($task=='voteup' && (int)$aid>0) {
          $status_code = storeVotes($aid, $disable_guests, $voting_period, 'up_count' );
        } elseif($task=='report' && (int)$aid>0) {
          $status_code = sendReport($aid);
        } else {
          $jsondata['msg']='No article or task selected';
          echo json_encode($jsondata);
        }

function storeVotes($aid, $disable_guests, $voting_period,$task ) {
	$db  = &JFactory::getDBO();
	$jsondata = array();
	$jsondata['article'] = 	$aid;
	//get user IP
	$userIP = GetUserIp();
	$user =& JFactory::getUser(); 
                 if($user->guest) {
                     (int)$userid = 0;
                    } else {
                      (int)$userid = $user->id;
         }
         
         if($userid > 0){
           $andquery = 'user_id = "'.$userid.'"';
         } else {
           $andquery = 'last_ip = "'.(string)$userIP.'"';
         }
         
	$query = 'SELECT *' . ' FROM #__scontent_votes' . ' WHERE content_id = ' . $aid.' AND '.$andquery;		
	$db->setQuery($query);
		if (!$db->query()) {
			$jsondata['msg'] = JText::_('PLEASE ENABLE CHECK DB TABLE OPTIONS! (SELECT YES)');
			echo json_encode($jsondata);
		} else {			
            $votes = $db->loadObject();		
			
            $nowdate = gmdate('Ymd');
	    $lastvotedate = $votes->date;
			
            $r_date = $nowdate - $lastvotedate;         
            
             $alreadyvoted = false;
             if($user->guest && $userIP == ($votes->last_ip)) {
            	    $alreadyvoted = true;
             }
             if($userid > 0 && $userid == ($votes->user_id)) {
		    $alreadyvoted = true;
	     }
	     
            if ($disable_guests == 'yes' && $user->guest) {
				$jsondata['msg'] = JText::_('ONLY LOGGED USERS CAN VOTE!');
				$counts = getCount($aid);
			        $jsondata['count'] = $counts[2];
				echo json_encode($jsondata);
            } else {
            	if ($voting_period === 'once' && $alreadyvoted) {
		    $jsondata['msg'] = JText::_('ALREADY VOTED! YOU CAN VOTE ONCE');
		    $counts = getCount($aid);
		    $jsondata['count'] = $counts[2];
		    echo json_encode($jsondata);
            	} else {            
					if ( !$votes ) {
						$query = "INSERT INTO #__scontent_votes ( content_id, user_id, ".$task.", last_ip, date)"
						. "\n VALUES ( " . $aid . ", ".$userid." , 1 , " . $db->Quote( $userIP ) . ", ".$nowdate."  )";
						$db->setQuery( $query );
						$db->query() or die( $db->stderr() );
						$counts = getCount($aid);						
						$query = "UPDATE #__scontent_votes_totals SET total_count = ".$counts[2]." WHERE content_id = ".$aid;
						$db->setQuery( $query );
						if($db->query()!==false && mysql_affected_rows()==0) {
						$query = "INSERT INTO #__scontent_votes_totals ( content_id, total_count) VALUES (".$aid.",".$counts[2].")";
						$db->setQuery( $query );
						$db->query() or die( $db->stderr() );
						}
						$jsondata['msg'] = JText::_('THANK YOU FOR VOTE!');
			                        $jsondata['count'] = $counts[2];
						echo json_encode($jsondata);
		
					} else {
						if ($alreadyvoted && $voting_period <= $r_date) {
						                 if($userid > 0){
                                                                  $andquery = 'user_id = "'.$userid.'"';
                                                                  } else {
                                                                   $andquery = 'user_id = "0" AND last_ip = "'.$userIP.'"';
                                                                 }
							$query = "UPDATE #__scontent_votes"
							. "\n SET ".$task." = ".$task." + 1, last_ip = " .$db->Quote( $userIP ).", date = ".$nowdate
							. "\n WHERE content_id = " . $aid ." AND ".$andquery;
							$db->setQuery( $query );
							if($db->query()!==false && mysql_affected_rows()==0) {
						          $query = "INSERT INTO #__scontent_votes ( content_id, user_id, ".$task.", last_ip, date)"
						          . "\n VALUES ( " . $aid . ", 0 , 1 , " . $db->Quote( $userIP ) . ", ".$nowdate."  )";
						          $db->setQuery( $query );
						          $db->query() or die( $db->stderr() );
						        }
							$counts = getCount($aid);				
							$query = "INSERT INTO #__scontent_votes_totals ( content_id, total_count) VALUES (".$aid.",".$counts[2].")";
							$query .= " ON DUPLICATE KEY UPDATE total_count = ".$counts[2]."";
							$db->setQuery( $query );
							$db->query() or die( $db->stderr() );
							$jsondata['msg'] = JText::_('THANK YOU FOR VOTE!');
			                                $jsondata['count'] = (int)$counts[2];
							echo json_encode($jsondata);							
						} else {
							$vote_again = $voting_period - $r_date;
							$jsondata['msg'] = JText::_('ALREADY VOTED! YOU CAN VOTE AGAIN AFTER').$vote_again.JText::_('DAYS');
							$counts = getCount($aid);
			                                $jsondata['count'] = $counts[2];
							echo json_encode($jsondata);
						}					
					
					}
            	}
            }
		}                   	            
}
function sendReport($aid) {
 $jsondata['test'] = 'test';
 echo json_encode($jsondata);
}
function getCount($aid){
	$db  = &JFactory::getDBO();
	$query = 'SELECT *' . ' FROM #__scontent_votes' . ' WHERE content_id = ' . $aid;
    $db->setQuery($query);
    $results = $db->loadObjectList();
	if (!$db->query()) {
          $counts = 0;						
	} else {
	 $up_count = 0;
	 $down_count = 0;
	 $total_count = 0;
	 foreach($results as $result){
	    	$up_count = $up_count + $result->up_count;
	    	$down_count = $down_count + $result->down_count;
	 }
	 $total_count = $up_count - $down_count;
	 $counts = array($up_count,$down_count,$total_count);
	}
    return $counts;
}

function GetUserIp()
{
 if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
 {
   $ip=$_SERVER['HTTP_CLIENT_IP'];
 }
 elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
 {
  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
 }
 else
 {
   $ip=$_SERVER['REMOTE_ADDR'];
 }
 return $ip;
}
?>