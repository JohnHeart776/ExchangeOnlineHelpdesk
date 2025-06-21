<?php
require_once __DIR__ . '/src/bootstrap.php';

Login::logout();
session_destroy();

global $s;
$s->display("logout.tpl");
