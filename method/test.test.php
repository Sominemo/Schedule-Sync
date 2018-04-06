<?

require_once('inc/api.php');
//api::required('token');

new Auth;

$m = new User(1);

$ra[0] = bin2hex(random_bytes(64));

include_once('inc/end.php');