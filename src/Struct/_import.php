<?php
foreach (glob(dirname(__FILE__) . "/*.class.php") as $file) {
	if (!str_contains($file, "._")) {
		require_once($file);
	}
}