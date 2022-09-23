<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class expense
{
    private $id;
    private $dateHeure;
    private $montant;
    private $libelle;
    private $gid;
    private $uid;

    public function __construct(){
        $this->id = 0;
        $this->dateHeure = 0;
        $this->montant = 0;
        $this->libelle = "";
        $this->gid = 0;
        $this->uid = 0;
    }

    public function __get($prop){
        switch($prop){
            case "libelle": return ucfirst($this->$prop);
            default: return $this->$prop;
        }
    }

    public function __set($prop, $val){
        switch($prop){
            case "libelle": $this->$prop = mb_strtolower($val); break;
            default: $this->$prop = $val;
        }
    }
}