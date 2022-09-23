<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class account
{
    private $id;
    private $courriel;
    private $nom;
    private $prenom;
    private $motPasse;
    private $estActif;

    public function __construct(){
        $this->id = 0;
        $this->courriel = "";
        $this->nom = "";
        $this->prenom = "";
        $this->motPasse = "";
        $this->estActif = 0;
    }

    public function __get($prop){
        return ($prop == "prenom") ? ucfirst($this->$prop) : $this->$prop;
    }

    public function __set($prop, $val){
        switch($prop){
            case "prenom":
            case "courriel": $this->$prop = mb_strtolower($val); break;
            case "nom": $this->$prop = mb_strtoupper($val); break;
            default: $this->$prop = $val;
        }
    }
}