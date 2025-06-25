<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;
$active_record = TRUE;//ci version 2.x

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'betalite',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'autoinit' => TRUE,//ci version 2.x
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
//
//$db['default'] = array(
//    'dsn'   => '',
//    'hostname' => 'localhost',
//    'username' => 'selvakum_erp',
//    'password' => '-1^aC*V9Xv.7',
//    'database' => 'selvakum_erp',
//    'dbdriver' => 'mysqli',
//    'max_allowed_packet'=>'200M',
//    'dbprefix' => '',
//    'pconnect' => FALSE,
//    'db_debug' => (ENVIRONMENT !== 'production'),
//    'cache_on' => FALSE,
//    'cachedir' => '',
//    'char_set' => 'utf8',
//    'dbcollat' => 'utf8_general_ci',
//    'swap_pre' => '',
//    'encrypt'  => FALSE,
//    'compress' => FALSE,
//    'autoinit' => TRUE,//ci version 2.x
//    'stricton' => FALSE,
//    'failover' => array(),
//    'save_queries' => TRUE
//);
