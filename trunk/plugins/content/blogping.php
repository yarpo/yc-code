<?php
/**
 * @package		UltimateSocialBookmarkingPlugin
 * @copyright	(C)2008 Stilero.com
 * * @license GNU/GPL
 * http://www.stilero.com
 */

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');

class plgContentblogping extends JPlugin
{
    function plgContentblogping( &$subject )
    {
		parent::__construct( $subject );

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'content', 'blogping' );
		$this->_params = new JParameter( $this->_plugin->params );
    }
	
	function onAfterContentSave(&$article, &$params, &$row)
    {
    	global $mainframe, $mosConfig_sitename,$mosConfig_live_site;
		
    	$mosConfig_absolute_path = JPATH_SITE;
		// Get plugin variables
		$pinglist = $this->_params->def('pinglist');
		$pingsection = $this->_params->def('section_id');
		$pingnew = $this->_params->def('pingnew', 1);
		
		
		//First check the following:
		// * is this a section to ping?
		// * is this article active?
		// * is this article public ?
		// If not, return and end this ping session.		
		if ( ($pingsection!=$article->sectionid) || ($pingsection="") || ($article->state!=1) || ($article->access!=0) ){
			return true;
		}
		
		// Next check if this article is new, and if ping should be made only on new articles
		if ( ($pingnew==1) && ($article->version!=1) ){
			return true;
		}

		
		$pingservers = explode("\n", $pinglist);
		
		
		$urlMode = 'standard';
		

		$host = $_SERVER['HTTP_HOST'];
		
		$fullurl = "http://".$host."/".$this->usbp_createUrl($article);
		$message="url: ".JText::_($article->title)."\n";

		$blogpost = array("title" => JText::_($article->title), "postlink" => $fullurl);
		

		
		foreach ( $pingservers as $server )
		{
			$response=$this->pingServer($server, $blogpost);
			$msg="PingService: ".$server." - <".$response[0]."> - ".$response[1];
			
			switch($response[0]){
				case 0: 
					$mainframe->enqueueMessage( $msg);
					break;
				case 1: 
					$mainframe->enqueueMessage( $msg, 'error');
					break;
				default:
					$mainframe->enqueueMessage( $msg);
					break;
			}


		}
		

		
		return true;
	}
	
	function usbp_createUrl($article, $articleid)
	{
			if(!class_exists('ContentHelperRoute')) require_once (JPATH_SITE . '/components/com_content/helpers/route.php'); 
		
				//$link = JRoute::_('index.php?option=com_content&view=article&id='. $article->slug .'&catid='. $article->catslug .'&Itemid='. JApplication::getItemid($article->id));
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));
				//if (substr($link, 0, 4) != 'http:') $link = $baseUrl . $link;
				$link = str_replace("&amp;id=", "&amp;id=".$article->id, $link);
			
		return $link;
	}
	
	function pingServer($URL, $blogpost)
	{

	
		$parse = parse_url($URL);
		if(!isset($parse['host'])) return false;
		$host = $parse['host'];
		$port = isset($parse['port'])?$parse['port']:80;
		$uri  = isset($parse['path'])?$parse['path']:'/';
	
		$fp=fsockopen($host,$port,$errno,$errstr);
		if(!$fp){		
			return array(-1,"Cannot open connection: $errstr ($errno)<br />\n");	
		}

		

		$data = "<?xml version=\"1.0\"?>\r\n
		<methodCall>\r\n
  			<methodName>weblogUpdates.ping</methodName>\r\n
	  		<params>\r\n
	    		<param>\r\n
	      			<value>".$blogpost['title']."</value>\r\n
	    		</param>\r\n
	    		<param>\r\n
	      			<value>".$blogpost['postlink']."</value>\r\n
	    		</param>\r\n
	  		</params>\r\n
		</methodCall>";

		$len  = strlen($data);
		$out  = "POST $uri HTTP/1.0\r\n";
		$out .= "User-Agent: BlogPing/1.0\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-Type: text/xml\r\n";
		$out .= "Content-length: $len\r\n\r\n";
		$out .= $data;

		fwrite($fp, $out);
		$response = '';
		while(!feof($fp)) $response.=fgets($fp, 128);
		fclose($fp);

		$lines=explode("\r\n",$response);
		$firstline=$lines[0];
		if(!ereg("HTTP/1.[01] 200 OK",$firstline))
		{	
			return array(-1,$firstline);	
		}

		while($lines[0]!='') array_shift($lines);
		array_shift($lines);
		$lines=strip_tags(implode(' ',$lines));

		$n=preg_match(
		'|<member>\s*<name>flerror</name>\s*<value>\s*<boolean>([^<]*)</boolean>\s*</value>\s*</member>|i',
		$response, $matches);
		if(0==$n)
		{	return array(-1,$lines);	}
		$flerror=$matches[1];

		$n=preg_match(
		'|<member>\s*<name>message</name>\s*<value>\s*<string>([^<]*)</string>\s*</value>\s*</member>|i',
		$response, $matches);
		if(0==$n)
		{	return array(-1,$lines);	}
		$message=$matches[1];

		return array($flerror,$message);
	}
	
}
