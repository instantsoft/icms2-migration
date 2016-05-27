<?php

class module_users extends modulebase {
    
    public function getStepsInfo(){
        return array(
            array(
                'function' => 'prepare', 
                'title' => 'Подготовка к переносу пользователей...'
            ),
            array(
                'function' => 'migrate', 
				'title' => 'Перенос пользователей',
                'is_count' => true,
				'count' => $this->mg->getTableSize1('users', "is_deleted=0")
            ),
            array(
                'function' => 'messages', 
				'title' => 'Перенос личных сообщений',
                'is_count' => true,
				'count' => $this->mg->getTableSize1('user_msg', "to_del=0 AND from_del=0")
            ),
            array(
                'function' => 'wall', 
				'title' => 'Перенос записей на стенах',
                'is_count' => true,
				'count' => $this->mg->getTableSize1('user_wall')
            ),
        );
    }
    
	public function prepare(){
		
		$this->mg->truncate2('users');
		$this->mg->truncate2('users_contacts');
		$this->mg->truncate2('users_groups_members');
		$this->mg->truncate2('users_friends');
		$this->mg->truncate2('users_ignors');
		$this->mg->truncate2('users_karma');
		$this->mg->truncate2('users_messages');
		$this->mg->truncate2('users_notices');
		$this->mg->truncate2('users_statuses');
		$this->mg->truncate2('wall_entries');
		
	}
	
    public function migrate($from){
        
        $users = $this->mg->get1('users', $from, 'is_deleted=0');
        $rows = count($users);
		
		$gmap = array(
			1 => 4,
			9 => 5,
			2 => 6,
			7 => 5
		);
		
		foreach($users as $u){
			
			$p = $this->mg->getRow1('user_profiles', "user_id = '{$u['id']}'");
			
			$password_salt = md5(implode(':', array(microtime(true), session_id(), time(), rand(0, 10000))));
			$password_salt = substr($password_salt, rand(1,8), 16);
			$password_hash = md5($u['password'] . $password_salt);
			
			$invites = $this->mg->get1('user_invites', false, "owner_id='{$u['id']}' AND is_sended=0");
			$invites_count = $invites ? count($invites) : 0;
			
			$friends = $this->mg->get1('user_friends', false, "(from_id='{$u['id']}' OR to_id='{$u['id']}') AND is_accepted=1");
			$friends_count = $friends ? count($friends) : 0;
			
			if ($friends_count){
				$friends = $this->mg->get1('user_friends', false, "from_id='{$u['id']}' AND is_accepted=1");
				if ($friends){
					foreach($friends as $f){
						$this->mg->insert2('users_friends', array(
							'user_id' => $f['from_id'],
							'friend_id' => $f['to_id'],
							'is_mutual' => true
						));
						$this->mg->insert2('users_friends', array(
							'user_id' => $f['to_id'],
							'friend_id' => $f['from_id'],
							'is_mutual' => true
						));
					}
				}
			}
			
			$group_id = 4;
			if (isset($gmap[$u['group_id']])){ $group_id = $gmap[$u['group_id']]; }
			
			$city_id = null;
			if ($p['city']){
				$city = $this->mg->getRow2('geo_cities', "name='{$p['city']}'");
				if ($city){ $city_id = $city['id']; }
			}
			
			$avatar = null;
			if ($p['imageurl']){
				$avatar = array(
					'original' => 'old/users/avatars/' . $p['imageurl'],
					'big' => 'old/users/avatars/' . $p['imageurl'],
					'normal' => 'old/users/avatars/' . $p['imageurl'],
					'small' => 'old/users/avatars/small/' . $p['imageurl'],
					'micro' => 'old/users/avatars/small/' . $p['imageurl']
				);
			}
			
			if ($p['karma']){
				$karma = $this->mg->get1('user_karma', false, "user_id='{$u['id']}'");
				if ($karma){
					foreach($karma as $k){
						$this->mg->insert2('users_karma', array(
							'user_id' => $k['sender_id'],
							'profile_id' => $k['user_id'],
							'date_pub' => $k['senddate'], 
							'points' => $k['points']
						));
					}
				}
			}
			
			$this->mg->insert2('users', array(
				
				'id' => $u['id'],
				'groups' => array($group_id),
				'email' => $u['email'],
				'password' => $password_hash,
				'password_salt' => $password_salt,
				'is_admin' => $u['group_id'] == 2,
				'nickname' => $u['nickname'],
				'ip' => $u['last_ip'],
				'date_reg' => $u['regdate'],
				'date_log' => $u['logdate'],
				'inviter_id' => $u['invited_by'],
				'invites_count' => $invites_count,
				'friends_count' => $friends_count,
				'hobby' => $p['description'],
				'karma' => $p['karma'],
				'birth_date' => $u['birthdate'],
				'icq' => $u['icq'],
				'city' => $city_id,
				'avatar' => $avatar
				
			));
			
		}
		
        return array('error'=>false, 'rows'=>$rows);
        
    }
	
	public function messages($from){
		
		$messages = $this->mg->get1('user_msg', $from, "to_del=0 AND from_del=0");		
		$rows = count($messages);
		
		foreach($messages as $m){
			
			$contact = $this->mg->getRow2("users_contacts", "user_id = '{$m['to_id']}'");
			if (!$contact){
				$this->mg->insert2('users_contacts', array(
					'user_id' => $m['to_id'],
					'contact_id' => $m['from_id'],
				));
				$this->mg->insert2('users_contacts', array(
					'user_id' => $m['from_id'],
					'contact_id' => $m['to_id'],
				));
			}
			
			$this->mg->insert2('users_messages', array(
				'to_id' => $m['to_id'],
				'from_id' => $m['from_id'],
				'date_pub' => $m['senddate'],
				'is_new' => $m['is_new'],
				'content' => $m['message'],
			));
			
		}
		
		return array('error'=>false, 'rows'=>$rows);
		
	}
	
	public function wall($from){
		
		$messages = $this->mg->get1('user_wall', $from);
		$rows = count($messages);
		
		foreach($messages as $m){
			
			$this->mg->insert2('wall_entries', array(
				'date_pub' => $m['pubdate'],
				'profile_type' => $m['usertype']=='users' ? 'user' : 'group',
				'profile_id' => $m['user_id'],
				'user_id' => $m['author_id'],
				'content' => $m['content'],
				'content_html' => $m['content'],
			)); 
			
		}
		
		return array('error'=>false, 'rows'=>$rows);
		
	}
    
}
