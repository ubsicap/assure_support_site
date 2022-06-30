<?php

/*
 * Security improvement implemented according to
 * Q2A's security suggestions outlined here:
 * https://docs.question2answer.org/install/security/
 *
 * All this does is import the `qa-config.php` file
 * from a location that above the web server's root.
 * Thus, the plaintext database credentials are not
 * directly accessible from the server and therefore
 * are more secure.
 */

require '../config/qa-config-secure.php';