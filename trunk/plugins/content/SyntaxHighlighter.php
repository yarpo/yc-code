<?php
defined( '_JEXEC' ) or  die('Restricted access');
jimport( 'joomla.event.plugin' );


class plgContentSyntaxHighlighter extends JPlugin
{

	function plgContentSyntaxHighlighter( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params )
	{
		if(is_null($article->id)){
			return true;
		}
		if($this->ignore($article->id,$article->catid)){
			return true;
		}

		$isExecuted=JRequest :: getVar('plgContentSyntaxHighlighter');
		if(	$isExecuted){
			return true;
		}
			
		$scripts='<script type="text/javascript">';
		$ClipBoard=$this->param('ClipBoard');
		if($ClipBoard){
			$scripts.='SyntaxHighlighter.config.clipboardSwf ="'.JURI::root(true) . '/plugins/content/syntaxhighlighter/scripts/clipboard.swf";';
		}
		$BloggerMode=$this->param('BloggerMode');
		if($BloggerMode){
			$scripts.='SyntaxHighlighter.config.bloggerMode =true;';
		}
		$scripts.='SyntaxHighlighter.config.stripBrs=true;
		SyntaxHighlighter.all();
	  </script>';

		$document =& JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true) . '/plugins/content/syntaxhighlighter/styles/shCore.css');
		$document->addStyleSheet( JURI::root(true) . '/plugins/content/syntaxhighlighter/styles/shThemeDefault.css');
		$document->addScript( JURI::root(true).'/plugins/content/syntaxhighlighter/scripts/shCore.js' );

		$languages=array('AS3','Bash','Cpp','CSharp','Css','Delphi','Diff','Groovy','Java','JavaFX','JScript','Perl','Php','Plain','PowerShell','Python','Ruby','Scala','Sql','Vb','Xml');
		foreach($languages as $language){
			if($this->param($language)){
				$document->addScript( JURI::root(true).'/plugins/content/syntaxhighlighter/scripts/shBrush'.$language.'.js' );
			}
		}
		$article->text.=$scripts;
		JRequest::setVar('plgContentSyntaxHighlighter','1');
		return true;
	}


	function param($name){
		static $plugin,$pluginParams;
		if (!isset( $plugin )){
			$plugin =& JPluginHelper::getPlugin('content', 'SyntaxHighlighter');
			$pluginParams = new JParameter( $plugin->params );
		}
		return $pluginParams->get($name);
	}



	function exclude($paramName,$value){
		$excludeArticlesIds=$this->param($paramName);
		$excludeArticlesIdsArray=explode(',',$excludeArticlesIds);
		if(empty($excludeArticlesIdsArray)){
			return 0;
		}
		if(!$value){
			return 0;
		}
		if(in_array($value,$excludeArticlesIdsArray,false)){
			return 1;
		}
		return 0;
	}

	function ignore($id,$catId){
		$ignore =$this->exclude('Exclude_Article_Ids',$id);
		if($ignore){ return $ignore; }
		$ignore=$this->exclude('Exclude_Category_Ids',$catId);
		return  $ignore;
	}


}
?>
