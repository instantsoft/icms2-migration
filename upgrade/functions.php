<?php

function is_ajax_request(){
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){ return false; }
    return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

function render($template_name, $data=array()){
    extract($data);
    ob_start();
    include PATH . "templates/{$template_name}.php";
    return ob_get_clean();
}

function run_step($step, $is_submit=false){
    require PATH . "steps/{$step['id']}.php";
    $result = step($is_submit);
    return $result;
}

function make_json($array){

    $json = '{';
    $pairs = array();

    foreach($array as $key=>$val){
        if (!is_numeric($val)) { $val = "'{$val}'"; }
        $pairs[] = "{$key}: $val";
    }

    $json .= implode(', ', $pairs);
    $json .= '}';

    return $json;

}

function html_bool_span($value, $condition){
    if ($condition){
        return '<span class="positive">' . $value . '</span>';
    } else {
        return '<span class="negative">' . $value . '</span>';
    }
}

function response($array){
    $json = json_encode($array);
    echo $json; 
    die();
}
