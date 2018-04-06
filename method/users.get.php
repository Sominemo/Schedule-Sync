<?
require_once('inc/api.php');

new Auth();
api::required("query");

$ra['response'] = [];

$logins = explode(",", $secure["query"]);

foreach ($logins as $v) {
    $u = new User($v, ['IGNORE_EXCEPTIONS' => true]);
    $ra['response'][] = $u->get();
}

require_once('inc/end.php');

?>