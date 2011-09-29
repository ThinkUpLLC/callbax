<?php
/************************************************/
/***  APPLICATION CONFIG                      ***/
/************************************************/
// Public path of callbax's /webapp/ folder on your web server.
// For example, if the /webapp/ folder is located at http://yourdomain/callbax/, set to '/callbax/'.
$CALLBAX_CFG['site_root_path']            = '/';

// Full server path to /thinkup/ folder.
$CALLBAX_CFG['source_root_path']          = '/your-server-path-to/callbax/';

// Your timezone
$CALLBAX_CFG['timezone']                  = 'America/Los_Angeles';

// Toggle Smarty caching. true: Smarty caching on, false: Smarty caching off
$CALLBAX_CFG['cache_pages']               = true;

// Smarty file cache lifetime in seconds; defaults to 600 (10 minutes)caching
$CALLBAX_CFG['cache_lifetime']               = 600;

/************************************************/
/***  DATABASE CONFIG                         ***/
/************************************************/

$CALLBAX_CFG['db_host']                   = 'localhost'; //On a shared host? Try mysql.yourdomain.com, or see your web host's documentation.
$CALLBAX_CFG['db_type']                   = 'mysql';
$CALLBAX_CFG['db_user']                   = 'your_database_username';
$CALLBAX_CFG['db_password']               = 'your_database_password';
$CALLBAX_CFG['db_name']                   = 'your_callbax_database_name';
$CALLBAX_CFG['db_socket']                 = '';
$CALLBAX_CFG['db_port']                   = '';
$CALLBAX_CFG['table_prefix']              = 'cb_';

/************************************************/
/***  DEVELOPER CONFIG                        ***/
/************************************************/

$CALLBAX_CFG['debug']                     = true;

$CALLBAX_CFG['enable_profiler']           = false;

// Set this to true if you want your PDO object's database connection's charset to be explicitly set to utf8.
// If false (or unset), the database connection's charset will not be explicitly set.
$CALLBAX_CFG['set_pdo_charset']           = false;

//Test database override: Set this to run tests against the tests database
if ((isset($_SESSION["MODE"]) && $_SESSION["MODE"] == "TESTS") || getenv("MODE")=="TESTS") {
    $CALLBAX_CFG['db_user']                   = 'your_test_database_username';
    $CALLBAX_CFG['db_password']               = 'your_test_database_password';
    $CALLBAX_CFG['db_name']                   = 'your_test_database_name'; //by default, thinkup_tests
    ini_set('error_reporting', E_STRICT);
}
