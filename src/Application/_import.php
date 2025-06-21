<?php
foreach (glob(dirname(__FILE__)."/*.class.php") as $file)
{
    if (!strstr($file, "._"))
    {
        require_once($file);
    }
}