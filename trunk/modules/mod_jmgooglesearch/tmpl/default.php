<?php 
defined('_JEXEC') or die('Restricted access');
$value = !empty( $jmgsq ) ? $jmsq : 'wyszukaj';
?>

<script type"text/javascript">
$(document).ready(function() {
	$('#s-search').hide();
	$('#q-search').focus(function() {
		$(this).animate({width : '160px', borderColor: '#999'}).next().fadeIn();
	}).blur(function() {
		var val = $(this).next().fadeOut().end().animate({width : '140px', borderColor: '#d4d4d4'}).val();
		if (0 === val.length)
		{
			this.value = '<?php echo $value; ?>';
		}
	});
});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jmgooglesearch'); ?>" charset="utf-8" method="post">
	<input id="q-search" name="jmgsq" onfocus="this.value=''" type="text" value="<?php echo $value; ?>" />
	<input id="s-search" name="s" src="/templates/redevo_beep/images/search-ico.png" type="image" />
</form>
