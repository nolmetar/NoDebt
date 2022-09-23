<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/payment.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class paymentRepository
{
    const TABLE_NAME = 'versement';

    public function create_payment(payment $payment, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(gid, uid, uid_1, dateHeure, montant, estConfirme) VALUES (:gid, :uid, :uid_1, :dateHeure, :montant, :estConfirme)");
            $stmt->bindValue(':gid', $payment->gid);
            $stmt->bindValue(':uid', $payment->uid);
            $stmt->bindValue(':uid_1', $payment->uid_1);
            $stmt->bindValue(':dateHeure', $payment->dateHeure);
            $stmt->bindValue(':montant', $payment->montant);
            $stmt->bindValue(':estConfirme', $payment->estConfirme);
            if ($stmt->execute() && $stmt->rowCount() == 1){
                $message .= "<strong>Le versement a été créé.</strong>";
                $noError = true;
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $noError;
    }

    public function get_payments_from_group($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE gid = :gid");
            $stmt->bindValue(':gid', $id);
            if ($stmt->execute()){
                $rows = $stmt->fetchAll();
                if($rows !== false){
                    $result = $rows;
                }
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur E: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $result;
    }
    public function get_payments_from_sender($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE uid = :uid");
            $stmt->bindValue(':uid', $id);
            if ($stmt->execute()){
                $rows = $stmt->fetchAll();
                if($rows !== false){
                    $result = $rows;
                }
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur E: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $result;
    }
    public function get_payments_from_receiver($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE uid_1 = :uid_1");
            $stmt->bindValue(':uid_1', $id);
            if ($stmt->execute()){
                $rows = $stmt->fetchAll();
                if($rows !== false){
                    $result = $rows;
                }
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur E: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $result;
    }

    public function update_payment(payment $payment, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET montant = :montant, estConfirme = :estConfirme WHERE gid = :gid AND uid = :uid AND uid_1 = :uid_1 AND dateHeure = :dateHeure");
            $stmt->bindValue(':montant', $payment->montant);
            $stmt->bindValue(':estConfirme', $payment->estConfirme);
            $stmt->bindValue(':gid', $payment->gid);
            $stmt->bindValue(':uid', $payment->uid);
            $stmt->bindValue(':uid_1', $payment->uid_1);
            $stmt->bindValue(':dateHeure', $payment->dateHeure);
            if ($stmt->execute()){
                $result = $stmt->rowCount();
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur E: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $result;
    }

    public function delete_payment(payment $payment, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME.
                " WHERE gid = :gid AND uid = :uid AND uid_1 = :uid_1 AND dateHeure = :dateHeure");
            $stmt->bindValue(':gid', $payment->gid);
            $stmt->bindValue(':uid', $payment->uid);
            $stmt->bindValue(':uid_1', $payment->uid_1);
            $stmt->bindValue(':dateHeure', $payment->dateHeure);
            if ($stmt->execute()){
                $result = $stmt->rowCount();
            } else {
                $message .= 'Une erreur système est survenue.<br> 
                    Veuillez essayer à nouveau plus tard ou contactez l\'administrateur du site. 
                    (Code erreur E: ' . $stmt->errorCode() . ')<br>';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        db_link::disconnect($bdd);
        return $result;
    }
}