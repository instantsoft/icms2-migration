<?php

class module_start extends modulebase {
    
    public function getStepsInfo(){
        return array(
            array(
                'function' => 'prepare', 
                'title' => 'Подготовка к первой миграции...'
            ),            
        );
    }
    
	public function prepare(){
		
		$ctypes = $this->mg->get2('content_types');
		
		if ($ctypes){
			foreach($ctypes as $ctype){
				$name = $ctype['name'];
				$this->mg->drop2("con_{$name}");
				$this->mg->drop2("con_{$name}_cats");
				$this->mg->drop2("con_{$name}_fields");
				$this->mg->drop2("con_{$name}_props");
				$this->mg->drop2("con_{$name}_props_bind");
				$this->mg->drop2("con_{$name}_props_values");
				$this->mg->truncate2("perms_users", "subject = '{$name}'");
				$this->mg->truncate2("menu_items", "url = '{content:{$name}}'");
			}
		}
		
		$this->mg->truncate2('content_types');
		$this->mg->truncate2('content_folders');
		$this->mg->truncate2('content_datasets');
		$this->mg->truncate2('rss_feeds');
		$this->mg->truncate2('tags');
		$this->mg->truncate2('tags_bind');
		
	}	
    
}
