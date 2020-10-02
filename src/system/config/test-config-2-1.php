<?php

if(!defined('VERSION')) {
    define('VERSION', getenv('OC_VERSION'));
}

// Site
$_['site_base']        = substr(HTTP_SERVER, 7);
$_['site_ssl']         = false;

// Database
$_['db_autostart']     = true;
$_['db_type']          = DB_DRIVER;
$_['db_hostname']      = DB_HOSTNAME;
$_['db_username']      = DB_USERNAME;
$_['db_password']      = DB_PASSWORD;
$_['db_database']      = DB_DATABASE;
$_['db_port']          = DB_PORT;

// Autoload Libraries
$_['library_autoload'] = array(
    'openbay',
    'multisafepay'
);

// Action Events
$_['action_event'] = array(
    'view/*/before' => 'event/theme',
);

// Check if admin or catalog
if (defined('HTTP_CATALOG')) {
    $_['action_default'] = 'common/dashboard';
    $_['action_pre_action'] = array(
//        'common/login/check',
        'startup/test_startup'
    );
    $_['session_autostart'] = true;
} else {
    $_['theme_default_status'] = 1;
    $_['action_pre_action'] = array(
        'startup/test_startup'
    );
    $_['session_autostart'] = false;
}

