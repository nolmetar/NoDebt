<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/expense.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class expenseRepository
{
    const TABLE_NAME = 'depense';

    public function create_expense(expense $expense, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(dateHeure, montant, libelle, gid, uid) VALUES (:un, :deux, :trois, :quatre, :cinq)");
            $stmt->bindValue(':un', date('Y-m-d H:i:s', $expense->dateHeure));
            $stmt->bindValue(':deux', $expense->montant);
            $stmt->bindValue(':trois', $expense->libelle);
            $stmt->bindValue(':quatre', $expense->gid);
            $stmt->bindValue(':cinq', $expense->uid);
            if ($stmt->execute() && $stmt->rowCount() == 1){
                $message .= "<strong>La dépense a été créé.</strong>";
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

    public function get_expense($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE did = :did");
            $stmt->bindValue(':did', $id);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = new expense();
                    $result->id = $row[0];
                    $result->dateHeure = $row[1];
                    $result->montant = $row[2];
                    $result->libelle = $row[3];
                    $result->gid = $row[4];
                    $result->uid = $row[5];
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

    public function get_expenses_from_group($id, &$message){
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

    public function get_expenses_from_group_and_owner($gid, $uid, &$message){
        $result = null;
        $bdd = null;
        $uid = (int) $uid;
        $gid = (int) $gid;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE uid = :uid AND gid = :gid");
            $stmt->bindValue(':uid', $uid);
            $stmt->bindValue(':gid', $gid);
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

    public function update_expense(expense $expense, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET dateHeure = :dateHeure, montant = :montant, libelle = :libelle, gid = :gid, uid = :uid WHERE gid = :gid");
            $stmt->bindValue(':gid', $expense->id);
            $stmt->bindValue(':dateHeure', date('Y-m-d H:i:s', $expense->dateHeure));
            $stmt->bindValue(':montant', $expense->montant);
            $stmt->bindValue(':libelle', $expense->libelle);
            $stmt->bindValue(':gid', $expense->gid);
            $stmt->bindValue(':uid', $expense->uid);
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

    public function delete_expense(expense $expense, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME.
                " WHERE did = :did");
            $stmt->bindValue(':did', $expense->id);
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