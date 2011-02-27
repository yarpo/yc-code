/**
* @version		$Id: fckeditor.php 1154 18-1-2008 AW
* @package		JoomlaFCK
* @copyright	Copyright (C) 2006 - 2008 WebXSolution Ltd. All rights reserved.
* @license		Creative Commons Licence
* The code for this additional work for the FCKeditor has been  been written/modified by WebxSolution Ltd.
* You may not copy or distribute the work without written consent
* from WebxSolution Ltd.
*/
var FCKStylesheetCommand = function(){FCKConfig['DefaultEditorAreaCSS'] = FCKConfig['EditorAreaCSS'];FCKConfig['DefaultStylesXmlPath'] = FCKConfig['StylesXmlPath'];};FCKStylesheetCommand.prototype ={Name : 'Stylesheet',Execute : function( stylesheetName,stylesheetComboItem ){FCKUndo.SaveUndoStep(); if (!stylesheetComboItem.Selected){FCKUndo.SaveUndoStep(); this.ReloadStyles(stylesheetName);FCK.Focus();FCK.Events.FireEvent( 'OnSelectionChange' ) ;}},GetState : function(){if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG || !FCK.EditorDocument ) return FCK_TRISTATE_DISABLED; return FCK_TRISTATE_OFF;},ReloadStyles : function(stylesheetName){if(stylesheetName == 'Default'){FCKConfig.EditorAreaCSS = FCKConfig['DefaultEditorAreaCSS'];FCKConfig.StylesXmlPath = FCKConfig['DefaultStylesXmlPath'];} else{FCKConfig.EditorAreaCSS = FCKConfig.SitePath + FCKConfig.BaseAddCSSPath + stylesheetName + '.css';FCKConfig.StylesXmlPath = FCKConfig.BasePath.replace(/fckeditor\/editor/i,'fckeditor') + stylesheetName + '.xml';};FCKToolbarItems.LoadedItems['Style']._Combo.ClearItems();				FCK.ToolbarSet.CurrentInstance.Styles._GetStyles = null; 								FCKToolbarItems.LoadedItems['Style'].CreateItems(FCKToolbarItems.LoadedItems['Style']._Combo);FCKToolbarItems.LoadedItems['Style'].RefreshState();FCK.SetData(FCK.GetData(FCKConfig.FormatSource ),true ) ;}};FCKCommands.RegisterCommand('Stylesheet',new FCKStylesheetCommand()); var FCKToolbarStylesheetsCombo = function( tooltip,style ){this.CommandName	= 'Stylesheet'; this.Label		= this.GetLabel(); this.Tooltip	= tooltip ? tooltip : this.Label; this.Style		= style ? style : FCK_TOOLBARITEM_ICONTEXT; this.PanelWidth = 150; this.FieldWidth = 150; this.PanelMaxHeight = 150;};FCKToolbarStylesheetsCombo.prototype = new FCKToolbarSpecialCombo();FCKToolbarStylesheetsCombo.prototype.GetLabel = function(){return FCKLang.Stylesheet;};FCKToolbarStylesheetsCombo.prototype.CreateItems = function( targetSpecialCombo ){var aStylesheets = FCKConfig.AddStylesheets.split(';'); this._Combo.AddItem( 'Default','<span style="font-family: Arial;font-size: 12px;"> -- Default -- </span>' ); for ( var i = 0;i < aStylesheets.length;i++ ) if (aStylesheets[i] != ""){this._Combo.AddItem( aStylesheets[i],'<span style="font-family: Arial;font-size: 12px;">' + aStylesheets[i] + '</span>' ) ;}};FCKToolbarItems.RegisterItem('Stylesheet',new FCKToolbarStylesheetsCombo());