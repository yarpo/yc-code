<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
<jdoc:include type="head" /> <!--head set in the global configuration-->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/menu.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/<?php echo $this->params->get("colorVariation", "blue"); ?>.css" type="text/css" />

<?php if(!$this->countModules('user1')||!$this->countModules("user2")){ $topwidth="wide"; }?>
<?php if(!$this->countModules('user1')&&!$this->countModules("user2")){ $topwidth="wider"; }?>

<?php if(!$this->countModules("left")){ $middlewidth="wider"; }?>
<?php if(!$this->countModules("left")&&$this->countModules("right")){ $middlewidth="rightonly"; }?>
<?php if(!$this->countModules("right")){ $middlewidth="leftonly"; $leftwidth="leftonly"; }?>
<?php if(!$this->countModules("right")&&!$this->countModules("left")){ $middlewidth="widest"; }?>

<?php if(!$this->countModules("user5")+!$this->countModules("user6")+!$this->countModules("user7")==1){$bottomwidth="wide"; }?>
<?php if(!$this->countModules("user5")+!$this->countModules("user6")+!$this->countModules("user7")==2){$bottomwidth="wider";}?>
</head>

<body>
<div id="wrapper">
	<div id="topwrap">
		<div id="logo">
			<jdoc:include type="modules" name="logo" style="xhtml" />
		</div><!-- #logo -->
		<div id="topright">
			<div id="user3">
				<jdoc:include type="modules" name="user3" style="xhtml" />
			</div><!-- #user3 -->

		<?php if($this->countModules("user4")): ?>
			<div id="user4"><jdoc:include type="modules" name="user4" style="xhtml" /></div>
		<?php endif; ?>

		</div><!--topright-->
	
		<div class="clear"></div>
	</div><!--topwrap-->
	
	<?php if($this->countModules('user1')): ?>
		<div id="user1">
			<jdoc:include type="modules" name="user1" style="xhtml" />
		</div><!-- #user1 -->
	<?php endif; ?>
	

<?php if($this->countModules('menu')): ?>
	<div id="nav">
		<jdoc:include type="modules" name="menu" style="xhtml" />
	</div><!--#nav-->
<?php endif; ?>

<?php if($this->countModules("header")+$this->countModules("user2")): ?>
	<div id="headerwrap<?php echo $topwidth;?>" class="headerwrap">
		<div class="bgbottom">
			<div id="header<?php echo $topwidth;?>">
				<jdoc:include type="modules" name="header" style="xhtml" />
			</div><!-- #header<?php echo $topwidth;?> -->
		<?php if($this->countModules('user2')): ?>
			<div id="user2">
				<jdoc:include type="modules" name="user2" style="xhtml" />
			</div><!-- #user2 -->
		<?php endif; ?>
			<div class="clear"></div>
		</div><!-- .bgbottom -->
	</div><!--headerwrap-->
<?php endif; ?>

	<div id="container">
		<div id="leftwrap<?php echo $leftwidth;?>" class="leftwrap">
		<?php if($this->countModules("left")): ?>
			<div id="left">
				<div class="padding">
					<jdoc:include type="modules" name="left" style="xhtml" />
				</div><!-- .padding -->
			</div><!-- #left -->
		<?php endif; ?>
			<div id="middle<?php echo $middlewidth;?>" class="middle">
				<div class="bg">
					<div class="bg">
						<div id="pathway"><?php echo JText::_( ":" ); ?>
							<jdoc:include type="module" name="breadcrumbs" />
						</div><!-- #pathway -->
						<div id="message">
							<jdoc:include type="message" />
						</div><!-- #message -->
						<div id="mainbody">
						<?php if($this->countModules("user9")): ?>
							<div id="user9">
								<div class="padding">
									<jdoc:include type="modules" name="user9" style="xhtml" />
								</div><!-- .padding -->
							</div><!-- #user9 -->
						<?php endif; ?>

							<jdoc:include type="component" style="xhtml" />

							<div><!-- <div id="bottomwrap"> -->
							<?php if($this->countModules("user6")): ?>
								<div id="user6" class="bottom<?php echo $bottomwidth;?>">
									<div class="padding">
										<jdoc:include type="modules" name="user6" style="xhtml" />
									</div>
								</div><!-- #user6 -->
							<?php endif; ?>
							<?php if($this->countModules("user7")): ?>
								<div id="user7" class="bottom<?php echo $bottomwidth;?>">
									<div class="padding">
										<jdoc:include type="modules" name="user7" style="xhtml" />
									</div>
								</div><!-- #user7 -->
							<?php endif; ?>

							<?php if($this->countModules("user5")): ?>
								<div id="user5" class="bottom<?php echo $bottomwidth;?>">
									<div class="padding">
										<jdoc:include type="modules" name="user5" style="xhtml" />
									</div><!-- .padding -->
								</div><!-- #user5 -->
							<?php endif; ?>
								<div class="clear"></div>
							</div><!-- #bottomwrap -->
						</div><!-- #mainbody -->

					<?php if($this->countModules("user8")): ?>
						<div id="user8" >
							<div class="padding">
								<jdoc:include type="modules" name="user8" style="xhtml" />
							</div><!-- .padding -->
						</div><!-- #user8 --->
					<?php endif; ?>
					</div>
				</div>
			</div>
		 </div><!--middle... -->
	</div><!--leftwrap-->
<?php if($this->countModules("right")): ?>
	<div id="right">
		<div class="padding">
			<jdoc:include type="modules" name="right" style="xhtml" />
		</div><!-- .padding -->
	</div><!-- #right -->
<?php endif; ?>
	<div class="clear"></div>
</div><!--container-->
<div id="footerwrap">
<?php if($this->countModules("footer")): ?>
<div id="footer">
	<jdoc:include type="modules" name="footer" style="xhtml" /></div>
<?php endif; ?>
	<div class="clear"></div>
</div><!--footerwrap-->
</div><!-- #wrapper -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-13152651-1");
pageTracker._setDomainName(".youthcoders.net");
pageTracker._trackPageview();
} catch(err) {}</script></body>
</html>
