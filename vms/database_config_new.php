<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| SECURE DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| Updated with secure credentials and best practices
| Generated on: 2025-07-07 23:21:24
| -------------------------------------------------------------------
*/

$active_group = 'default';
$query_builder = TRUE;

// Main database connection (eproc_perencanaan)
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => 'localhost',
    'port'      => '3307',
    'username'  => 'eproc_user',
    'password'  => 'UEVzaticN91wTVPI',
    'database'  => 'eproc_perencanaan',
    'dbdriver'  => 'mysqli',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'db_debug'  => FALSE, // Disabled for security in production
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8',
    'dbcollat'  => 'utf8_general_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE, // Enable when SSL is configured
    'compress'  => FALSE,
    'stricton'  => TRUE,  // Enable strict mode for data integrity
    'failover'  => array(),
    'save_queries' => FALSE // Disabled for performance and security
);

// Secondary database connection (eproc)
$db['eproc'] = array(
    'dsn'       => '',
    'hostname'  => 'localhost',
    'port'      => '3307',
    'username'  => 'eproc_user',
    'password'  => 'UEVzaticN91wTVPI',
    'database'  => 'eproc',
    'dbdriver'  => 'mysqli',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'db_debug'  => FALSE,
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8',
    'dbcollat'  => 'utf8_general_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => TRUE,
    'failover'  => array(),
    'save_queries' => FALSE
);

// Remove unused database configurations for security
// Only keep necessary connections
