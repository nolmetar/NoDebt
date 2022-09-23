<?php

namespace NoDebt;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class payment
{
    private $gid;
    private $uid;
    private $uid_1;
    private $dateHeure;
    private $montant;
    private $estConfirme;

    public function __construct(){
        $this->gid = 0;
        $this->uid = 0;
        $this->uid_1 = 0;
        $this->dateHeure = 0;
        $this->montant = 0;
        $this->estConfirme = 0;
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