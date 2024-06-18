<?php

$base = dirname(__FILE__) . "/..";

set_include_path(
    $base . "/config:" .
    $base . "/system/libraries/l10n:" .
    get_include_path()
);

// Load and setup class file autloader
include_once("class.autoloader.inc.php");
spl_autoload_register("Autoloader::load");


?>
