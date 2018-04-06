<?php
// Calls API SDK
require_once('inc/api.php');

// Requires fields which you can call from $secure array
api::required('name, surname');
//      .....       ^ Space is important

// Requires token
new Auth;

// Gets User ID1
$m = new User(1);

// Generates random string
$ra[0] = bin2hex(random_bytes(64));

// Outputs and finishes everithing in API SDK
include_once('inc/end.php');