<?php 
/**
* Author:	Omar Muhammad
* Email:	admin@omar84.com
* Website:	http://omar84.com
* Plugin:	Component as Content
* Version:	1.5.7
* Date:		19/11/2010
* License:	http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Copyright:Copyright (C) 2007 - 2010 Omar's Site. All rights reserved.
**/

defined('_JEXEC') or die ('Restricted access');

jimport( 'joomla.plugin.plugin');

class plgSystemComascon extends JPlugin
	{
	function plgSystemComascon(&$subject, $config)
		{
		parent::__construct($subject, $config);
		}

	function onAfterRender()
		{
		global $mainframe;
		if($mainframe->isAdmin() || strpos($_SERVER["PHP_SELF"], "index.php") === false)
			{
			return;
			}
		$enabled= $this->param('state');
		$debug	= $this->param('debug');
		$iflag	= 0;
		$stag	= "\n<!-- Component as Content 1.5.7 starts here -->\n";
		$etag	= "\n<!-- Component as Content 1.5.7 ends here -->\n";
		$app =& JFactory::getApplication();
		$maincss = JURI::base()."templates/".$app->getTemplate()."/css/template.css";
		$body	= JResponse::getBody();
		$regex	= "#{comascon}(.*?){/comascon}#s";
		preg_match_all($regex, $body, $matches);
		//print_r($matches[1]);
		$matchescount = count($matches[1]);
		for ($i=0;$i<$matchescount; $i++)
			{
			$type	= $this->param('type');
			$width	= $this->param('width');
			$height	= $this->param('height');
			$scroll	= $this->param('scroll');
			$bgcolor= "";
			$css	= "";
			$frameid	= "comascon".(int)(microtime()*100000);
			$original	= $matches[1][$i];
			if (!$enabled)
				{
				$body = str_replace("{comascon}".$original."{/comascon}", $stag."disabled".$etag, $body);
				}
			else
				{
				//echo $matches[1][$i]."<br>";
				if (stristr($original,'|'))	//support old syntax
					{
					$params = explode('|',$original);
					$target = $params[0];
					$parmsnumb = count($params);
					for ($j=1; $j<$parmsnumb; $j++)
						{
						$type = stristr($params[$j],"type=") ? str_replace('type=','',$params[$j]) : $type;
						$width = stristr($params[$j],"w=") ? str_replace('w=','',$params[$j]) : $width;
						$height = stristr($params[$j],"h=") ? str_replace('h=','',$params[$j]) : $height;
						$bgcolor = stristr($params[$j],"bg=") ? str_replace('bg=','',$params[$j]) : $bgcolor;
						$usercss = stristr($params[$j],"css=") ? str_replace('css=','',$params[$j]) : $css;
						$scroll = stristr($params[$j],"scroll=") ? str_replace('scroll=','',$params[$j]) : $scroll;
						}
					}
				else		//new syntax (no |)
					{
					$target = $original;
					}
					$target = str_replace('&amp;', '&', $target);
					$target = str_replace('&', '&amp;', $target);
				if (substr($target, 0, 7)=="option=")
					{
					$url = JURI::base()."index2.php?".$target;
					}
				else
					{
					$url = $target."?tmpl=component";
					}
				if ($type=="curl")
					{
					$options = array(
						CURLOPT_RETURNTRANSFER	=> true,		// return web page
						CURLOPT_HEADER			=> false,		// don't return headers
						//CURLOPT_FOLLOWLOCATION	=> true,		// follow redirects
						CURLOPT_ENCODING		=> "",			// handle all encodings
						CURLOPT_USERAGENT		=> "spider",	// who am i
						CURLOPT_AUTOREFERER		=> true,		// set referer on redirect
						CURLOPT_CONNECTTIMEOUT	=> 120,			// timeout on connect
						CURLOPT_TIMEOUT			=> 120,			// timeout on response
						CURLOPT_MAXREDIRS		=> 10,			// stop after 10 redirects
						);

					$ch	= curl_init($url);
					curl_setopt_array($ch, $options);
					$content	= curl_exec($ch);
					curl_close($ch);
					$buffer	= str_replace("?tmpl=component","?",$content);
					$head_regex = "#<head(.*?)>(.*?)</head>#s";
					preg_match_all($head_regex, $buffer, $heads);
					//print_r($heads);
					$head = $heads[2][0];
					$con_regex = "#<body(.*?)>(.*?)</body>#s";
					preg_match_all($con_regex, $buffer, $contents);
					//print_r($contents);
					$content = $contents[2][0];

					$meta_regex	= "#<(meta|title|base)(.*?)(/>|</title>)#s";
					preg_match_all($meta_regex, $head, $metas);
					$metacount= count($metas[0]);
					for ($m=0; $m<$metacount; $m++)
						{
						$head = str_replace($metas[0][$m], "", $head);
						}
					//echo $head;
					$styling= ($bgcolor=="") ? "" : "style='background-color:$bgcolor'";
					$final	= $stag."<div $styling>".$content."</div>".(($debug) ? "\n<!--".$url."-->" : "").$etag;
					$body	= str_replace("{comascon}".$original."{/comascon}", $final, $body);
					$body	= str_replace("</head>", $head."</head>", $body);
					$doc =& JFactory::getDocument();
					$doc->addStyleSheet($usercss);
					}
				else
					{
					$iflag=1;
					$content = "<div".(($height!="auto") ? "" : " onmouseout='applycss(\"$frameid\",\"$maincss\",\"$usercss\",\"$height\",\"$bgcolor\")'")."><iframe id='$frameid' src='$url' width='$width' ".(($height!="auto") ? "height='$height' " : "")."frameborder='0' scrolling='$scroll'></iframe></div>\n";
					$content.= "<script type='text/javascript'>
								<!--
								window.setTimeout('applycss(\"$frameid\",\"$maincss\",\"$usercss\",\"$height\",\"$bgcolor\");', 3000);
								-->
								</script>";
					$final	= $stag.$content.(($debug) ? "\n<!--".$url."-->" : "").$etag;
					$body	= str_replace("{comascon}".$original."{/comascon}", $final, $body);
					}
				}
			}
		if ($iflag)
			{
			$head = "<script type=\"text/javascript\" src=\"".JURI::base()."plugins/system/comascon.js\"></script>\n";
			$body = str_replace("</head>", $head."</head>", $body);
			}
		JResponse::setBody($body);
		}

	function param($name)
		{
		static $plugin,$pluginParams;
		if (!isset($plugin))
			{
			$plugin =& JPluginHelper::getPlugin('system', 'comascon');
			$pluginParams = new JParameter($plugin->params);
			}
		return $pluginParams->get($name);
		}
	}

?>
