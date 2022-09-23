<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/account.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class accountRepository
{
    const TABLE_NAME = 'utilisateur';

    public function create_account(account $account, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(courriel, nom, prenom, motPasse, estActif) VALUES (:courriel, :nom, :prenom, :motPasse, :estActif)");
            $stmt->bindValue(':courriel', $account->courriel);
            $stmt->bindValue(':nom', $account->nom);
            $stmt->bindValue(':prenom', $account->prenom);
            $stmt->bindValue(':motPasse', $account->motPasse);
            $stmt->bindValue(':estActif', $account->estActif);
            if ($stmt->execute() && $stmt->rowCount() == 1){
                $message .= "<strong>Bienvenue, $account->prenom !</strong><p><a href='login.php'>Vers la page de connection</a></p>";
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

    public function existsInDB($courriel, &$message){
        $result = -1;
        $bdd = null;
        $courriel = strtolower($courriel);
        if(!filter_var($courriel, FILTER_VALIDATE_EMAIL)){
            $result = 1;
            $message .= 'Courriel invalide';
        }else{
            try {
                $bdd  = db_link::connect(MYDB, $message);
                $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE courriel = :courriel");
                $stmt->bindValue(':courriel', $courriel);
                if ($stmt->execute()){
                    $row = $stmt->fetch();
                    if($row !== false){
                        $result = $row[0];
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
        }
        return $result;
    }

    public function isPasswordCorrect($courriel, $motPasse, &$message){
        $result = false;
        $bdd = null;
        $courriel = strtolower($courriel);
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT motPasse FROM ".self::TABLE_NAME." WHERE courriel = :courriel");
            $stmt->bindValue(':courriel', $courriel);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row[0] == $motPasse){
                    $result = true;
                }else{
                    $result = false;
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

    public function get_account($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE uid = :uid");
            $stmt->bindValue(':uid', $id);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = new account();
                    $result->id = $row[0];
                    $result->courriel = $row[1];
                    $result->nom = $row[2];
                    $result->prenom = $row[3];
                    $result->motPasse = $row[4];
                    $result->estActif = $row[5];
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

    public function update_account(account $account, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET courriel = :courriel, nom = :nom, prenom = :prenom, motPasse = :motPasse, estActif = :estActif WHERE uid = :uid");
            $stmt->bindValue(':uid', $account->id);
            $stmt->bindValue(':courriel', $account->courriel);
            $stmt->bindValue(':nom', $account->nom);
            $stmt->bindValue(':prenom', $account->prenom);
            $stmt->bindValue(':motPasse', $account->motPasse);
            $stmt->bindValue(':estActif', $account->estActif);
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

    public function delete_account(Account $account, &$message){
        //Pas de suppression de compte, seulement une désactivation
        $account->estActif = 0;
        return $this->update_account($account, $message);
    }
}