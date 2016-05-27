<?php

session_start();

usleep(350000);

define('DS', DIRECTORY_SEPARATOR);
define('PATH', dirname(__FILE__).DS);
define('DOC_ROOT', str_replace(DS, '/', realpath($_SERVER['DOCUMENT_ROOT'])));

header("Content-type:text/html; charset=utf-8");
mb_internal_encoding('UTF-8');

include PATH . "migrator.php";
include PATH . "modulebase.php";
include PATH . "functions.php";

if (!is_ajax_request()){ exit; }

$module = $_POST['module'];
$step = $_POST['step'];

if (!file_exists(PATH . "modules/{$module}.php")){ exit; }

include PATH . "modules/{$module}.php";

$classname = "module_{$module}";
$module = new $classname();
$module->init();

$steps = $module->getStepsInfo();

if ($step == 'list') { response($steps); }

$step_function = $steps[$step]['function'];



if (!empty($steps[$step]['is_count'])){
    $from = $_POST['from'];    
    $result = call_user_func(array($module, $step_function), $from);
} else {
    $result = call_user_func(array($module, $step_function));
}

response($result);