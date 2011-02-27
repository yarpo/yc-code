<?php
/* 
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2009 Add This, LLC                                         |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 3 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
 * +--------------------------------------------------------------------------+
 */
  
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
  
class plgContentAddThis extends JPlugin{
  
   /**
    * plgContentAddThisPlugin
    *
    * AddThis Plugin constructor
    * 
    */
    function plgContentAddThis(&$subject)
    {
        parent::__construct($subject);
  
        // loading plugin parameters
        $this->_plugin = JPluginHelper::getPlugin('content', 'addthis');
        $this->_params = new JParameter($this->_plugin->params);
        
        $this->_pub_id                      = $this->_params->get('pub_id');
        $this->_button_style                = $this->_params->get('button_style');
        $this->_custom_url                  = $this->_params->get('custom_url');
        $this->_addthis_button_language     = $this->_params->get('addthis_button_language');
        $this->_addthis_brand               = $this->_params->get('addthis_brand');
        $this->_addthis_header_color        = $this->_params->get('addthis_header_color');
        $this->_addthis_header_background   = $this->_params->get('addthis_header_background');
        $this->_addthis_options             = $this->_params->get('addthis_options');
        $this->_addthis_offset_top          = $this->_params->get('addthis_offset_top');
        $this->_addthis_offset_left         = $this->_params->get('addthis_offset_left');
        $this->_addthis_hover_delay         = $this->_params->get('addthis_hover_delay');
        $this->_addthis_hide_embed          = $this->_params->get('addthis_hide_embed');
        $this->_addthis_language            = $this->_params->get('addthis_language');
        $this->_alignment                   = $this->_params->get('alignment');

    }
      
    /**
     * onPrepareContent
     *
     * Create the addthis button code while content is being prepared
     * 
     */
    function onPrepareContent(&$article, &$params, $limitstart)
    {

        $outputValue = "<div style='float:" . $this->_alignment . "'>\r\n";
        
        $outputValue .="<!-- AddThis Button BEGIN -->\r\n";
        
        $outputValue .= "<script type='text/javascript'>\r\n";
        
        if (trim($this->_pub_id) !== "Your Publisher ID" && trim($this->_pub_id) !== "")
        {
            $outputValue .= "var addthis_pub = '" .trim($this->_pub_id). "';\r\n";
        }
        if (trim($this->_addthis_brand) != "")
        {
            $outputValue .= "var addthis_brand = '".trim($this->_addthis_brand)."';\r\n";
        }
        if (trim($this->_addthis_header_color) != "")
        {
            $outputValue .= "var addthis_header_color = '".trim($this->_addthis_header_color)."';\r\n";
        }
        if (trim($this->_addthis_header_background) != "")
        {
            $outputValue .= "var addthis_header_background = '".trim($this->_addthis_header_background)."';\r\n";
        }
        if (trim($this->_addthis_options) != "")
        {
            $outputValue .= "var addthis_options = '".trim($this->_addthis_options)."';\r\n";
        }
        if (intval(trim($this->_addthis_offset_top)) != 0)
        {
            $outputValue .= "var addthis_offset_top = ".$this->_addthis_offset_top.";\r\n";
        }
        if (intval(trim($this->_addthis_offset_left)) != 0)
        {
            $outputValue .= "var addthis_offset_left = ".$this->_addthis_offset_left.";\r\n";
        }
        if (intval(trim($this->_addthis_hover_delay)) > 0)
        {
            $outputValue .= "var addthis_hover_delay = ".$this->_addthis_hover_delay.";\r\n";
        }
        if (trim($this->_addthis_language) != "" )
        {
            $outputValue .= "var addthis_language = '".$this->_addthis_language."';\r\n"; 
        }
        if (trim($this->_addthis_hide_embed) == '0')
        {
            $outputValue .= "var addthis_hide_embed = false;\r\n";
        }
        
        $outputValue .= "</script>\r\n";
        
        $outputValue .= "<a  href='http://www.addthis.com/bookmark.php?v=20' onMouseOver=\"return addthis_open(this, '', '" . urldecode($this->getArticleUrl($article)) . "', '" . $article->title . "'); \"   onMouseOut='addthis_close();' onClick='return addthis_sendto();'>";
        
        $outputValue .= "<img src='";
        
        if (trim($this->_button_style === "custom"))
        {
            if (trim($this->_custom_url) == '')
            {
                $outputValue .= "http://s7.addthis.com/static/btn/" .  $this->getButtonImage('lg-share',$this->_addthis_button_language);
            }
            else $outputValue .= $this->_custom_url;
        }
        else
        {
            $outputValue .= "http://s7.addthis.com/static/btn/" .  $this->getButtonImage($this->_button_style,$this->_addthis_button_language);
        }
        $outputValue .= "' border='0' alt='AddThis Social Bookmark Button' />";
        $outputValue .= "</a>\r\n";
        
        $outputValue .= "<script type='text/javascript' src='http://s7.addthis.com/js/200/addthis_widget.js'></script>\r\n";
        
        $outputValue .= "<!-- AddThis Button END -->";
        
        $outputValue .= "</div>\r\n";
        
        //Regular expression for finding the custom tag which disables the addthisbutton in the article.
        $switchregex = "#{addthis (on|off)}#s";
        
        //Ensure the custom tag is not present in the article text.
        if (strpos($article->text, '{addthis off}') === false )
        {
            $article->text = $outputValue . $article->text;
        }
        else
        {
            //Removing the custom tag from the final output.
            $article->text = preg_replace($switchregex, '', $article->text);
        }
        
    }

    /**
    * getArticleUrl
    *
    * Get the static url for the article
    * 
    * @param object $article - Joomla article object
    **/
    function getArticleUrl(&$article)
    {
        if (!is_null($article)) 
        {
            require_once( JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
            
            $uri = &JURI::getInstance();
            $base = $uri->toString(array('scheme', 'host', 'port'));
            $url = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));
            return JRoute::_($base . $url, true, 0);
        }
    }

    /**
     * getButtonImage
     *
     * This is used for preparing the image button name.
     * 
     * @param string $name - Button style of addthis button selected.
     * @param string $language - The language selected for addthis button.
     */
    function getButtonImage($name, $language)
    {
        if ($name == "sm-plus")
        {
            $buttonImage = $name . '.gif';
        }
        elseif ($language != 'en')
        {
            if ($name == 'lg-share' || $name == 'lg-bookmark' || $name == 'lg-addthis')
            {
                $buttonImage = 'lg-share-' . $language . '.gif';
            }
            elseif($name == 'sm-share' || $name == 'sm-bookmark')
            {
                $buttonImage = 'sm-share-' . $language . '.gif';
            }
        }
        else
        {
            $buttonImage = $name . '-' . $language . '.gif';
        }
 
        return $buttonImage;
    }

  }
  ?>