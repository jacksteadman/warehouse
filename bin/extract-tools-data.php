#!/usr/bin/env php
<?php

chdir(dirname(__FILE__));
set_include_path('../lib' . PATH_SEPARATOR . get_include_path());

require_once 'db.php';
require_once 'utils.php';

/**
  * CONFIGURATION
  */

// set to 1 for verbose output
define('DEBUG', 0);

define('INCREMENT_SIZE', 10000);



/**
  * OPTIONS AND SETUP
  */

$opts = getopt('f:r:s');

if (empty($opts['f'])) {
    usage("No file specified.");
    exit(1);
}

$log_fp = fopen('log/import_openair.log', 'a+');
if (!$log_fp) {
    echo "Couldn't open log file.\n";
    exit(1);
}
