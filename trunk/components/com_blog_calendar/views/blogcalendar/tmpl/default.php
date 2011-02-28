<?php 
/**
* @version		1.05
* @package		Blog Calendar Reload
* @author		Juan Padial
* @authorweb	http://www.shikle.com
* @license		GNU/GPL
*
* modified from the default.php file of the Blog Calendar 1.2.2.1 component by Justo Gonzalez de Rivera
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<!--component start-->
<?php
global $mainframe;
$aca = new BlogCalendarViewBlogCalendar;
if($this->params->get('copy_view')){
?>
<div class="componentheading">
<?php echo ucfirst($this->date) ?>
</div>
<?php
	foreach($this->contents as $this->article){
		$this->params->set('link_titles', 1);
		$this->params->set('show_vote', 0);
		$this->article->readmore_link = $this->article->link;
		$this->article->section = $this->article->secTitle;
		$this->article->category = $this->article->catTitle;
		$this->article->rating_count	= 0;
		$this->article->rating		= 0;
		/*
		 * Handle display events
		 */
		$dispatcher =& new JDispatcher();
		$dispatcher =& $dispatcher->getInstance();
		JPluginHelper::importPlugin('content');		
		
		$results = $dispatcher->trigger('onPrepareContent', array (&$this->article, &$this->params, $limitstart));
		
		$this->article->event = new stdClass();
		$results = $dispatcher->trigger('onAfterDisplayTitle', array ($this->article, &$this->params, $limitstart));
		$this->article->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array (& $this->article, & $this->params, $limitstart));
		$this->article->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onAfterDisplayContent', array (& $this->article, & $this->params, $limitstart));
		$this->article->event->afterDisplayContent = trim(implode("\n", $results));
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		
		$db =& JFactory::getDBO();
		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__templates_menu '
				.' WHERE client_id = ' . (int) $clientId
				.' AND menuid = 0 ';
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		
		$templatepath = JPATH_ROOT.DS.'templates'.DS.$defaultemplate.DS.'html'.DS.'com_content'.DS.'article'.DS.'default.php';
		$defaultpath  = JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl'.DS.'default.php';
		$requirepath  = is_file($templatepath)? $templatepath : $defaultpath;		
		require($requirepath);		
		
		echo '<br><br><br>';
	}
}
else{ 	?>
<table class="blog" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
	<td>
<div class="componentheading">
<?php echo ucfirst($this->date) ?>
</div>
<?php 
		$dispatcherClassName = (class_exists('JDispatcher')? 'JDispatcher' : 'JEventDispatcher');
		// Process the content preparation plugins
		JPluginHelper::importPlugin('content');
		$dispatcher =& new $dispatcherClassName();
		$dispatcher =& $dispatcher->getInstance();
		global $mainframe;
		
		// Get the page/component configuration
		$mainparams = &$mainframe->getParams();
?>

<?php foreach($this->contents as $article):?>
<?php if($article->created_new_day) : ?>
<table class="contentpaneopen">
		<tbody><tr>
				<td class="contentheading" width="100%">
				
<br><h3>
<?php echo ucfirst($article->created_new_day) ?>
	</h3>
				</td>
				</tr></tbody>
</table>
<?php endif ?>
<?php $dispatcher->trigger('onPrepareContent', array (& $article, & $mainparams, 0));	?>
	<table class="contentpaneopen">
		<tbody><tr>
				<td class="contentheading" width="100%">
	<?php if ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')) : ?>
 <?php echo JHTML::_('icon.edit', $article, $params, $this->access); ?>
<?php endif; ?>


			<a href='<?php echo $article->link ?>' class="contentpagetitle" >
			<?php echo $article->title?>
			</a>
				</td>
				
	<td class="buttonheading" align="right" width="100%">
	<?php echo JHTML::_('icon.pdf', $article, $this->params, $this->access); ?>
	</td>

	<td class="buttonheading" align="right" width="100%">
	<?php echo JHTML::_('icon.print_popup', $article, $this->params, $this->access); ?>
	</td>

	<td class="buttonheading" align="right" width="100%">
	<?php echo JHTML::_('icon.email', $article, $this->params, $this->access); ?>
	</td>
	</tr>
	</tbody></table>
	<table class="contentpaneopen">
	<tbody>
	<?php if($this->params->get('show_author')) :?>
	<tr>
	<td colspan="2" valign="top" width="70%">
	<span class="small">
        <?php echo $aca->aname($article,$this->params);?>
	</span>
		</td>
	</tr>
	<?php endif;?>
	<tr>
		<td colspan="2" class="<?php echo $this->params->get('show_create_date')? "createdate" : "" ?>" valign="top">
		<?php foreach($dispatcher->trigger('onAfterDisplayTitle', array (& $article, & $mainparams, 0)) as $plugin){
	if($plugin){echo $plugin;}} ?>
	                       <?php echo $this->params->get('show_create_date')? ($article->date) : ''?>
		
		                 <?php echo ($this->params->get('link_section')? '<br>'.'<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($article->sectionid)).'">' : '' ) ?>
				 <?php echo ($this->params->get('show_section')? $article->secTitle : '') ?> 
						</a>
						<?php echo ($this->params->get('show_section') && $this->params->get('show_category'))?  '/' : ''?>
						<?php echo ($this->params->get('link_category')? '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($article->catid, $article->sectionid)).'">': '' ) ?>
						<?php echo ($this->params->get('show_category')? $article->catTitle : ''); ?>
						</a>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			<?php foreach($dispatcher->trigger('onBeforeDisplayContent', array (& $article, & $mainparams, 0)) as $plugin){
	               if($plugin){echo $plugin;}}
	                ?>
	                <?php if($this->params->get('truncate_words')!=''):?> 
	                  <?php echo $aca->gentruncatedcontent($article,$this->params)?>
	                  <a href="<?php echo $article->link ?>" rel="nofollow" class="readon"><?php echo JText::_('Read more')?></a>
                          <?php elseif ($this->params->get('show_intro') && !$this->params->get('show_fulltext')):?>
                          <?php echo $article->introtext ?>
                          <a href="<?php echo $article->link ?>" rel="nofollow" class="readon"><?php echo JText::_('Read more')?></a>
                          <?php elseif (!$this->params->get('show_intro') && !$this->params->get('show_fulltext')):?>
                          <a href="<?php echo $article->link ?>" rel="nofollow" class="readon"><?php echo JText::_('Read more')?></a>
                          <?php elseif($this->params->get('show_fulltext') && !$this->params->get('show_intro')):?> 
                          <?php if($article->fulltext!='') echo $article->fulltext;else echo $article->text; ?>
                          <?php elseif($this->params->get('show_fulltext') && $this->params->get('show_intro')) :?> 
                          <?php echo $article->text ?>
                          <?php endif;?>
                          <?php if($this->params->get('joomlacomments')):?>
                          <a href="<?php echo $article->link?>#JOSC_TOP" rel="nofollow" class="readon"><?php echo $aca->acomm($article)?></a>
                          <?php endif;?>
                          </div>
		</td>
	</tr>
<?php if( ( $article->modified) != 0 && $this->params->get('show_modify_date')) : ?>	
<tr>
	<td colspan="2"  class="modifydate">
		<?php echo JText::_( 'Last Updated' ); ?> ( <?php echo JHTML::_('date', $article->modified, JText::_('DATE_FORMAT_LC2')); ?> )	
	</td>
</tr>
<?php endif; ?>
	<tr>
	         	<td colspan="2">
	 <?php foreach($dispatcher->trigger('onAfterDisplayContent', array (& $article, & $mainparams, 0)) as $plugin){
	if($plugin){echo $plugin;}}?>
		<br>
		</td>
	</tr>
<tr>
<?php endforeach ?>
</td>
</tr>
</tbody>
</table>
</table>
<?php } ?>
<div style="float:right;font-size:75%"><?php echo $aca->mh()?></div>
<?php if ($this->pagination->total > $this->pagination->limit) : ?>
<center>
<?php echo $this->pagination->getPagesCounter(); ?>
<br>
<?php echo $this->pagination->getPagesLinks(); ?>
</center>
<?php endif; ?>
<!--component end-->