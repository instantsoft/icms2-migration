<?php

class modulebase {
    
    public $mg;
    
    public function init(){
        $this->mg = new Migrator();
    }
    
}