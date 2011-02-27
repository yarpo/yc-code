<div id="login">
	<?PHP
		defined('_JEXEC') or die('Restricted access');
		
		// zmienne ktorych na pewno mozna uzyc - pochodza 
		// z bezpiecznego zrodla lub sa sprawdzone
		$clear = array();
		$clear['action_url'] = JRoute::_( 'index.php', true, $params->get('usesecure'));
		$clear['return']     = $return;
		$clear['token']      = JHTML::_( 'form.token' );
		$clear['is_logged']  = (!empty($type) and 'logout' === $type);

	if ( $clear['is_logged']) : ?>

	<script type="text/javascript">
		function logOut()
		{
			document.getElementById("logout-form").submit();
		}
	</script>

	<div id="login-form">
		<form action="<?php echo $clear['action_url']; ?>" method="post" name="login" id="logout-form">
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="Submit" value="Wyloguj" />
			<input type="hidden" name="task" value="logout" />
			<input type="hidden" name="return" value="<?php echo $clear['return']; ?>" />
			<p id="user-greeting">
			<?php if ($params->get('greeting')) : ?>
				<?php if ($params->get('name')) : {
					echo JText::sprintf( 'HINAME', $user->get('name') );
				} else : {
					echo JText::sprintf( 'HINAME', $user->get('username') );
				} endif; ?>
			<?php endif; ?>
			</p>
		</form>
		<ul id="login-options">
			<li><a class="option" href="/dodaj-artykul.html">dodaj artykuł</a></li>
			<li><a class="option" href="/ustawienia-konta.html">moje konto</a></li>
			<li><a class="option" href="javascript:logOut()">wyloguj</a></li>
		</ul>
	</div>

	<?php else : ?>

	<script type="text/javascript">
	$(document).ready(function() {
		$('#login-form').hide();
		$('#login-activator').show();
		$('#modlgn_username').blur(function() {
			if (0 == this.value.length)
			{
				this.value = 'login';
			}
		});
	});
	
	function logIn()
	{
		$('#login-activator').hide();
		$('#login-form').show();
	}
	
	function dontLogIn()
	{
		$('#login-form').hide();
		$('#login-activator').show();
	}
	</script>

	<div id="login-form">
		<form action="<?php echo $clear['action_url']; ?>" method="post" name="login">
			<input tabindex="1" id="modlgn_username" type="text" name="username" value="login" title="Podaj login" />
			<input tabindex="2" id="modlgn_passwd" type="password" name="passwd" value="hasło" title="Podaj hasło" />
			<label>
				<input tabindex="3" id="modlgn_remember" type="checkbox" name="remember" value="yes" title="Zapamiętaj mnie" />
				zapamiętaj mnie
			</label>
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?PHP echo $clear['return']; ?>" />
			<?php echo $clear['token']; ?>
			<input type="submit" value="ok" name="sumbit"/>
		</form>
		<ul id="login-options">
			<li><a class="option" href="/zmien-haslo.html">przypomnij hasło</a></li>
			<li><a class="option" href="/zapomnialem-loginu.html">przypomnij login</a></li>
			<li><a class="option" href="/rejestracja.html">załóż konto!</a></li>
			<li><a class="option" href="javascript:dontLogIn()">nie loguj</a></li>
		</ul>
	</div><!-- #login-form -->

	<div id="login-activator">
		<a class="option" href="javascript:logIn()">zaloguj</a>
	</div><!-- #login-activator -->
	
	<?php endif; ?>
</div><!-- #login -->

