<?php
/**
*
* mod_thanks [Russian]
*
* @package language
* @version $Id: info_ucp_thanks.php 125 2009-12-01 10:02:51Палыч $
* @copyright (c) 2008 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
   exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ALLOW_THANKS_PM'			=> 'Получать уведомления о благодарностях в ЛС',
	'ALLOW_THANKS_PM_EXPLAIN'	=> 'Вы будете получать ЛС при изменении списка благодарностей за ваши сообщения',
	'ALLOW_THANKS_EMAIL'		=> 'Получать уведомления о благодарностях по Email',
	'ALLOW_THANKS_EMAIL_EXPLAIN'=> 'Вы будете получать Email при изменении списка благодарностей за ваши сообщения',
));

?>
