<?php


new Auth();

$ra['response'] = User::ClassesToData(Contacts::Get("me", [], ["U_GET" => true]));

