<?php 

if (!file_exists('../application/config/config.php')) {
    die('Configuration file missing');
}

if (!file_exists('../vendor/autoload.php')) {
    die('Composer was not run, autoload.php missing');
}

?>
<h1>Kolibri Setup Test</h1>
<p>Everything seems to be okay, <a href="index.php">try it</a>!</p>