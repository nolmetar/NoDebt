<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class invoice
{
    private $id;
    private $scan;
    private $did;

    public function __construct(){
        $this->id = 0;
        $this->scan = "";
        $this->did = "";
    }

    public function __get($prop){
        return $this->$prop;
    }

    public function __set($prop, $val){
        switch($prop){
            default: $this->$prop = $val;
        }
    }
}