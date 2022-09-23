<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class group
{
    private $id;
    private $nom;
    private $devise;
    private $symbole;
    private $solde;
    private $uid;

    public function __construct(){
        $this->id = 0;
        $this->nom = "";
        $this->devise = "";
        $this->symbole = "";
        $this->solde = 0;
        $this->uid = 0;
    }

    public function __get($prop){
        return ($prop == "nom") ? ucfirst($this->$prop) : $this->$prop;
    }

    public function __set($prop, $val){
        switch($prop){
            case "nom": $this->$prop = mb_strtolower($val); break;
            case "devise": $this->$prop = mb_strtoupper($val); break;
            default: $this->$prop = $val;
        }
    }
}