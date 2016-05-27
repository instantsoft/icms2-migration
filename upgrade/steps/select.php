<?php

function step($is_submit){

	$counts = get_counts();
	$cats = get_cats();
	
    $result = array(
        'html' => render('step_select', array(
			'counts' => $counts,
			'cats' => $cats,
		))
    );

    return $result;

}

function get_cats(){
	
	$cats = array();
	
	$db = $_SESSION['upgrade']['db1'];	
	$mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);
	$mysqli->set_charset('utf8');
	$p = $db['prefix'];			
	
	$result = $mysqli->query("SELECT id, title, seolink FROM {$p}category WHERE NSLevel=1 ORDER BY NSLeft");
	if ($result->num_rows){
		while ($cat = $result->fetch_assoc()){
			$cats['content'][] = $cat;
		}
	}
	
	$result = $mysqli->query("SELECT id, title FROM {$p}uc_cats WHERE NSLevel=1 ORDER BY NSLeft");	
	if ($result->num_rows){
		while ($cat = $result->fetch_assoc()){
			$cats['catalog'][] = $cat;
		}
	}
	
	return $cats;
	
}

function get_counts(){
	
	$counts = array();
	
	$db = $_SESSION['upgrade']['db1'];	
	$mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);
	$p = $db['prefix'];
	
	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}content");
	$row = $result->fetch_row();
	$counts['content'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}blog_posts");
	$row = $result->fetch_row();
	$counts['blogs'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}board_items");
	$row = $result->fetch_row();
	$counts['board'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}uc_items");
	$row = $result->fetch_row();
	$counts['catalog'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}clubs");
	$row = $result->fetch_row();
	$counts['clubs'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}faq_quests");
	$row = $result->fetch_row();
	$counts['faq'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}photo_albums");
	$row = $result->fetch_row();
	$counts['photos'] = $row[0];

	$result = $mysqli->query("SELECT COUNT(*) FROM {$p}users");
	$row = $result->fetch_row();
	$counts['users'] = $row[0];
	
	$mysqli->close();

	return $counts;
	
}
