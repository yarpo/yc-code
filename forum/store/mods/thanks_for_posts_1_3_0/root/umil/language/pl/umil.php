<?php
/**
 *
 * @author Nathan Guse (EXreaction) http://lithiumstudios.org
 * @author David Lewis (Highway of Life) highwayoflife@gmail.com
 * @package umil
 * @version $Id: umil.php 149 2009-06-16 00:58:51Z exreaction $
 * @copyright (c) 2008 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// TRANSLATION DETAILS
//
// Author: Wargo
// E-mail: wojciech.r@op.pl
//
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
//
// Some characters you may want to copy&paste:
// aˆ™ A» aˆs aˆt aˆS
//

$lang = array_merge($lang, array(
	'ACTION'						=> 'Akcja',
	'ADVANCED'						=> 'Zaawansowane',
	'AUTH_CACHE_PURGE'				=> 'Czyszczenie cache uprawnien',

	'CACHE_PURGE'					=> 'Czyszczenie cache forum',
	'CONFIGURE'						=> 'Konfiguruj',
	'CONFIG_ADD'					=> 'Dodawanie nowej zmiennej konfiguracyjnej: %s',
	'CONFIG_ALREADY_EXISTS'			=> 'BLAD: zmienna konfiguracji %s juz istnieje.',
	'CONFIG_NOT_EXIST'				=> 'BLAD: zmienna konfiguracji %s nie istnieje.',
	'CONFIG_REMOVE'					=> 'Usuwanie zmiennej konfiguracujnej: %s',
	'CONFIG_UPDATE'					=> 'Aktualizowanie zmiennej konfiguracyjnej: %s',

	'DISPLAY_RESULTS'				=> 'Wyswietl pelne wyniki',
	'DISPLAY_RESULTS_EXPLAIN'		=> 'Wybierz Tak, aby wyswietlac informacje na temat aktualnie wykonywanych czynnosci.',

	'ERROR_NOTICE'					=> 'Wystapilo jeden lub wiecej bledow podczas wybranej akcji. Pobierz <a href="%1$s">ten plik</a> z lista bledow oraz skontaktuj sie z autorem modyfikacji, aby uzyskac pomoc.<br /><br />Jezeli masz problem z pobraniem tego pliku, wprowadz w przegladarce ten link: %2$s',
	'ERROR_NOTICE_NO_FILE'			=> 'Wystapilo jeden lub wiecej bledow podczas wybranej akcji.  Skontaktuj sie z autorem modyfikacji aby uzyskac wiecej informacji.',

	'FAIL'							=> 'Niepowodzenie',
	'FILE_COULD_NOT_READ'			=> 'BLAD: nie mozna otworzyc pliku %s do odczytu.',
	'FOUNDERS_ONLY'					=> 'Musisz byc zalozycielem forum aby uzyskac dostep do tej strony.',

	'GROUP_NOT_EXIST'				=> 'Grupa nie istnieje',

	'IGNORE'						=> 'Ignoruj',
	'IMAGESET_CACHE_PURGE'			=> 'Odswiezanie zestawu obrazkow: %s',
	'INSTALL'						=> 'Instaluj',
	'INSTALL_MOD'					=> 'Instaluj %s',
	'INSTALL_MOD_CONFIRM'			=> 'Czy jestes gotowy do instalacji %s?',

	'MODULE_ADD'					=> 'Dodawanie %1$s modulu: %2$s',
	'MODULE_ALREADY_EXIST'			=> 'BLAD: Modul juz istnieje.',
	'MODULE_NOT_EXIST'				=> 'BLAD: Modul nie istnieje.',
	'MODULE_REMOVE'					=> 'Usuwanie %1$s modul: %2$s',

	'NONE'							=> 'Brak',
	'NO_TABLE_DATA'					=> 'BLAD: Nie ustalono zawartosci tabeli',

	'PARENT_NOT_EXIST'				=> 'BLAD: Okreslona glowna kategoria tego modulu nie istnieje.',
	'PERMISSIONS_WARNING'			=> 'Nowe ustawienia uprawnien zostaly dodane.  Upewnij sie, czy sa zgodne z twoimi oczekiwaniami.',
	'PERMISSION_ADD'				=> 'Dodawanie opcji uprawnien: %s',
	'PERMISSION_ALREADY_EXISTS'		=> 'BLAD: Opcja uprawnien: %s juz istnieje.',
	'PERMISSION_NOT_EXIST'			=> 'BLAD:  Opcja uprawnien: %s nie istnieje.',
	'PERMISSION_REMOVE'				=> 'Usuwanie opcji uprawnien: %s',
	'PERMISSION_SET_GROUP'			=> 'Ustawianie uprawnien dla grupy %s.',
	'PERMISSION_SET_ROLE'			=> 'Ustawianie uprawnien dla zestawu uprawnien %s.',
	'PERMISSION_UNSET_GROUP'		=> 'Usuwanie uprawnien dla grupy %s.',
	'PERMISSION_UNSET_ROLE'			=> 'Usuwanie uprawnien dla zestawu uprawnien %s.',

	'ROLE_NOT_EXIST'				=> 'Zestaw uprawnien nie istnieje',

	'SUCCESS'						=> 'Sukces',

	'TABLE_ADD'						=> 'Dodawanie nowej tabeli w bazie danych: %s',
	'TABLE_ALREADY_EXISTS'			=> 'BLAD: Tabela %s istnieje juz w bazie danych.',
	'TABLE_COLUMN_ADD'				=> 'Dodawanie kolumny %2$s do tabeli %1$s',
	'TABLE_COLUMN_ALREADY_EXISTS'	=> 'BLAD: Kolumna %2$s juz istnieje w tabeli %1$s.',
	'TABLE_COLUMN_NOT_EXIST'		=> 'BLAD: Kolumna %2$s nie istnieje w tabeli %1$s.',
	'TABLE_COLUMN_REMOVE'			=> 'Usuwanie kolumny %2$s z tabeli %1$s',
	'TABLE_COLUMN_UPDATE'			=> 'Aktualizacja kolumny %2$s w tabeli %1$s',
	'TABLE_KEY_ADD'					=> 'Dodawanie klucza %2$s do tabeli %1$s',
	'TABLE_KEY_ALREADY_EXIST'		=> 'BLAD: Indeks %2$s juz istnieje w tabeli %1$s.',
	'TABLE_KEY_NOT_EXIST'			=> 'BLAD: Indeks %2$s nie istnieje w tabeli %1$s.',
	'TABLE_KEY_REMOVE'				=> 'Usuwanie klucza %2$s z tabeli %1$s',
	'TABLE_NOT_EXIST'				=> 'BLAD: Tabela %s nie istnieje w bazie danych.',
	'TABLE_REMOVE'					=> 'Usuwanie tabeli: %s z bazy danych',
	'TABLE_ROW_INSERT_DATA'			=> 'Wypelnianie tabeli %s.',
	'TABLE_ROW_REMOVE_DATA'			=> 'Usuwanie rekordu z tabeli %s',
	'TABLE_ROW_UPDATE_DATA'			=> 'Zmiana rekordu w tabeli %s.',
	'TEMPLATE_CACHE_PURGE'			=> 'Odswiezanie cache stylu %s',
	'THEME_CACHE_PURGE'				=> 'Odswiezanie cache motywu %s',

	'UNINSTALL'						=> 'Deinstaluj',
	'UNINSTALL_MOD'					=> 'Deinstaluj %s',
	'UNINSTALL_MOD_CONFIRM'			=> 'Czy jestes pewien, ze chcesz odinstalowac %s?  Wszystkie ustawienia i dane, zapisane przez ta modyfikacje, zostana usuniete!',
	'UNKNOWN'						=> 'Nieznany',
	'UPDATE_MOD'					=> 'Aktualizacja %s',
	'UPDATE_MOD_CONFIRM'			=> 'Czy na pewno zaktualizowac %s?',
	'UPDATE_UMIL'					=> 'Ta wersja UMIL jest niekatualna.<br /><br />Prosze pobrac najnowsza wersje UMIL (Unified MOD Install Library) z: <a href="%1$s">%1$s</a>',

	'VERSIONS'						=> 'Wersja modyfikacji: <strong>%1$s</strong><br />Aktualnie zainstalowana: <strong>%2$s</strong>',
	'VERSION_SELECT'				=> 'Wybieranie wersji',
	'VERSION_SELECT_EXPLAIN'		=> 'Nie wybieraj aˆsIgnorujaˆt jezeli nie wiesz co moze spowodowac wybranie innej wersji.',
));

?>