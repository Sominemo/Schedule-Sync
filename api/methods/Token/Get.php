<?php

/**
 * token.get
 *
 * Get access token - Authorize in API
 *
 * Returns access token, which uses for authentication in API. To use authentication just send the token as `token` field in each request to API where user should be authorized.
 *
 * ## Request
 * * **login**
 * User's login
 * _string, **required field**_
 * * **password**
 * User's password
 * _string, **required field**_
 *
 * ## Response
 * * **token**
 * Generated access token, that uses for auth
 * _string, **required field**_
 * * **user**
 * Current user
 * _[User class](https://sominemo.github.io/Temply-Account/#b-class.user), **required field**_
 *
 * @package Temply-Account\Methods
 * @author Sergey Dilong
 * @license GPL-2.0
 */

// Call new token function
$m = new Auth('auth');

// Output data
$ra['token'] = Auth::getTokenData()['token'];
$ra['user'] = Auth::User(true)->get();
