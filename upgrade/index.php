<?php

session_start();

define('DS', DIRECTORY_SEPARATOR);
define('PATH', dirname(__FILE__).DS);
define('DOC_ROOT', str_replace(DS, '/', realpath($_SERVER['DOCUMENT_ROOT'])));

header("Content-type:text/html; charset=utf-8");
mb_internal_encoding('UTF-8');

include PATH . "functions.php";

$steps = array(
    array('id' => 'start', 'title' => 'Вступление'),
    array('id' => 'database1', 'title' => 'База данных 1.10.6'),
    array('id' => 'database2', 'title' => 'База данных 2.x'),
    array('id' => 'select', 'title' => 'Выбор контента'),
    array('id' => 'migrate', 'title' => 'Миграция'),
    array('id' => 'finish', 'title' => 'Завершение')
);

$current_step = 0;

if (is_ajax_request()){
    $step = $steps[$_POST['step']];
    $is_submit = isset($_POST['submit']);
    echo json_encode( run_step($step, $is_submit) );
    exit();
}

$step_result = run_step($steps[$current_step], false);

echo render('main', array(
    'steps' => $steps,
    'current_step' => $current_step,
    'step_html' => $step_result['html']
));
