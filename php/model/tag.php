<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class tag
{
    private $id;
    private $tag;
    private $gid;

    public function __construct(){
        $this->id = 0;
        $this->tag = "";
        $this->gid = 0;
    }

    public function __get($prop){
        return ($prop == "tag") ? ucfirst($this->$prop) : $this->$prop;
    }

    public function __set($prop, $val){
        switch($prop){
            case "tag": $this->$prop = mb_strtolower($val); break;
            default: $this->$prop = $val;
        }
    }
}