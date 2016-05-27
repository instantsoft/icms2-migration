<?php

function step($is_submit){

    if ($is_submit){
        return check_db();
    }

    $result = array(
        'html' => render('step_database2', array(
        ))
    );

    return $result;

}

function check_db(){

    $db = $_POST['db'];

    $db['host'] = trim($db['host']);
    $db['user'] = trim($db['user']);
    $db['base'] = trim($db['base']);

	if (!$db['host'] || !$db['user'] || !$db['base'] || !$db['prefix']){
		return array(
			'error' => true,
			'message' => 'Заполните все поля необходимые для подключения'
		);
	}
	
	$mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);
	
    if ($mysqli->connect_error) {
        return array(
            'error' => true,
            'message' => sprintf('Ошибка подключения к базе: %s', $mysqli->connect_error)
        );
    }

    $mysqli->close();

	$_SESSION['upgrade']['db2'] = $db;

    return array(
        'error' => false,
        'message' => ''
    );

}
