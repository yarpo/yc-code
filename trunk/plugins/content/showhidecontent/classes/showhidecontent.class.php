<?php
/**
 * @version 1.1.0 Beta
 * @package Joomla
 * @subpackage Show/Hide Content
 * @copyright	Copyright (C) 2010 webconstruction.ch. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 * @contact		info@webconstruction.ch
 * @website		www.webconstruction.ch
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die ('Access denied!');

$ShowHideContentPart = 0;
global $ShowHideContentPart;

class ShowHideContent{
	var $content = '';
	var $strParamsTypes = array ('objectwrapper','wrapobject','htmlobject','titlemouse','mousetitle','cssclasssuffix','csssuffix','wrapcontent','contentwrapper','titlewrapper','wraptitle','contenthtml','changetitlehtml','titlehtml','htmlchangetitle','changehtmltitle','htmltitle','openid','closeid','id','title','classsuffix','cssfile','duration','changetitle','titlechange','changeimage','imagechange','image','template');
	var $boolParamsTypes = array ('titleasspan','mousetitleistitle','closeonclick','noclose','openonce','show','showonce','hidetitle','link','open','showonload','horizontal','list','hover');	
	var $defaultStrValues = array('objectwrapper'=>'','wrapobject'=>'','htmlobject'=>'','titlemouse'=>'','mousetitle'=>'','wrapcontent'=>'','titlewrapper'=>'','wraptitle'=>'','cssclasssuffix'=>'','csssuffix'=>'','contentwrapper'=>'','contenthtml'=>'','changetitlehtml'=>'','titlehtml'=>'','imagechange'=>'','titlechange'=>'','htmlchangetitle'=>'','changehtmltitle'=>'','htmltitle'=>'','id'=>'','openid'=>'','closeid'=>'','title'=>'Click to read more...','classsuffix'=>'','cssfile'=>'','duration'=>'500','changetitle'=>'','changeimage'=>'','image'=>'','template'=>'');
	var $defaultBoolValues = array('titleasspan'=>-1,'mousetitleistitle'=>-1,'closeonclick'=>-1,'noclose'=>-1,'openonce'=>-1,'show'=>-1,'showonce'=>-1,'hidetitle'=>-1,'enabled'=>1,'link'=>1,'showonload'=>-1,'horizontal'=>-1,'list'=>-1,'hover'=>-1,'open'=>-1);
	
	var $aliasArray = array('objectwrapper'=>'htmlobject','wrapobject'=>'htmlobject','titlemouse'=>'mousetitle','noclose'=>'openonce','showonce'=>'openonce','cssclasssuffix'=>'classsuffix','csssuffix'=>'classsuffix','wrapcontent'=>'contenthtml','contentwrapper'=>'contenthtml','titlewrapper'=>'htmltitle','wraptitle'=>'htmltitle','changetitlehtml'=>'changehtmltitle','titlehtml'=>'htmltitle','imagechange'=>'changeimage','titlechange'=>'changetitle','show'=>'open','htmlchangetitle'=>'changehtmltitle');
	
	var $configParams = array();
	
	var $tagParams = array();
	
	function ShowHideContent($content){
		$mainframe =& JFactory::getApplication();
	    if ($mainframe->isAdmin()){
    		return;
    	}
    	
        if (!(strpos('@'.$content, "{showhide")>0))
        {
            return; //Return if no showhide placeholders
        }
        
        global $ShowHideContentPart;
        $ShowHideContentPart++;
        
        $content = str_replace('{showhide}','{showhide }',$content);
        
        $plugin = & JPluginHelper::getPlugin('content', 'showhidecontent');
        $params2 = new JParameter($plugin->params);
        
	    if (!isset($params2) || $params2 == null) {
			$params2 = new JParameter('');
		} 

		$multitemplates = trim($params2->get('multitemplates',''));
		//Get params/default values
		
		foreach ($this->defaultStrValues as $key => $value){
			reset($this->aliasArray);
			$check = false;
			foreach($this->aliasArray as $key2 => $value2){
				if ($key2==$key){
					$check = true;
					break;
				}
			}
			if ($check){
				$this->configParams[$key] = $params2->get($this->aliasArray[$key],$value);
			}else{
				if ($key=='template'&&$multitemplates!=''){
					$this->configParams[$key] = $multitemplates;
				}else{
					$this->configParams[$key] = $params2->get($key,$value);
				}
			}
		}

		foreach ($this->defaultBoolValues as $key => $value){
			reset($this->aliasArray);
			$check = false;
			foreach($this->aliasArray as $key2 => $value2){
				if ($key2==$key){
					$check = true;
					break;
				}
			}
			if ($check){
				$this->configParams[$key] = intval($params2->get($this->aliasArray[$key],$value));
			}else{
				$this->configParams[$key] = intval($params2->get($key,$value));
			}
		}	

		$content = $this->_prepareTags($content);
		
		$this->css = '';
		
		$this->jsGlobal = '';
		
		$this->jsOnLoad = '';
		
		$document =& JFactory::getDocument();
		
		if ($this->_maxLevel>0){
			for ($i=1;$i<=$this->_maxLevel;$i++){
				$content = $this->_render($content,$i);
			}
		}else{
			return;
		}
		
		if ($this->css!=''){
			$document->addStyleDeclaration($this->css, 'text/css');
		}
		
		if ($this->jsGlobal!=''){
			$document->addScriptDeclaration($this->jsGlobal);
		}
		
		if ($this->configParams['enabled']==1){
			jimport('joomla.html.html');
			JHTML::_('behavior.mootools');
			$js = $this->jsGlobal;
			$jsAdd = '';
			for ($i=0;$i<count($this->renderOpenIds);$i++){
				if ($this->objOpenOnLoad[$this->renderOpenIds[$i]]==true){
					$jsAdd .= $this->endJsOpenIds[$this->renderOpenIds[$i]];
				}
				/*if($this->sublevel[$this->renderOpenIds[$i]]>1){
					$jsAdd .= $this->renderJs2OpenIds[$this->renderOpenIds[$i]];
				}*/
			}
			$js .= 'window.addEvent(\'domready\', function(){'.$this->jsDomReady.$jsAdd.'});';
			if ($this->jsOnLoad!=''){//||$jsAdd != ''){
				$js .= 'window.addEvent(\'load\', function(){'.$this->jsOnLoad.'});';
			}
			for ($i=0;$i<count($this->renderOpenIds);$i++){
				$js = str_replace('{###'.$this->renderOpenIds[$i].'###}',$this->renderJsOpenIds[$this->renderOpenIds[$i]],$js);
			}
			$document->addScriptDeclaration(' /* <![CDATA[ */ '.$js.' /* ]]> */');
		}
		
		$this->content = $content;
	}
	
	var $_tagLevel = 0;
    var $_maxLevel = 0;
        
    function _prepareTags($content,$end = false){
    	if (stripos('@'.$content,'{showhide ')<stripos('@'.$content,'{/showhide}')&&stripos('@'.$content,'{showhide ')!=0){
			$pos = stripos('@'.$content,'{showhide ');
	    	if ($pos>0){
	    		$this->_tagLevel++;
	    		if ($this->_tagLevel>$this->_maxLevel){
	    			$this->_maxLevel = $this->_tagLevel;
	    		}
				$content = $this->_str_replaceFirst('{showhide ','{showhide'.$this->_tagLevel.' ',$content);
				$content = $this->_prepareTags($content);
				//$this->_tagscount++;
				return $content;
			}
    	}else{
		    $pos = stripos('@'.$content,'{/showhide}');
	    	if ($pos>0){
				$content = $this->_str_replaceFirst('{/showhide}','{/showhide'.$this->_tagLevel.'}',$content);
	    		$this->_tagLevel--;
	    		$content = $this->_prepareTags($content);
	    		return $content;
			}
    	}
		return $content;
    }
    
    var $cssFileDone = array();
    var $jsDomReady = '';
    var $css = '';
    var $jsGlobal = '';
    var $jsOnLoad = '';
    var $jsDefDone = false;
    var $renderOpenIds = array();
    var $renderJsOpenIds = array();
    var $renderJs2OpenIds = array();
    var $renderOpenIdsIndex = array();
	var $endJsOpenIds = '';
    var $objOpenOnLoad = array();
	var $sublevel = array();
    
    function _render($content,$subtag=1){
    	global $ShowHideContentPart;
    	$document =& JFactory::getDocument();
    	
    	preg_match_all("#{showhide".$subtag."[\s]*(.*?)}(.*?){/showhide".$subtag."}#s", $content, $matches);
		$html = $cont = $this->tagParams = $this->paramSet = $this->addSuffixClass = array();
		
		foreach($matches[0] as $str){
			$cont[] = $str;
		}
		foreach($matches[2] as $m){
			$html[]=$m;
		}
		
		foreach($matches[1] as $m){
			$this->templatesLoaded=array();
			$this->_getParams($m);
		}
		
		for($i = 0;$i<count($cont);$i++){
			/*if ($this->tagParams['title'][$i]=='Expandieren...'){
				reset ($this->tagParams);
				foreach ($this->tagParams as $key => $value){
					echo $this->tagParams[$key][$i]."<br>";
				}
				reset ($this->tagParams);
			}*/
			//do aliases 
			reset($this->aliasArray);
			foreach ($this->aliasArray as $key => $value){
				if ($this->paramSet[$key][$i]){
					$this->tagParams[$value][$i]=$this->tagParams[$key][$i];
				}
			}
			
			$eventType = 'jsDomReady';
			if ($this->tagParams['showonload'][$i]==1){
				$eventType = 'jsOnLoad';
			}
			$index = $i+1;
			$index = '_'.$ShowHideContentPart.'_'.$i.'_'.$subtag;
			$title = ' ';
			if ($this->tagParams['open'][$i]==1){
				if ($this->tagParams['changetitle'][$i]!=''){
					$tmp = $this->tagParams['title'][$i];
					$this->tagParams['title'][$i]=$this->tagParams['changetitle'][$i];
					$this->tagParams['changetitle'][$i]=$tmp;
				}
				if ($this->tagParams['changeimage'][$i]!=''){
					$tmp = $this->tagParams['image'][$i];
					$this->tagParams['image'][$i]=$this->tagParams['changeimage'][$i];
					$this->tagParams['changeimage'][$i]=$tmp;
				}
				if ($this->tagParams['changehtmltitle'][$i]!=''){
					$tmp = $this->tagParams['changehtmltitle'][$i];
					$this->tagParams['changehtmltitle'][$i]=$this->tagParams['htmltitle'][$i];
					$this->tagParams['htmltitle'][$i]=$tmp;
				}
			}
			if ($this->tagParams['title'][$i]!=''){
				$title = $this->tagParams['title'][$i];
			}
			$titleText = $title;
			$mousetitle = '';
			$mousetitlenolink = '';
			if ($this->tagParams['mousetitleistitle'][$i]!=-1){
				$mousetitle = 'title="'.$this->tagParams['title'][$i].'" ';
			}
			if ($this->tagParams['mousetitle'][$i]!=''){
				$mousetitle = 'title="'.$this->tagParams['mousetitle'][$i].'" ';
			}
			$titleNoLink = $title;
			if ($this->tagParams['link'][$i]==1){
				$title = '<a href="javascript:" '.$mousetitle.'id="id-showhide-title-link'.$index.'">[###title###]</a>';
			}else{
				$mousetitlenolink = $mousetitle;
			}
			$titleOrg = $title;
			$this->tagParams['changehtmltitle'][$i] = trim($this->_unescapeTags($this->_clearReturns($this->tagParams['changehtmltitle'][$i])));
			$this->tagParams['htmltitle'][$i] = trim($this->_unescapeTags($this->_clearReturns($this->tagParams['htmltitle'][$i])));
			if (trim($this->tagParams['changehtmltitle'][$i])==''&&$this->tagParams['htmltitle'][$i]!=''){
				$this->tagParams['changehtmltitle'][$i]=$this->tagParams['htmltitle'][$i];
			}
			
			if ($this->tagParams['htmltitle'][$i]!=''){
				$htmltitle = trim($this->tagParams['htmltitle'][$i]);
				$tmp1 = str_replace('[###title###]',$titleText,$title);
				$tmp1 = str_replace('[title]',$tmp1,$htmltitle);
				$title = $tmp1;
			}else{
				$title = str_replace('[###title###]',$titleText,$title);
			}
			
			//$csssuffix = '';
			//if ($this->tagParams['classsuffix'][$i]!=""){
			//	$csssuffix = $this->tagParams['classsuffix'][$i];
			//}
			$classTitles = 'showhide-title';
			$classContainers = 'showhide-container';
			if (isset($this->addSuffixClass[$i])){
				for ($xx=0;$xx<count($this->addSuffixClass[$i]);$xx++){
					$space = ' ';
					$space1 = '';
					if ($xx==0){
						$space1 = ' ';
					}
					if ($xx==count($this->addSuffixClass[$i])-1){
						$space = '';
					}
					$classTitles .= $space1.$this->addSuffixClass[$i][$xx].'showhide-title'.$space;
					$classContainers .= $space1.$this->addSuffixClass[$i][$xx].'showhide-container'.$space;
				}
			}
			$horizStr = '';
			if ($this->tagParams['horizontal'][$i]==1){
				$horizStr = "mode: 'horizontal'";
			}
			$durationStr = '';
			if ($this->tagParams['duration'][$i]!=null){
				$durationStr = 'duration:'.intval($this->tagParams['duration'][$i]);
			}
			if ($durationStr!=''&&$horizStr!=''){
				$horizStr = ', '.$horizStr;
			}
			$paramStr = ',{'.$durationStr.$horizStr.'}';
			if ($durationStr==''&&$horizStr==''){
				$paramStr = '';
			}
			
			$this->$eventType .= 'var showhideSlide'.$index.' = new Fx.Slide(\'id-showhide-container'.$index.'\''.$paramStr.');';
			$changeTitleJs = '';
			$changeImageJs = '';
			if ($this->tagParams['hidetitle'][$i]==1){
				$this->tagParams['changetitle'][$i]=' ';
			}
			$changetitle = '';
			$differentHtml = false;
			$_change = false;
			$_changehtml = false;
			$_changetitle = false;
			$_changeimage = false;
			$_changeinlink = true;
			$_link = false;
			$_htmltitle = false;
			if ($this->tagParams['link'][$i]!=-1){
				$_link = true;
			}
			if ($this->tagParams['changetitle'][$i]!=""){
				$_change = true;
				$_changetitle = true;
			}
			if ($this->tagParams['changeimage'][$i]!=''){
				$_change = true;
				$_changeimage = true;
			}
			$differentHtml = $this->tagParams['changehtmltitle'][$i]!=$this->tagParams['htmltitle'][$i]&&$this->tagParams['changehtmltitle'][$i]!='';
			if ($this->tagParams['changehtmltitle'][$i]!=''&&$differentHtml){
				$_change = true;
				$_changehtml = true;
			}
			if ($this->tagParams['htmltitle'][$i]!=''){
				$_change = true;
				$_htmltitle = true;
				$_changeinlink = false;
			}
			if ($_changehtml){
				$htmltitle = trim($this->tagParams['htmltitle'][$i]);
				$tit = $this->tagParams['title'][$i];
				$tmp1 = str_replace('[###title###]',$tit,$titleOrg);
				$tmp1 = str_replace('[title]',$tmp1,$htmltitle);
				$titleText = $tmp1;
				$htmltitle = trim($this->tagParams['changehtmltitle'][$i]);
				if ($this->tagParams['changehtmltitle'][$i]!=''&&$this->tagParams['changetitle'][$i]!=''){
					$tit = $this->tagParams['changetitle'][$i];
				}else{
					$tit = $this->tagParams['title'][$i];
				}
				$tmp1 = str_replace('[###title###]',$tit,$titleOrg);
				$tmp1 = str_replace('[title]',$tmp1,$htmltitle);
				$changetitle = $tmp1;
				$_changeinlink = false;
			}else{
				if ($_htmltitle){
					$htmltitle = trim($this->tagParams['htmltitle'][$i]);
					$tit = $this->tagParams['title'][$i];
					$tmp1 = str_replace('[###title###]',$tit,$titleOrg);
					$tmp1 = str_replace('[title]',$tmp1,$htmltitle);
					$titleText = $tmp1;
					if ($_changetitle){
						$htmltitle = trim($this->tagParams['htmltitle'][$i]);
						$tit = $this->tagParams['changetitle'][$i];
						$tmp1 = str_replace('[###title###]',$tit,$this->tagParams['changetitle'][$i]);
						if ($_link){
							$tmp1 = str_replace('[###title###]',$tit,$titleOrg);
						}
						$tmp1 = str_replace('[title]',$tmp1,$htmltitle);
						$changetitle = $tmp1;
						/*if ($_changeimage){
							echo $changetitle;
							exit;
						}*/
						if ($_link){
							$_changeinlink = false;
						}
					}
				}else{
					if ($_changetitle){
						$changetitle = str_replace('[###title###]',$this->tagParams['changetitle'][$i],$titleOrg);
						if ($_link){
							$_changeinlink = false;
						}
					}
					$titleText = str_replace('[###title###]',$this->tagParams['title'][$i],$titleOrg);
				}
			}
			if ($_change){
				if (!$this->jsDefDone){
					$this->jsGlobal .= 'var changeTitleState = new Array;';
					$this->jsDefDone = true;
				}
				if ($_link&&$_changeinlink&&!$_htmltitle){
					$changeId = 'id-showhide-title-link'.$index;
					$changeId2 = 'id-showhide-title-link'.$index;			
				}else{
					$changeId = 'id-showhide-title'.$index;
					$changeId2 = 'id-showhide-title'.$index;
				}
				/*if ($_changehtml&&$this->tagParams['link'][$i]&&!$_changeinlink){
					$changeId = 'id-showhide-title'.$index;
					$changeId2 = 'id-showhide-title'.$index;
				}*/
				$_changedone = false;
				if (($_changetitle||$_changehtml)&&!$_changeimage){
					$changeTitleJs = 'if (changeTitleState[\''.$changeId.'\']!=1){changeTitleState[\''.$changeId.'\']=1;$(\''.$changeId2.'\').setHTML("'.addslashes($changetitle).'");}else{changeTitleState[\''.$changeId.'\']=0;$(\''.$changeId.'\').setHTML("'.addslashes($titleText).'");}';
					$_changedone = true;
				}elseif (($_changetitle||$_changehtml)&&$_changeimage){
					$changeTitleJs = 'if (changeTitleState[\''.$changeId.'\']!=1){changeTitleState[\''.$changeId.'\']=1;$(\''.$changeId2.'\').setHTML("'.addslashes($changetitle).'");}else{changeTitleState[\''.$changeId.'\']=0;$(\''.$changeId.'-open\').setHTML("'.addslashes($titleText).'");}';
					$_changedone = true;
				}
				if ($_changeimage&&$_changedone){
					$changeImageJs = 'if (changeTitleState[\''.$changeId.'\']==1){$(\'id-showhide-title'.$index.'\').setAttribute("id","id-showhide-title'.$index.'-open");}else{$(\'id-showhide-title'.$index.'-open\').setAttribute("id","id-showhide-title'.$index.'");}';
				}elseif($_changeimage&&!$_changedone){
					$changeImageJs = 'if (changeTitleState[\''.$changeId.'\']!=1){changeTitleState[\''.$changeId.'\']=1;}else{changeTitleState[\''.$changeId.'\']=0;};if (changeTitleState[\''.$changeId.'\']==1){$(\'id-showhide-title'.$index.'\').setAttribute("id","id-showhide-title'.$index.'-open");}else{$(\'id-showhide-title'.$index.'-open\').setAttribute("id","id-showhide-title'.$index.'");}';
				}
			}
			if ($this->tagParams['hover'][$i]==1){
				$eventStr = 'mouseover';
			}else{
				$eventStr = 'click';
			}
			$startStr = '';
			if ($this->tagParams['open'][$i]!=1){
				$startStr = 'showhideSlide'.$index.'.hide();';
			}
			$openIdsJs = $closeIdsJs = $openOnceJs = '';
			if (trim($this->tagParams['openid'][$i])!=''){
				$ids = explode(',',$this->tagParams['openid'][$i]);
				for ($x=0;$x<count($ids);$x++){
					$openid = trim($ids[$x]);
					$openIdsJs = '{###'.$openid.'###}';
					if (!in_array($openid,$this->renderOpenIds)){
						$this->renderOpenIds[]=$openid;
					}
					$this->endJsOpenIds[$openid] = $openIdsJs;
				}
			}		
			if ($this->tagParams['openonce'][$i]==1){
				$openOnceJs = ';$(\'id-showhide-title'.$index.'\').removeEvents(\''.$eventStr.'\');';
			}
			if ($this->tagParams['showonload'][$i]==1){
				$startStr .= '$(\'id-showhide-container'.$index.'\').setStyle(\'visibility\',\'visible\');';
				$startStr .= 'showhideSlide'.$index.'.slideIn();';
				$this->jsDomReady .= '$(\'id-showhide-container'.$index.'\').setStyle(\'visibility\',\'hidden\');';
			}	
			$jsTmp = $startStr."$('id-showhide-title".$index."').addEvent('".$eventStr."', function(e){e = new Event(e);showhideSlide".$index.".toggle();".$changeTitleJs.$changeImageJs.$openIdsJs.$closeIdsJs."e.stop()".$openOnceJs.";});";
			$this->$eventType .= $jsTmp;
			if ($this->tagParams['closeonclick'][$i]!=-1){
				$this->jsDomReady .= "$('id-showhide-container".$index."').addEvent('click', function(e){e = new Event(e);showhideSlide".$index.".slideOut();".$changeTitleJs.$changeImageJs.$openIdsJs.$closeIdsJs."e.stop()".$openOnceJs.";});";
			}
			$elid = trim($this->tagParams['id'][$i]);
			if ($elid!=''){
				$this->renderJsOpenIds[$elid]=';showhideSlide'.$index.'.slideIn();';
				//$this->renderJs2OpenIds[$elid]='$(\'id-showhide-container'.$index.'\').setStyle(\'visibility\',\'visible\');';
				if ($this->tagParams['open'][$i]==1){
					$this->objOpenOnLoad[$elid]=true;
				}else{
					$this->objOpenOnLoad[$elid]=false;
				}
				$this->sublevel[$elid] = $subtag;
			}
			
			if (trim($this->tagParams['image'][$i])!=''){
				$this->tagParams['list'][$i]=1;
			}
			$htmlTmp = '[content]';
			if ($this->tagParams['contenthtml'][$i]!=''){
				$htmlTmp = trim($this->_unescapeTags($this->_clearReturns($this->tagParams['contenthtml'][$i])));
			}
			$htmlTmp = str_replace('[content]',$html[$i],$htmlTmp);
			if ($this->tagParams['list'][$i]==-1){
				if ($this->tagParams['titleasspan'][$i]!=-1){
					$divorspan = 'span';
				}else{
					$divorspan = 'div';
				}
				$new = '<'.$divorspan.' '.$mousetitlenolink.'class="'.$classTitles.'" id="id-showhide-title'.$index.'">'.$title.'</'.$divorspan.'><div class="'.$classContainers.'" id="id-showhide-container'.$index.'">'.$htmlTmp.'</div>';
			}else{
				if ($this->tagParams['image'][$i]!=''){
					$this->tagParams['image'][$i] = $this->_addUrlIfNot($this->tagParams['image'][$i]);
					$this->css .= 'ul li#id-showhide-title'.$index.' {list-style:none; list-style-image:url(\''.$this->tagParams['image'][$i].'\');}';
				}
				if ($this->tagParams['changeimage'][$i]!=''){
					$this->tagParams['changeimage'][$i] = $this->_addUrlIfNot($this->tagParams['changeimage'][$i]);
					$this->css .= 'ul li#id-showhide-title'.$index.'-open {list-style:none; list-style-image:url(\''.$this->tagParams['changeimage'][$i].'\');}';
				}
				$new = '<ul><li '.$mousetitlenolink.'class="'.$classTitles.'" id="id-showhide-title'.$index.'">'.$title.'</li></ul><div class="'.$classContainers.'" id="id-showhide-container'.$index.'">'.$htmlTmp.'</div>';
			}
			$repObject = '[object]';
			if ($this->tagParams['htmlobject'][$i]!=''){
				$repObject = trim($this->_unescapeTags($this->_clearReturns($this->tagParams['htmlobject'][$i])));
			}
			$repObject = str_replace('[object]',$new,$repObject);
			$content = $this->_str_replaceFirst($cont[$i],$repObject,$content);
		}
		return $content;
    	
    }

    function _addUrlIfNot($str){
    	if (substr($str,0,7)!='http://'){
			$str = JURI::base().$str;	
		}
		return $str;
    }
    
	function _str_replaceFirst($s, $r, $str)
    {
        $l = strlen($str);
        $a = strpos($str, $s);
        $b = $a+strlen($s);
        $temp = substr($str, 0, $a).$r.substr($str, $b, ($l-$b));
        return $temp;
    }
    
    function _clearReturns($m){
    	$new = nl2br($m);
   		$new = explode('<br />',$new);
   		$m = ' '.implode(' ',$new);
   		$m = str_replace("\n",'',$m);
   		$m = str_replace("\r",'',$m);
   		return $m;
    }
    
    function _unescapeTags($str){
    	$str = str_replace('\\***','</',$str);
    	$str = str_replace('\\**','>',$str);
    	$str = str_replace("\\*",'<',$str);
    	return $str;
    }
    
    var $addSuffixClass = array();
    var $paramSet = array();
    var $templatesLoaded = array();
    
    function _getParams($m,$doindex=-1,$multiLine=false){
    	$document =& JFactory::getDocument();
    	$m = ' '.$m;
    	if ($multiLine){
    		$m = $this->_clearReturns($m);
    	}
    	$m = str_replace('\"','(::###::)',$m);
    	$m = str_replace("\'",'(::##::)',$m);
    	
    	for ($i=0;$i<count($this->strParamsTypes);$i++){
    		$paramType = $this->strParamsTypes[$i];
			if ($doindex==-1){
				if (isset($this->tagParams[$paramType])){
					$index = count($this->tagParams[$paramType]);
				}else{
					$index = 0;
				}
			}else{
				$index = $doindex;
			}
			$match = array();
			$match2 = array();
			if (preg_match("#[\s]+".$paramType."[\s]*=[\s]*[\"|'](.*?)[\"|'][\s]*#s",$m,$match)){
				if (isset($match[1])){
					$p = $match[1];
			    	$p = str_replace('(::###::)','"',$p);
			    	$p = str_replace('(::##::)',"'",$p);
			    	if ($paramType=='cssfile'){
						$cssArray = explode(',',$p);
						for ($d=0;$d<count($cssArray);$d++){
							$cssF = trim($cssArray[$d]);
							if (!isset($this->cssFileDone[$cssF])){
								$this->cssFileDone[$cssF]=true; 
								$cssF = $this->_addUrlIfNot($cssF);
								$document->addStyleSheet($cssF);
							}
						}
			    	}
					if ($paramType=='classsuffix'){
						$pArray = explode(',',$p);
						for ($c=0;$c<count($pArray);$c++){
							$p = trim($pArray[$c]);
							$cont = true;
							if (isset($this->addSuffixClass[$index])){		
								if (!in_array($p,$this->addSuffixClass[$index])){
									$cont = true;
								}else{
									$cont = false;
								}
							}
							if ($cont){
								$this->addSuffixClass[$index][] = $p;
							}
						}
						$this->paramSet[$paramType][$index] = true;
					}elseif($paramType!='classsuffix'){
				    	$this->tagParams[$paramType][$index] = $p;
				    	$this->paramSet[$paramType][$index] = true;
					}
				}
			}else{
				if (!isset($this->tagParams[$paramType][$index])){
					$this->tagParams[$paramType][$index] = $this->configParams[$paramType];
					$this->paramSet[$paramType][$index] = false;
				}
			}
		}
		for ($i=0;$i<count($this->boolParamsTypes);$i++){
			$paramType = $this->boolParamsTypes[$i];
			if ($doindex==-1){
				if (isset($this->tagParams[$paramType])){
					$index = count($this->tagParams[$paramType]);
				}else{
					$index = 0;
				}
			}else{
				$index = $doindex;
			}
			//if (preg_match("#(.*?) ".$paramType."(?)=(?)true(.*?)#s",$m)){
			if (preg_match("#[\s]+".$paramType."[\s]*=[\s]*true[\s]*#s",$m)){
				$this->tagParams[$paramType][$index] = 1;
				$this->paramSet[$paramType][$index] = true;
			}else{
				if (!isset($this->tagParams[$paramType][$index])){
					$this->tagParams[$paramType][$index] = $this->configParams[$paramType];
					$this->paramSet[$paramType][$index] = false;
				}
			}
			if (preg_match("#[\s]+".$paramType."[\s]*=[\s]*false[\s]*#s",$m)){
				$this->tagParams[$paramType][$index] = -1;
				$this->paramSet[$paramType][$index] = true;
			}elseif(!isset($this->tagParams[$paramType][$index])){
				$this->tagParams[$paramType][$index] = $this->configParams[$paramType];
				$this->paramSet[$paramType][$index] = false;
			}
		}
		if (trim($this->tagParams['template'][$index])!=''&&$this->tagParams['template'][$index]!=-1){
			jimport('joomla.filesystem.file');
			$tmpArray = explode(',',$this->tagParams['template'][$index]);
			$getInlineParams = false;
			for ($x=0;$x<count($tmpArray);$x++){
				$template = trim($tmpArray[$x]);
				if (!in_array($template,$this->templatesLoaded)){
					$this->templatesLoaded[] = $template;
					$file = JPATH_BASE.DS.'plugins'.DS.'content'.DS.'showhidecontent'.DS.'templates'.DS.$template.'.tmpl';
					if (JFile::exists($file)){
						$templateParams = JFile::read($file);
						$this->_getParams(' '.$templateParams,$index,true);
						$getInlineParams = true;
					}else{
						//echo "Template ".$file." not found.";
						//debug template file not found
					}
				}
			}			
			if ($getInlineParams){
				$this->_getParams(' '.$m,$index);
			}
		}
    }
}