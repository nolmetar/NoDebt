<?php

namespace NoDebt;
require_once 'config.php';
use PDO;
use PDOException;

class db_link
{
    public static function connect($base, &$message){
        try {
            $link = new PDO('mysql:host=' . MYHOST . ';dbname=' . $base . ';charset=UTF8', MYUSER, MYPASS);
            $link->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $link->exec("set names utf8");
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $message .= $e->getMessage().'<br>';
            $link = false;
        }
        return $link;
    }

    public static function disconnect (&$link) {
        $link = null;
    }
}