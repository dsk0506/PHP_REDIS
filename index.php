<?php


/**
 * @date 2014-1-13 
 * @author 郑印
 * @email zhengyin.name@gmail.com
 */
session_start();
date_default_timezone_set('PRC');
define('STIME',microtime(true));
define('__APP__','http://'.$_SERVER['HTTP_HOST'].str_replace('\\','/',$_SERVER['SCRIPT_NAME']));
define('__ROOT__', dirname(__APP__));
define('__STATIC__',__ROOT__.'/Static');
set_include_path(realpath('.'));
include 'weibo.class.php';
$method = isset($_GET['method'])?strtolower($_GET['method']):'index';
$weibo = new Weibo();
$weibo->$method();