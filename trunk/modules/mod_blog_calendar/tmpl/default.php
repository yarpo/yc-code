<?php 
/**
* @version		1.05
* @package		Blog Calendar Reload
* @author		Juan Padial
* @authorweb	http://www.shikle.com
* @license		GNU/GPL
*
* modified from the default.php file of the Blog Calendar 1.2.2.1 module by Justo Gonzales de Rivera
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
	$displaySublevels= $params->get('show_list_sublevels');
	$showYears= $params->get('show_list_years');
	$showMonths= $params->get('show_list_months');
	$showArticles= $params->get('show_list_articles');
	
define('SEO_URL', '/blog/kalendarz.html');
?>

<link rel="stylesheet" href="/modules/mod_blog_calendar/tmpl/style.css" type="text/css">
<?php if(isset($calendar)) { ?>
<div id="calendar-<?php echo $module->id ?>">
<!--calendar-<?php echo $module->id?> start-->
<?php echo $calendar ?>
<!--calendar-<?php echo $module->id?> end-->
</div>
<?php } ?>
<div id="ArticleList">
<!--dropdown start-->
<?php if(isset($dropdown)) { ?>
		<?php foreach($articleCounter as $listYear => $yearCount) { ?>	
	<?php if($showYears) { ?>
	<li class="dropList">
		<?php if($showMonths || $showArticles) { ?>
		<a id="toggle-<?php echo $listYear ?>-<?php echo $module->id?>" onclick="toggle_visibility('<?php echo $listYear .'-'. $module->id?>')" href="javascript:void(0)">
					<?php if($yearCount['now'] && $displaySublevels){ echo "&#9660;"; }
							else{ echo "&#9658;"; } ?>
		</a>
		<?php } ?>
		<a href="<?php echo SEO_URL; ?>?year=<?php echo $listYear ?>&amp;modid=<?php echo $module->id ?>">
		<?php echo $listYear ?>
		</a>
		(<?php echo $yearCount['total'] ?>)
			<?php } ?>
			
		<?php unset($yearCount['total']); //unset it so that it doesnt appears in the foreach bucle?>
		
		<?php if($yearCount['now']){$yearNow=$listYear; unset($yearCount['now']); } ?>
		
		<?php if($showMonths && $showYears) { ?>
		<ul style="display: <?php if($yearNow==$listYear && $displaySublevels){
						echo "block"; 
						}
						else{
						echo "none";
						}
					?>;" class="dropList" id="<?php echo $listYear .'-'. $module->id ?>" >
		<?php }  else{ echo "</li>"; }?>
		
		
			<?php foreach($yearCount as $month => $monthCount) { ?>
	<?php if($showMonths) { ?>	
	<li>
			<?php if ($showArticles) { ?>
	<a id="toggle-<?php echo $listYear . "-" . $month . "-" . $module->id ?>" onclick="toggle_visibility('<?php echo $listYear . "-" . $month . "-" . $module->id ?>')" href="javascript:void(0)"  >
					<?php if($monthCount['now'] && $yearNow==$listYear && $displaySublevels && !($showYears && $showMonths)){ 
					echo "&#9660;"; }
							else{ echo "&#9658;"; } ?>
				</a>
			<?php } ?>
			<a href="<?php echo SEO_URL; ?>?year=<?php echo $listYear ?>&amp;month=<?php echo $month?>&amp;modid=<?php echo $module->id ?>">
			<?php echo date('F',mktime(12,30,30,$month,15,2000,0)) . ($showYears? '': ' '.$listYear)  ?>
		</a>
	
			(<?php echo $monthCount['total'] ?>)
	<?php } ?>
	
	
	<?php if($showArticles){ ?>	
		<ul class="articles" style="display: <?php if($monthCount['now'] && $yearNow==$listYear && 
		!($showYears && $showMonths) && $displaySublevels ){
						echo "block"; unset($monthCount['now']); 
						}
					else {
						echo "none";
						}
					?>;" id="<?php echo $listYear . "-" . ($showMonths? ($month . "-") : '') . $module->id?>">
								
	
	<?php foreach($dropdown as $article) { ?>			
	<?php if($article->year==$listYear && $article->month==$month) { ?>
				<li class="articles">
				<a class="articles" href="<?php echo $article->link ?>">
				<?php echo $article->title ?>
				</a>
				</li>
				<?php } ?>
				<?php } ?> 			
		</ul>
		
			<?php } ?>
			
			<?php if($showMonths) echo "</li>" ?>
			
		<?php } ?>
		
		<?php if($showMonths && $showYears) echo "</ul>" ?>
		
<?php } ?>

</ul>
<!--dropdown end-->
<?php } ?>
</div>
<?php if($ajax && $ajaxmod==$module->id){ exit(); } ?>
