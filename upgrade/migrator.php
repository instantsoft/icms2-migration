<?php

class Migrator {
    
    private $db = array();
    private $prefix = array();
    private $limit = 10;
    
    public function __construct(){
        
        $db = $_SESSION['upgrade']['db1'];	
        $this->db[1] = new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);
        $this->db[1]->set_charset('utf8');
		
        $db = $_SESSION['upgrade']['db2'];	
        $this->db[2] = new mysqli($db['host'], $db['user'], $db['pass'], $db['base']);
		$this->db[2]->set_charset('utf8');
        
        $this->prefix[1] = $_SESSION['upgrade']['db1']['prefix'];
        $this->prefix[2] = $_SESSION['upgrade']['db2']['prefix'];
        
    }
	
	public function getTableSize1($table, $where=false){
		return $this->getTableSize($table, $where, 1);
	}
	
	public function getTableSize2($table, $where=false){
		return $this->getTableSize($table, $where, 2);
	}
	
	private function getTableSize($table, $where, $db){
		
		$sql = "SELECT COUNT(*) FROM {$this->prefix[$db]}{$table} ";
		
		if ($where) { $sql .= " WHERE {$where}"; }
				
        $result = $this->db[$db]->query($sql);
		
		if (!$result->num_rows) { return 0; }
		
		$row = $result->fetch_row();
		
		return $row[0];
		
	}
	
	public function getRow1($table, $where){
		return $this->getRow($table, $where, 1);
	}
    
	public function getRow2($table, $where){
		return $this->getRow($table, $where, 2);
	}
    
	private function getRow($table, $where, $db){
		
		$sql = "SELECT * FROM {$this->prefix[$db]}{$table} WHERE {$where} LIMIT 1";
				
        $result = $this->db[$db]->query($sql);
        
        if (!$result->num_rows) { return false; }
        
        $row = $result->fetch_assoc();
        
        return $row;
		
	}
	
    public function get1($table, $from=false, $where=false){
        return $this->get($table, $from, $where, 1);
    }
    
    public function get2($table, $from=false, $where=false){
        return $this->get($table, $from, $where, 2);
    }
    
    private function get($table, $from, $where, $db){
        
        $sql = "SELECT * FROM {$this->prefix[$db]}{$table}";
		
		if ($where) { $sql .= " WHERE {$where}"; }
		
		if ($from !== false) { $sql .= " LIMIT {$from}, {$this->limit}"; }
        
        $result = $this->db[$db]->query($sql);
        
        if (!$result->num_rows) { return false; }
        
        $rows = array();
        
        while($row = $result->fetch_assoc()){
            $rows[] = $row;
        }
        
        return $rows;
        
    }

    public function update1($table, $where, $data){
        return $this->update($table, $where, $data, 1);
    }
    
    public function update2($table, $where, $data){
        return $this->update($table, $where, $data, 2);
    }
    
	private function update($table, $where, $data, $db){

		$set = array();

		foreach ($data as $field=>$value) {
            $value = $this->prepareValue($field, $value);
			$set[] = "`{$field}` = {$value}";
		}

        $set = implode(', ', $set);

		$sql = "UPDATE {$this->prefix[$db]}{$table} SET {$set} WHERE {$where}";

		$this->db[$db]->query($sql);

	}
    
	public function drop1($table){
		return $this->drop($table, 1);
	}
	
	public function drop2($table){
		return $this->drop($table, 2);
	}
	
	private function drop($table, $db){
		$this->db[$db]->query("DROP TABLE {$this->prefix[$db]}{$table}");
	}
    
	public function truncate1($table, $where=false){
		return $this->truncate($table, $where, 1);
	}
	
	public function truncate2($table, $where=false){
		return $this->truncate($table, $where, 2);
	}
	
	private function truncate($table, $where, $db){
		if ($where){
			$sql = "DELETE FROM {$this->prefix[$db]}{$table} WHERE {$where}";
		} else {
			$sql = "TRUNCATE TABLE {$this->prefix[$db]}{$table}";
		}
		$this->db[$db]->query($sql);
	}
	
    public function insert1($table, $data){
        return $this->insert($table, $data, 1);
    }
    
    public function insert2($table, $data){
        return $this->insert($table, $data, 2);
    }   	
	
	private function insert($table, $data, $db){

        $fields = array();
		$values = array();

        if (is_array($data)){

			foreach ($data as $field => $value){

                $value = $this->prepareValue($field, $value);

                $fields[] = "`$field`";
                $values[] = $value;

			}

            $fields = implode(', ', $fields);
            $values = implode(', ', $values);

			$sql = "INSERT INTO {$this->prefix[$db]}{$table} ({$fields})\nVALUES ({$values})";
			$this->db[$db]->query($sql);
			
			return $this->db[$db]->insert_id;

		}

	}    
    
    private function prepareValue($field, $value){

        if (is_array($value)){ $value = "'". $this->escape($this->arrayToYaml($value)) ."'"; } else

        if (mb_strpos($field, 'date_')===0 && ($value == '' || is_null($value))) { $value = "CURRENT_TIMESTAMP"; }  else

        if (is_bool($value)) { $value = (int)$value; } else

        if ($value === '' || is_null($value)) { $value = "NULL"; }
        else {
            $value = $this->escape(trim($value));
            $value = "'{$value}'";
        }

        return $value;

    }    
    
	private function escape($string){
		return @$this->db[1]->real_escape_string($string);
	}
    
    public function close(){
        $this->db[1]->close();
        $this->db[2]->close();        
    }
    
    public function arrayToYaml($array) {
		include_once DOC_ROOT . "/system/libs/spyc.class.php";
		$yaml = Spyc::YAMLDump($array,2,40);
        return $yaml;
    }

    public function yamlToArray($yaml) {
        include_once DOC_ROOT . "/system/libs/spyc.class.php";
        $array = Spyc::YAMLLoad($yaml);
        return $array;
    }	
	
	public function import1($file, $placeholders=array()){
		return $this->import($file, $placeholders, 1);
	}
	
	public function import2($file, $placeholders=array()){
		return $this->import($file, $placeholders, 2);
	}
	
	private function import($file, $placeholders=array(), $db){
		
		set_time_limit(0);

		$file = DOC_ROOT . '/upgrade/sql/' . $file . '.sql';

		if (!is_file($file)){ return false; }

		$file = fopen($file, 'r');

		$query = array();
		
		$placeholders['#'] = $this->prefix[$db];
		
		$delimiter = ';';

		while (feof($file) === false){

			$query[] = fgets($file);

			if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1){

				$query = trim(implode('', $query));

				if ($placeholders){
					foreach($placeholders as $param=>$value){
						$query = str_replace("{{$param}}", $value, $query);
					}
				}

				$result = $this->db[$db]->query($query);

				if ($result === false) {
					return false;
				}

			}

			if (is_string($query) === true){
				$query = array();
			}

		}

		fclose($file);

		return true;

	}
	
}