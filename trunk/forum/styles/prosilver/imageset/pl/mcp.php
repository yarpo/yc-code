<?php
/**
*
* mcp [Polski]
*
* @package language
* @version $Id: mcp.php 8940.2 2009-03-17 Ronnie $
* @copyright (c) 2006 - 2009 phpBB3.PL Group
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

// INFORMACJA
//
// Wszystkie pliki językowe powinny używać kodowania UTF-8 i nie powinny zawierać znaku BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACTION'					=> 'Akcja',
	'ACTION_NOTE'				=> 'Akcja/Notatka',
	'ADD_FEEDBACK'				=> 'Dodaj notatkę',
	'ADD_FEEDBACK_EXPLAIN'		=> 'Jeżeli chcesz dodać notatkę o tym użytkowniku to wypełnij ten formularz. Użyj tylko zwykłego tekstu, HTML i BBCode nie są dozwolone.',
	'ADD_WARNING'				=> 'Dodaj ostrzeżenie',
	'ADD_WARNING_EXPLAIN'		=> 'Aby wystawić ostrzeżenie temu użytkownikowi, wypełnij poniższy formularz. Użyj tylko zwykłego tekstu, HTML i BBCode nie są dozwolone.',
	'ALL_ENTRIES'				=> 'Wszystkie pozycje',
	'ALL_NOTES_DELETED'			=> 'Usunięto wszystkie notatki o użytkowniku.',
	'ALL_REPORTS'				=> 'Wszystkie zgłoszenia',
	'ALREADY_REPORTED'			=> 'Ten post już został zgłoszony.',
	'ALREADY_WARNED'			=> 'Wystawiono już ostrzeżenie za ten post.',
	'APPROVE'					=> 'Zaakceptuj',
	'APPROVE_POST'				=> 'Zaakceptuj post',
	'APPROVE_POST_CONFIRM'		=> 'Czy na pewno chcesz zaakceptować ten post?',
	'APPROVE_POSTS'				=> 'Zaakceptuj posty',
	'APPROVE_POSTS_CONFIRM'		=> 'Czy na pewno chcesz zaakceptować wybrane posty?',

	'CANNOT_MOVE_SAME_FORUM'	=> 'Nie możesz przenieść tematu do działu, w którym już się znajduje.',
	'CANNOT_WARN_ANONYMOUS'		=> 'Nie możesz wystawić ostrzeżenia niezarejestrowanemu użytkownikowi.',
	'CANNOT_WARN_SELF'			=> 'Nie możesz ostrzec sam siebie.',
	'CAN_LEAVE_BLANK'			=> 'To pole może pozostać puste.',
	'CHANGE_POSTER'				=> 'Zmień autora',
	'CLOSE_REPORT'				=> 'Zamknij zgłoszenie',
	'CLOSE_REPORT_CONFIRM'		=> 'Czy na pewno chcesz zamknąć wybrane zgłoszenie?',
	'CLOSE_REPORTS'				=> 'Zamknij zgłoszenia',
	'CLOSE_REPORTS_CONFIRM'		=> 'Czy na pewno chcesz zamknąć wybrane zgłoszenia?',

	'DELETE_POSTS'				=> 'Usuń posty',
	'DELETE_POSTS_CONFIRM'		=> 'Czy na pewno chcesz usunąć te posty?',
	'DELETE_POST_CONFIRM'		=> 'Czy na pewno chcesz usunąć ten post?',
	'DELETE_REPORT'				=> 'Usuń zgłoszenie',
	'DELETE_REPORT_CONFIRM'		=> 'Czy na pewno chcesz usunąć wybrane zgłoszenie?',
	'DELETE_REPORTS'			=> 'Usuń zgłoszenia',
	'DELETE_REPORTS_CONFIRM'	=> 'Czy na pewno chcesz usunąć wybrane zgłoszenia?',
	'DELETE_SHADOW_TOPIC'		=> 'Usuń cień tematu',
	'DELETE_TOPICS'				=> 'Usuń wybrane tematy',
	'DELETE_TOPICS_CONFIRM'		=> 'Czy na peno chcesz usunąć te tematy?',
	'DELETE_TOPIC_CONFIRM'		=> 'Czy na pewno chcesz usunąć ten temat?',
	'DISAPPROVE'				=> 'Odrzuć',
	'DISAPPROVE_REASON'			=> 'Powód odrzucenia',
	'DISAPPROVE_POST'			=> 'Odrzuć post',
	'DISAPPROVE_POST_CONFIRM'	=> 'Czy na pewno chcesz odrzucić ten post?',
	'DISAPPROVE_POSTS'			=> 'Odrzuć posty',
	'DISAPPROVE_POSTS_CONFIRM'	=> 'Czy na pewno chcesz odrzucić te posty?',
	'DISPLAY_LOG'				=> 'Wyświetl wpisy z ostatnich',
	'DISPLAY_OPTIONS'			=> 'Opcje wyświetlania',

	'EMPTY_REPORT'					=> 'Musisz podać opis, jeśli wybierasz ten powód.',
	'EMPTY_TOPICS_REMOVED_WARNING'	=> 'Jeden lub więcej tematów zostało usuniętych, ponieważ były puste.',

	'FEEDBACK'					=> 'Notatki',
	'FORK'						=> 'Skopiuj',
	'FORK_TOPIC'				=> 'Skopiuj temat',
	'FORK_TOPIC_CONFIRM'		=> 'Czy na pewno chcesz skopiować ten temat?',
	'FORK_TOPICS'				=> 'Skopiuj wybrane tematy',
	'FORK_TOPICS_CONFIRM'		=> 'Czy na pewno chcesz skopiować te tematy?',
	'FORUM_DESC'				=> 'Opis',
	'FORUM_NAME'				=> 'Nazwa działu',
	'FORUM_NOT_EXIST'			=> 'Wybrany dział nie istnieje.',
	'FORUM_NOT_POSTABLE'		=> 'Nie można pisać w wybranym dziale.',
	'FORUM_STATUS'				=> 'Status działu',
	'FORUM_STYLE'				=> 'Styl działu',

	'GLOBAL_ANNOUNCEMENT'		=> 'Ogłoszenie globalne',

	'IP_INFO'					=> 'Informacje o IP',
	'IPS_POSTED_FROM'			=> 'Inne adresy IP, z których pisał ten użytkownik',

	'LATEST_LOGS'				=> '5 ostatnich akcji',
	'LATEST_REPORTED'			=> '5 ostatnich zgłoszeń',
	'LATEST_UNAPPROVED'			=> '5 najnowszych postów oczekujących na zaakceptowanie',
	'LATEST_WARNING_TIME'		=> 'Ostatnie ostrzeżenie przyznane',
	'LATEST_WARNINGS'			=> '5 najnowszych ostrzeżeń',
	'LEAVE_SHADOW'				=> 'Pozostaw cień tematu w poprzednim dziale',
	'LIST_REPORT'				=> 'Zgłoszenia: 1',
	'LIST_REPORTS'				=> 'Zgłoszenia: %d',
	'LOCK'						=> 'Zablokuj',
	'LOCK_POST_POST'			=> 'Zablokuj post',
	'LOCK_POST_POST_CONFIRM'	=> 'Czy na pewno chcesz zablokować możliwość edycji tego postu?',
	'LOCK_POST_POSTS'			=> 'Zablokuj wybrane posty',
	'LOCK_POST_POSTS_CONFIRM'	=> 'Czy na pewno chcesz zablokować możliwość edycji wybranych postów?',
	'LOCK_TOPIC_CONFIRM'		=> 'Czy na pewno chcesz zablokować ten temat?',
	'LOCK_TOPICS'				=> 'Zablokuj wybrane tematy',
	'LOCK_TOPICS_CONFIRM'		=> 'Czy na pewno chcesz zablokować wybrane tematy?',
	'LOGS_CURRENT_TOPIC'		=> 'Aktualnie pokazywane są logi tematu:',
	'LOGIN_EXPLAIN_MCP'			=> 'Żeby moderować ten dział, musisz się zalogować.',
	'LOGVIEW_VIEWTOPIC'			=> 'Zobacz temat',
	'LOGVIEW_VIEWLOGS'			=> 'Zobacz log tematu',
	'LOGVIEW_VIEWFORUM'			=> 'Zobacz dział',
	'LOOKUP_ALL'				=> 'Znajdź wszystkie adresy IP',
	'LOOKUP_IP'					=> 'Znajdź adres IP',

	'MARKED_NOTES_DELETED'		=> 'Usunięto wszystkie wybrane notatki o użytkowniku.',

	'MCP_ADD'					=> 'Dodaj ostrzeżenie',

	'MCP_BAN'					=> 'Banowanie',
	'MCP_BAN_EMAILS'			=> 'Zbanuj adresy e-mail',
	'MCP_BAN_IPS'				=> 'Zbanuj adresy IP',
	'MCP_BAN_USERNAMES'			=> 'Zbanuj nazwy użytkowników',

	'MCP_LOGS'					=> 'Log aktywności moderatorów',
	'MCP_LOGS_FRONT'			=> 'Strona główna',
	'MCP_LOGS_FORUM_VIEW'		=> 'Log działu',
	'MCP_LOGS_TOPIC_VIEW'		=> 'Log tematu',

	'MCP_MAIN'							=> 'Panel moderacji',
	'MCP_MAIN_FORUM_VIEW'				=> 'Zobacz dział',
	'MCP_MAIN_FRONT'					=> 'Strona główna',
	'MCP_MAIN_POST_DETAILS'				=> 'Szczegóły postu',
	'MCP_MAIN_TOPIC_VIEW'				=> 'Zobacz temat',
	'MCP_MAKE_ANNOUNCEMENT'				=> 'Zmień w ogłoszenie',
	'MCP_MAKE_ANNOUNCEMENT_CONFIRM'		=> 'Czy na pewno chcesz zmienić typ tego tematu na ogłoszenie?',
	'MCP_MAKE_ANNOUNCEMENTS'			=> 'Zmień w ogłoszenia',
	'MCP_MAKE_ANNOUNCEMENTS_CONFIRM'	=> 'Czy na pewno chcesz zmienić typ tych tematów na ogłoszenie?',
	'MCP_MAKE_GLOBAL'					=> 'Zmień w ogłoszenie globalne',
	'MCP_MAKE_GLOBAL_CONFIRM'			=> 'Czy na pewno chcesz zmienić typ tego tematu na ogłoszenie globalne?',
	'MCP_MAKE_GLOBALS'					=> 'Zmień w ogłoszenia globalne',
	'MCP_MAKE_GLOBALS_CONFIRM'			=> 'Czy na pewno chcesz zmienić typ tych tematów na ogłoszenie globalne?',
	'MCP_MAKE_STICKY'					=> 'Przyklej',
	'MCP_MAKE_STICKY_CONFIRM'			=> 'Czy na pewno chcesz przykleić ten temat?',
	'MCP_MAKE_STICKIES'					=> 'Przyklej',
	'MCP_MAKE_STICKIES_CONFIRM'			=> 'Czy na pewno chcesz przykleić te tematy?',
	'MCP_MAKE_NORMAL'					=> 'Zmień w zwykły temat',
	'MCP_MAKE_NORMAL_CONFIRM'			=> 'Czy na pewno chcesz zmienić typ tego tematu na zwykły temat?',
	'MCP_MAKE_NORMALS'					=> 'Zmień w zwykłe tematy',
	'MCP_MAKE_NORMALS_CONFIRM'			=> 'Czy na pewno chcesz zmienić typ tych tematów na zwykły temat?',

	'MCP_NOTES'					=> 'Notatki o użytkownikach',
	'MCP_NOTES_FRONT'			=> 'Strona główna',
	'MCP_NOTES_USER'			=> 'Szczegóły użytkownika',

	'MCP_POST_REPORTS'				=> 'Raporty związane z tym postem',

	'MCP_REPORTS'					=> 'Zgłoszone posty',
	'MCP_REPORT_DETAILS'			=> 'Szczegóły zgłoszeń',
	'MCP_REPORTS_CLOSED'			=> 'Zamknięte zgłoszenia',
	'MCP_REPORTS_CLOSED_EXPLAIN'	=> 'To jest lista wszystkich zgłoszeń o postach, które zostały już sprawdzone.',
	'MCP_REPORTS_OPEN'				=> 'Otwarte zgłoszenia',
	'MCP_REPORTS_OPEN_EXPLAIN'		=> 'To jest lista wszystkich zgłoszeń o postach, które wymagają sprawdzenia.',

	'MCP_QUEUE'								=> 'Kolejka moderacji',
	'MCP_QUEUE_APPROVE_DETAILS'				=> 'Zaakceptuj szczegóły',
	'MCP_QUEUE_UNAPPROVED_POSTS'			=> 'Posty oczekujące na akceptację',
	'MCP_QUEUE_UNAPPROVED_POSTS_EXPLAIN'	=> 'To jest lista wszystkich postów, które muszą zostać zaakceptowane zanim będą widzialne dla użytkowników',
	'MCP_QUEUE_UNAPPROVED_TOPICS'			=> 'Tematy oczekujące na akceptację',
	'MCP_QUEUE_UNAPPROVED_TOPICS_EXPLAIN'	=> 'To jest lista wszystkich tematów, które muszą zostać zaakceptowane zanim będą widzialne dla użytkowników',

	'MCP_VIEW_USER'				=> 'Pokaż ostrzeżenia użytkownika',

	'MCP_WARN'					=> 'Ostrzeżenia',
	'MCP_WARN_FRONT'			=> 'Strona główna',
	'MCP_WARN_LIST'				=> 'Lista ostrzeżeń',
	'MCP_WARN_POST'				=> 'Dodaj ostrzeżenie za określony post',
	'MCP_WARN_USER'				=> 'Dodaj ostrzeżenie',

	'MERGE_POSTS'				=> 'Przenieś posty',
	'MERGE_POSTS_CONFIRM'		=> 'Czy jesteś pewien, że chcesz przenieść wybrane posty?',
	'MERGE_TOPIC_EXPLAIN'		=> 'Używając tego formularza możesz przenieść wybrane posty do innego tematu. Nie zostanie zmieniona ich kolejność i będą wyglądały tak, jakby użytkownicy napisali je w tym docelowym temacie.<br />Wpisz numer ID docelowego tematu lub kliknij na przycisk „Wybierz temat”.',
	'MERGE_TOPIC_ID'			=> 'Numer ID docelowego tematu',
	'MERGE_TOPICS'				=> 'Połącz tematy',
	'MERGE_TOPICS_CONFIRM'		=> 'Czy jesteś pewien, że chcesz połączyć wybrane tematy?',
	'MODERATE_FORUM'			=> 'Moderuj dział',
	'MODERATE_TOPIC'			=> 'Moderuj temat',
	'MODERATE_POST'				=> 'Moderuj post',
	'MOD_OPTIONS'				=> 'Opcje moderacji',
	'MORE_INFO'					=> 'Dalsze informacje',
	'MOST_WARNINGS'				=> 'Użytkownicy z największą liczbą ostrzeżeń',
	'MOVE_TOPIC_CONFIRM'		=> 'Czy na pewno chcesz przenieść ten temat do nowego działu?',
	'MOVE_TOPICS'				=> 'Przenieś wybrane tematy',
	'MOVE_TOPICS_CONFIRM'		=> 'Czy na pewno chcesz przenieść wybrane tematy do nowego działu?',

	'NOTIFY_POSTER_APPROVAL'	=> 'Czy powiadomić użytkownika o zaakceptowaniu?',
	'NOTIFY_POSTER_DISAPPROVAL' => 'Czy powiadomić użytkownika o odrzuceniu?',
	'NOTIFY_USER_WARN'			=> 'Czy powiadomić użytkownika o ostrzeżeniu?',
	'NOT_MODERATOR'				=> 'Nie jesteś moderatorem tego działu.',
	'NO_DESTINATION_FORUM'		=> 'Nie wybrałeś docelowego działu.',
	'NO_DESTINATION_FORUM_FOUND'=> 'Nie jest dostępny żaden docelowy dział.',
	'NO_ENTRIES'				=> 'Brak wpisów w logu dla określonego okresu.',
	'NO_FEEDBACK'				=> 'Nie ma żadnych notatek o tym użytkowniku.',
	'NO_FINAL_TOPIC_SELECTED'	=> 'Żeby przenieść posty musisz wybrać docelowy temat.',
	'NO_MATCHES_FOUND'			=> 'Nic nie znaleziono.',
	'NO_POST'					=> 'Musisz wybrać post, aby dać za niego ostrzeżenie.',
	'NO_POST_REPORT'			=> 'Ten post nie został zgłoszony.',
	'NO_POST_SELECTED'			=> 'Musisz wybrać przynajmniej jeden post.',
	'NO_REASON_DISAPPROVAL'		=> 'Podaj powód odrzucenia postu.',
	'NO_REPORT'					=> 'Nie znaleziono raportu.',
	'NO_REPORTS'				=> 'Nie znaleziono raportów.',
	'NO_REPORT_SELECTED'		=> 'Musisz wybrać chociaż jeden raport, żeby wykonać tę akcję.',
	'NO_TOPIC_ICON'				=> 'Brak',
	'NO_TOPIC_SELECTED'			=> 'Musisz wybrać przynajmniej jeden temat.',
	'NO_TOPICS_QUEUE'			=> 'Żaden temat nie oczekuje na zaakceptowanie.',

	'ONLY_TOPIC'				=> 'Tylko temat „%s”',
	'OTHER_USERS'				=> 'Inni użytkownicy, którzy pisali z tego IP',

	'POSTER'					=> 'Autor',
	'POSTS_APPROVED_SUCCESS'	=> 'Wybrane posty zostały zaakceptowane.',
	'POSTS_DELETED_SUCCESS'		=> 'Wybrane posty zostały usunięte.',
	'POSTS_DISAPPROVED_SUCCESS'	=> 'Wybrane posty zostały odrzucone.',
	'POSTS_LOCKED_SUCCESS'		=> 'Wybrane posty zostały zablokowane.',
	'POSTS_MERGED_SUCCESS'		=> 'Wybrane posty zostały przeniesione.',
	'POSTS_UNLOCKED_SUCCESS'	=> 'Wybrane posty zostały odblokowane.',
	'POSTS_PER_PAGE'			=> 'Liczba postów na stronie',
	'POSTS_PER_PAGE_EXPLAIN'	=> '(Wpisz 0, żeby zobaczyć wszystkie)',
	'POST_APPROVED_SUCCESS'		=> 'Wybrany post został zaakceptowany.',
	'POST_DELETED_SUCCESS'		=> 'Wybrany post został usunięty.',
	'POST_DISAPPROVED_SUCCESS'	=> 'Wybrany post został odrzucony.',
	'POST_LOCKED_SUCCESS'		=> 'Wybrany post został zablokowany.',
	'POST_NOT_EXIST'			=> 'Wybrany post nie istnieje.',
	'POST_REPORTED_SUCCESS'		=> 'Wybrany post został zgłoszony.',
	'POST_UNLOCKED_SUCCESS'		=> 'Wybrany post został odblokowany.',

	'READ_USERNOTES'			=> 'Notatki o użytkowniku',
	'READ_WARNINGS'				=> 'Ostrzeżenia użytkownika',
	'REPORTER'					=> 'Zgłaszający',
	'REPORTED'					=> 'Zgłoszony',
	'REPORTED_BY'				=> 'Zgłoszony przez',
	'REPORTED_ON_DATE'			=> '',
	'REPORTS_CLOSED_SUCCESS'	=> 'Wybrane zgłoszenia zostały zamknięte.',
	'REPORTS_DELETED_SUCCESS'	=> 'Wybrane zgłoszenia zostały usunięte.',
	'REPORTS_TOTAL'				=> 'Nowe zgłoszenia: <strong>%d</strong>',
	'REPORTS_ZERO_TOTAL'		=> 'Nie ma żadnych nowych zgłoszeń.',
	'REPORT_CLOSED'				=> 'To zgłoszenie już zostało zamknięte.',
	'REPORT_CLOSED_SUCCESS'		=> 'Wybrane zgłoszenie zostało zamknięte.',
	'REPORT_DELETED_SUCCESS'	=> 'Wybrane zgłoszenie zostało usunięte.',
	'REPORT_DETAILS'			=> 'Szczegóły zgłoszenia',
	'REPORT_MESSAGE'			=> 'Zgłoś tę wiadomość',
	'REPORT_MESSAGE_EXPLAIN'	=> 'Użyj tego formularza, aby zgłosić wybraną wiadomość do administratora. Powinieneś to zrobić tylko wtedy, jeśli ta wiadomość łamie postanowienia regulaminu forum.',
	'REPORT_NOTIFY'				=> 'Powiadom mnie',
	'REPORT_NOTIFY_EXPLAIN'		=> 'Wysyła powiadomienie, gdy raport zostanie przyjęty.',
	'REPORT_POST_EXPLAIN'		=> 'Użyj tego formularza, aby zgłosić wybrany post do moderatorów i administratora. Powinieneś to zrobić tylko wtedy, jeśli ta wiadomość łamie postanowienia regulaminu forum.',
	'REPORT_REASON'				=> 'Powód zgłoszenia',
	'REPORT_TIME'				=> 'Data zgłoszenia',
	'REPORT_TOTAL'				=> 'Nowe zgłoszenia: <strong>1</strong>',
	'RESYNC'					=> 'Ponownie synchronizuj',
	'RETURN_MESSAGE'			=> '%sPrzejdź do wiadomości%s',
	'RETURN_NEW_FORUM'			=> '%sPrzejdź do nowego działu%s',
	'RETURN_NEW_TOPIC'			=> '%sPrzejdź do nowego tematu%s',
	'RETURN_POST'				=> '%sPrzejdź do postu%s',
	'RETURN_QUEUE'				=> '%sPrzejdź do kolejki%s',
	'RETURN_REPORTS'			=> '%sPrzejdź do zgłoszeń%s',
	'RETURN_TOPIC_SIMPLE'		=> '%sPrzejdź do tematu%s',

	'SEARCH_POSTS_BY_USER'			=> 'Znajdź posty użytkownika',
	'SELECT_ACTION'					=> 'Wybierz oczekiwaną akcję',
	'SELECT_FORUM_GLOBAL_ANNOUNCEMENT'	=> 'Wybierz dział, w którym to globalne ogłoszenie ma być wyświetlane.',
	'SELECT_FORUM_GLOBAL_ANNOUNCEMENTS'	=> 'Jeden lub więcej z wybranych tematów to globalne ogłoszenia. Wybierz dział, w którym mają być one wyświetlane.',
	'SELECT_MERGE'					=> 'Wybierz do połączenia',
	'SELECT_TOPICS_FROM'			=> 'Wybierz tematy z',
	'SELECT_TOPIC'					=> 'Wybierz temat',
	'SELECT_USER'					=> 'Wybierz użytkownika',
	'SORT_ACTION'					=> 'Log akcji',
	'SORT_DATE'						=> 'Data',
	'SORT_IP'						=> 'Adres IP',
	'SORT_WARNINGS'					=> 'Ostrzeżenia',
	'SPLIT_AFTER'					=> 'Wydziel od wybranego postu',
	'SPLIT_FORUM'					=> 'Dział dla nowego tematu',
	'SPLIT_POSTS'					=> 'Wydziel wybrane posty',
	'SPLIT_SUBJECT'					=> 'Tytuł nowego tematu',
	'SPLIT_TOPIC_ALL'				=> 'Utwórz osobny temat z wybranych postów',
	'SPLIT_TOPIC_ALL_CONFIRM'		=> 'Czy na pewno chcesz podzielić ten temat?',
	'SPLIT_TOPIC_BEYOND'			=> 'Utwórz osobny temat z wybranego postu i tych, które zostały napisane po nim',
	'SPLIT_TOPIC_BEYOND_CONFIRM'	=> 'Czy na pewno chcesz podzielić ten temat?',
	'SPLIT_TOPIC_EXPLAIN'			=> 'Używając tego formularza możesz podzielić ten temat na dwa różne. Aby to zrobić, zaznacz każdy post osobno lub wybierz jeden post, który będzie pierwszym postem nowego tematu.',

	'THIS_POST_IP'					=> 'Adres IP autora tego postu',
	'TOPICS_APPROVED_SUCCESS'		=> 'Wybrane tematy zostały zaakceptowane.',
	'TOPICS_DELETED_SUCCESS'		=> 'Wybrane tematy zostały usunięte.',
	'TOPICS_DISAPPROVED_SUCCESS'	=> 'Wybrane tematy zostały odrzucone.',
	'TOPICS_FORKED_SUCCESS'			=> 'Wybrane tematy zostały skopiowane.',
	'TOPICS_LOCKED_SUCCESS'			=> 'Wybrane tematy zostały zablokowane.',
	'TOPICS_MOVED_SUCCESS'			=> 'Wybrane tematy zostały przeniesione.',
	'TOPICS_RESYNC_SUCCESS'			=> 'Wybrane tematy zostały ponownie zsynchronizowane.',
	'TOPICS_TYPE_CHANGED'			=> 'Typy tematów zostały zmienione.',
	'TOPICS_UNLOCKED_SUCCESS'		=> 'Wybrane tematy zostały odblokowane.',
	'TOPIC_APPROVED_SUCCESS'		=> 'Wybrany temat został zaakceptowany.',
	'TOPIC_DELETED_SUCCESS'			=> 'Wybrany temat został usunięty.',
	'TOPIC_DISAPPROVED_SUCCESS'		=> 'Wybrany temat został odrzucony.',
	'TOPIC_FORKED_SUCCESS'			=> 'Wybrany temat został skopiowany.',
	'TOPIC_LOCKED_SUCCESS'			=> 'Wybrany temat został zablokowany.',
	'TOPIC_MOVED_SUCCESS'			=> 'Wybrany temat został przeniesiony.',
	'TOPIC_NOT_EXIST'				=> 'Wybrany temat nie istnieje.',
	'TOPIC_RESYNC_SUCCESS'			=> 'Wybrany temat został ponownie zsynchronizowany.',
	'TOPIC_SPLIT_SUCCESS'			=> 'Wybrany temat został podzielony.',
	'TOPIC_TIME'					=> 'Data publikacji',
	'TOPIC_TYPE_CHANGED'			=> 'Typ tematu został zmieniony.',
	'TOPIC_UNLOCKED_SUCCESS'		=> 'Wybrany temat został odblokowany.',
	'TOTAL_WARNINGS'				=> 'Liczba ostrzeżeń',

	'UNAPPROVED_POSTS_TOTAL'		=> 'Posty oczekujące na zaakceptowanie: <strong>%d</strong>',
	'UNAPPROVED_POSTS_ZERO_TOTAL'	=> 'Żaden post nie oczekuje na zaakceptowanie.',
	'UNAPPROVED_POST_TOTAL'			=> 'Posty oczekujące na zaakceptowanie: <strong>1</strong>',
	'UNLOCK'						=> 'Odblokuj',
	'UNLOCK_POST'					=> 'Odblokuj post',
	'UNLOCK_POST_EXPLAIN'			=> 'Pozwól na jego edycję',
	'UNLOCK_POST_POST'				=> 'Odblokuj post',
	'UNLOCK_POST_POST_CONFIRM'		=> 'Czy na pewno chcesz pozwolić na edycję postu?',
	'UNLOCK_POST_POSTS'				=> 'Odblokuj wybrane posty',
	'UNLOCK_POST_POSTS_CONFIRM'		=> 'Czy na pewno chcesz pozwolić na edycję wybranych postów?',
	'UNLOCK_TOPIC'					=> 'Odblokuj temat',
	'UNLOCK_TOPIC_CONFIRM'			=> 'Czy na pewno chcesz odblokować ten temat?',
	'UNLOCK_TOPICS'					=> 'Odblokuj wybrane tematy',
	'UNLOCK_TOPICS_CONFIRM'			=> 'Czy na pewno chcesz odblokować wybrane tematy?',
	'USER_CANNOT_POST'				=> 'Nie możesz pisać w tym dziale.',
	'USER_CANNOT_REPORT'			=> 'Nie możesz zgłaszać postów w tym dziale.',
	'USER_FEEDBACK_ADDED'			=> 'Notatka o użytkowniku została dodana.',
	'USER_WARNING_ADDED'			=> 'Ostrzeżenie zostało przyznane.',

	'VIEW_DETAILS'				=> 'Pokaż szczegóły',
	'VIEW_POST'					=> 'Pokaż post',

	'WARNED_USERS'				=> 'Użytkownicy z ostrzeżeniami',
	'WARNED_USERS_EXPLAIN'		=> 'To jest lista użytkowników mających na koncie niewygasłe ostrzeżenia.',
	'WARNING_PM_BODY'			=> 'Oto ostrzeżenie wystawione przez moderatora lub administratora forum: [quote]%s[/quote]',
	'WARNING_PM_SUBJECT'		=> 'Wystawione ostrzeżenia',
	'WARNING_POST_DEFAULT'		=> 'To ostrzeżenie dotyczy następującego postu: %s.',
	'WARNINGS_ZERO_TOTAL'		=> 'Brak ostrzeżeń.',

	'YOU_SELECTED_TOPIC'		=> 'Wybrałeś temat numer %d: %s.',

	'report_reasons'			=> array(
		'TITLE'				=> array(
			'WAREZ'		=> 'Warez',
			'SPAM'		=> 'Spam',
			'OFF_TOPIC'	=> 'Off-topic',
//			'TROLL'		=> 'Trolling',
			'OTHER'		=> 'Inne',
		),
		'DESCRIPTION'		=> array(
			'WAREZ'		=> 'Post zawiera odnośniki do nielegalnego oprogramowania.',
			'SPAM'		=> 'Post zawiera spam.',
			'OFF_TOPIC'	=> 'Treść postu nie dotyczy tematu dyskusji.',
//			'TROLL'		=> 'Autor postu atakuje kogoś, post składa się w większości z przekleństw lub autor postu wyraźnie wykazuje nieznajomość regulaminu.',
			'OTHER'		=> 'Powód zgłoszenia nie pasuje do powyższych kategorii, podaj powód w polu opis.',
		)
	),
));

?>