<?php

if(!defined('VERSION')) {
    define('VERSION', getenv('OC_VERSION'));
}

// Site
$_['site_url']         = getenv('HTTP_SERVER');
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
    'multisafepay'
);

// Action Events
$_['action_event'] = array(
    'controller/*/before' => array(
        'event/language/before'
    ),
    'controller/*/after' => array(
        'event/language/after'
    ),
    'view/*/before' => array(
        500  => 'event/theme/override',
        998  => 'event/language',
        1000 => 'event/theme'
    ),
    'language/*/after' => array(
        'event/translation'
    ),
    'controller/*/after'  => array(
        'event/debug/after'
    )
);

// Check if admin or catalog
if (defined('HTTP_CATALOG')) {
    $_['action_pre_action'] = array(
        'startup/startup',
        'startup/error',
        'startup/event',
        'startup/sass',
        'startup/login',
        'startup/permission'
    );
} else {
    $_['action_pre_action'] = array(
        'startup/startup',
        'startup/error',
        'startup/event',
        'startup/maintenance',
        'startup/seo_url'
    );
}

// Test Settings
$_['session_engine'] = 'test';
$_['session_autostart'] = false;
$_['template_engine']    = 'twig';
$_['template_directory'] = '';
$_['template_cache']     = false;