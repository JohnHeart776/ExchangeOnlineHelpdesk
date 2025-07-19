<?php

// Import base controller classes first
require_once __DIR__ . '/Base/_import.php';

foreach (glob(dirname(__FILE__)."/*.class.php") as $file)
{
    if (!strstr($file, "._"))
    {
        require_once($file);
    }
}