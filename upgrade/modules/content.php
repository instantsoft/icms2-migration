<?php

class module_content extends modulebase {
    
    public function getStepsInfo(){
        return array(
            array(
                'function' => 'prepare', 
                'title' => 'Подготовка к переносу статей...'
            ),
            array(
                'function' => 'cats', 
                'title' => 'Создание категорий...'
            ),
            array(
                'function' => 'migrate', 
                'is_count' => true,
                'title' => 'Перенос статей',
				'count' => $this->mg->getTableSize1('content')
            ),
        );
    }
    
    public function prepare(){				
		
		$this->createContentType('articles', 'Статьи');
		
		$cats = $_POST['cats'];
		
		if (!$cats) { return; }
		
		$cats = json_decode($cats, true);
		
		foreach($cats as $c){
			
			$this->createContentType($c['slug'], $c['title']);			 			
			
		}
        
    }	
    
    public function cats(){
        
		$post_cats = $_POST['cats'];
		if ($post_cats) { $post_cats = json_decode($post_cats, true); }
		
		$cats = $this->mg->get1('category');
		
        
        
    }
    
    public function migrate($from){
        
        $rows = $this->mg->get1('content', $from);
        
        return array('error'=>false, 'rows'=>count($rows));
        
    }
	
	private function createContentType($name, $title){
		
		$id = $this->mg->insert2('content_types', array(
			'name' => $name,
			'title' => $title,
			'description' => $title . ' / Статьи InstantCMS 1.10.3',
			'is_cats' => true,
			'is_cats_recursive' => true,
			'is_comments' => true,
			'is_comments_tree' => true,
			'is_rating' => true,
			'is_tags' => true,
			'is_auto_keys' => true,
			'is_auto_desc' => true,
			'is_auto_url' => true,
			'options' => '---\nis_cats_change: 1\nis_cats_open_root: 1\nis_cats_only_last: null\nis_show_cats: 1\nis_tags_in_list: 1\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: 1\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nis_cats_keys: 1\nis_cats_desc: 1\nis_cats_auto_url: null\n',
			'labels' => '---\none: статья\ntwo: статьи\nmany: статей\ncreate: статью\n'
		));		

		$this->mg->insert2('rss_feeds', array(
			'ctype_id' => $id,
			'ctype_name' => $name,
			'title' => $title,
			'mapping' => array(
				'title' => 'title',
				'description' => 'content',
				'pubDate' => 'date_pub',
				'image' => '',
				'image_size' => 'normal'
			)
		));
		
		$this->mg->import2('content_data', array(
			'title' => $title,
			'name' => $name,
			'id' => $id
		));
		
		$this->mg->import2('content_fields', array(
			'title' => $title,
			'name' => $name,
			'id' => $id
		));
		
	}
    
}
