<?php
/**
* CodeCitation 1.3.1
* Joomla plugin
* allow usage of SyntaxHighlighter in WYSIWYG editor
* Usage: {codecitation class="(class params for SyntaxHighlighter)" width="(optional width param for div tag)"} CODE TO HIGHTLIGHT {/codecitation}
* Usage example: {codecitation class="brush: VisualBasic; gutter:true"} VB CODE {/codecitation}
* Copyright 2009 konstantin.nizhegorodov@gmail.com. All rights reserved.
* This is a derivative work. Portions are copyright:
*     Open Source Matters
* This work is licensed under the GNU/GPL license.
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.event.plugin' );


class plgContentCodeCitation extends JPlugin {
	
	const _tag="codecitation";
	
	private static $langAliases = array("as3" => "as3", "actionscript3" => "as3",
										"bash" => "bash", "shell" => "bash",
										"cpp" => "cpp", "c" => "cpp",
										"csharp" => "csharp", "c#" => "csharp", "c-sharp" => "csharp",
										"css" => "css",
										"pascal" => "pascal", "delphi" => "pascal",
										"diff" => "diff", "patch" => "diff",
										"groovy" => "groovy",
										"java" => "java",
										"javafx" => "javafx", "jfx" => "javafx",
										"js" => "javascript", "javascript" => "javascript", "jscript" => "javascript",
										"perl" => "perl", "pl" => "perl", "Perl" => "perl",
										"php" => "php",
										"text" => "text", "plain" => "text",
										"powershell" => "powershell", "ps" => "powershell",
										"python" => "python", "py" => "python",
										"ruby" => "ruby", "rails" => "ruby", "ror" => "ruby",
										"scala" => "scala",
										"sql" => "sql",
										"vb" => "vb", "vbnet" => "vb", "VisualBasic" => "vb",
										"xml" => "xml", "xhtml" => "xml", "xslt" => "xml", "html" => "xml"	);
	
	private static $langFiles = array(  "as3"			=> "shBrushAS3.js",
										"bash"			=> "shBrushBash.js",
										"cpp"			=> "shBrushCpp.js",
										"csharp"		=> "shBrushCSharp.js",
										"css"			=> "shBrushCss.js",
										"pascal"		=> "shBrushDelphi.js",
										"diff"			=> "shBrushDiff.js",
										"groovy"		=> "shBrushGroovy.js",
										"java"			=> "shBrushJava.js",
										"javafx"		=> "shBrushJavaFX.js",
										"javascript"	=> "shBrushJScript.js",
										"perl"			=> "shBrushPerl.js",
										"php"			=> "shBrushPhp.js",
										"text"			=> "shBrushPlain.js",
										"powershell"	=> "shBrushPowerShell.js",
										"python"		=> "shBrushPython.js",
										"ruby"			=> "shBrushRuby.js",
										"scala"			=> "shBrushScala.js",
										"sql"			=> "shBrushSql.js",
										"vb"			=> "shBrushVb.js",
										"xml"			=> "shBrushXml.js"
	);
	
	private $_includeCSSOnce = False;
	private $_includedScripts = array();
	private $_runHighlighterOnce=False;

  function plgContentCodeCitation( &$subject, $params ){
     parent::__construct( $subject, $params );
  }
     
  //onPrepareContent event handler. Replace all entries of {codecitation} tag and load necessary scripts
  function onPrepareContent( &$row, &$params, $page=0 ) {
  	$tag=self::_tag;
  	$alternativetag=$this->params->def( 'alternativetag', '');
  	if ($alternativetag!='')
  	{
  		$tag='('.$tag.'|'.$alternativetag.')'; 		
  	}
  	else
  	{
  		$tag='('.$tag.')';
  	}
    $regex = "#{".$tag."[\s|&nbsp;]*(.*?)}([\s\S]*?){/\\1}#s";
    // register the regular expresstion to invoke our replacer in case Joomla finds the matches
    $row->text = preg_replace_callback( $regex, array($this,'replacer'), $row->text );
    return true;
  }

  //Do the replacement work to replace {codecitation}{/codecitation} into <div><pre></pre></div>
  //and include scripts as necessary
  private function replacer( &$matches ) {
    jimport('domit.xml_saxy_shared');

    //adjust document header to include SyntaxHighlighter styles and core
	$this->includeCSSOnce();
    $html_entities_match = array( "'\n'", "'\<\s*span\s*[\s\S]*?/*\s*\>'i", "'\<pre\s*[\s\S]*?/*\s*\>'i","'\<\s*/\s*pre\s*\>'i", "'\<div\s*[\s\S]*?/*\s*\>'i","'\<\s*/\s*div\s*\>'i","'\<\s*/\s*span\s*\>'i", "'\<\s*/\s*p\s*\>'i", "'\<p\s*[\s\S]*?/*\s*\>'i","'\<br\s*\/*\s*\>'i", "'<'i", "'>'i", "'&#39;'", "'&quot;'i", "'&nbsp;'i" );
    $html_entities_replace = array("",      "",                              "",                           "",                     "",                           "",                     "",                       "",                  "\n",                      "\n",               '&lt;',  '&gt;', "'",        '"',        ' ' );

	$matches[2]=preg_replace($html_entities_match, $html_entities_replace, $matches[2] );
	$matches[3]=preg_replace($html_entities_match, $html_entities_replace, $matches[3] );
	$text = $matches[3];
	
    $args = SAXY_Parser_Base::parseAttributes( $matches[2] );
    
    $shclass = JArrayHelper::getValue( $args, 'class', '' );
    $width = JArrayHelper::getValue( $args, 'width', 'inherit' );
    
    //determine language to show
    $langAlias="";
    $regexLangAlias="#brush\s*:\s*(\S[^;]*[^;\s])#s";

    if (preg_match($regexLangAlias,$shclass,$langAliasMatches)>0) 
    {
    	$langAlias=$langAliasMatches[1];
    }
    else
    {
    	$langAlias=$this->params->def( 'defaultlang', 'text');
    	$shclass='brush:'.$langAlias.';'.$shclass;
    }
    //determine if we need to include xml markup parser for html scripts
    $htmlScriptMode=False;
    $regexHtmlScriptMode="#html-script\s*:\s*(\S[^;]*[^;\s])#s";
     if (preg_match($regexHtmlScriptMode,$shclass,$htmlScriptModeMatches)>0) 
    {
    	$htmlScriptMode=strtoupper($htmlScriptModeMatches[1]);
    }
    else
    {
    	$htmlScriptMode="FALSE";
    }    
    
    //include Java script that is necessary to show the code (if we have not already did it for this language)
    $codes=$this->includeScript($langAlias,$htmlScriptMode);
    
    $prolog='<div style="overflow: hidden; display: block; height: auto; width: '.$width.';"><pre class="'.$shclass.'">';
    $epilog='</pre></div>';

    $text = $codes.$prolog.$text.$epilog;

    return $text;
  }
  
  private function includeScript($langAlias, $htmlScriptMode) {
  	//get then lanuage name for the alias
  	$langName="text"; //IN CASE OF WRONG ALIAS WE WILL TREAT IT AS PLAIN TEXT

  	if (isset(self::$langAliases[$langAlias]))
  	{
  		$langName=self::$langAliases[$langAlias];
  	}
  	//get the filename for the language
  	$fileName="";
  	if (isset(self::$langFiles[$langName]))
  	{
  		$fileName=self::$langFiles[$langName];
  	}
  	else
  	{
  		$fileName=self::$langFiles["text"];
  	}
  	//check if we have already embeeded file into the page
  	if (!isset($this->_includedScripts[$fileName]))
  	{
  		$this->_includedScripts[$fileName]="1";
  		$this->updatePageIncludes($fileName);
  	}
  	if($htmlScriptMode=="TRUE")
  	{
  		//embeed xml lang file if necessary for html-script mode
  		if (!isset($this->_includedScripts[self::$langFiles[self::$langAliases["xml"]]]))
  		{
  			$this->_includedScripts[self::$langFiles[self::$langAliases["xml"]]]="1";
  			$this->updatePageIncludes(self::$langFiles[self::$langAliases["xml"]]);
  		}
  	}

  	//since we are here, we need to embeed styles and scripts into document
  	if ($this->_runHighlighterOnce) return "";
  	$this->_runHighlighterOnce=True;
  	$pluginRoot=self::getPluginRoot();
  	//TODO: Handle plugin parameters and set default SyntaxHighlighter values according to the plugin params vaules
  	$codes='<script type="text/javascript">
	SyntaxHighlighter.config.clipboardSwf = "'.$pluginRoot.'scripts/clipboard.swf";
	SyntaxHighlighter.defaults["auto-links"] = '.($this->params->def( 'auto-links', 0) ? 'true' : 'false').';
	SyntaxHighlighter.defaults["collapse"] = '.($this->params->def( 'collapse', 0) ? 'true' : 'false').';
	SyntaxHighlighter.defaults["gutter"] = '.($this->params->def( 'gutter', 0) ? 'true' : 'false').';
	SyntaxHighlighter.defaults["smart-tabs"] = '.($this->params->def( 'smart-tabs', 0) ? 'true' : 'false').';
	SyntaxHighlighter.defaults["tab-size"] = '.$this->params->def( 'tab-size', '4').';
	SyntaxHighlighter.defaults["toolbar"] = '.($this->params->def( 'toolbar', 0) ? 'true' : 'false').';
	SyntaxHighlighter.defaults["wrap-lines"] = '.($this->params->def( 'wrap-lines', 0) ? 'true' : 'false').';	
	SyntaxHighlighter.all();
</script>';
  	return $codes;
  }

  private function updatePageIncludes($fileName)
  {
  	$pluginRoot=self::getPluginRoot();
  	$tag='<script type="text/javascript" src="'.$pluginRoot.'scripts/'.$fileName.'"></script>';
 	$document= & JFactory::getDocument();
  	if ($document)
  	{
  		$document->addCustomTag($tag);
  	}
  	return;  	
  }
  
  //include necessary styles into page only once
  private function includeCSSOnce() {
  	//since we are here, we need to embeed styles and scripts into document
  	if ($this->_includeCSSOnce) return;
  	// we want only one link to the CSS
  	$this->_includeCSSOnce = True;
  	$pluginRoot=self::getPluginRoot();
  	$tag= '<link type="text/css" rel="stylesheet" href="'.$pluginRoot.'styles/shCore.css"/>
<link type="text/css" rel="stylesheet" href="'.$pluginRoot.'styles/'.$this->params->def( 'theme', 'shThemeDefault.css').'"/>
<script type="text/javascript" src="'.$pluginRoot.'scripts/shCore.js"></script>';
  	$document= & JFactory::getDocument();
  	if ($document)
  	{
  		$document->addCustomTag($tag);
  	}
  	return;
  }

  public static function getPluginRoot()
  {
  	return JURI::root().'plugins/content/codecitation/';
  }
  
}