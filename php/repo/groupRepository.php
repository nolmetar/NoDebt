<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/group.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class groupRepository
{
    const TABLE_NAME = 'groupe';
    const TABLE_REL_NAME = 'participer';

    public function create_group(group $group, &$message){
        $result = $this->make_group($group, $message);
        $result2 = false;
        $groups = $this->get_groups_from_owner($group->uid, $message);
        foreach ($groups as $gr){
            $gid = $gr[0];
            $groups_part = $this->get_groups_from_participant_group($gid, $message);
            $is_input_in = false;
            foreach ($groups_part as $grp){
                if($grp[0] == $group->uid && $grp[1] == $gid) {
                    $is_input_in = true;
                }
            }
            if(!$is_input_in){
                $result2 = $this->add_participant($gid, $group->uid, true, $message);
            }
        }
        if($result && $result2){
            return true;
        }else{
            return false;
        }
    }

    public function make_group(group $group, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(nom, devise, symbole, solde, uid) VALUES (:nom, :devise, :symbole, :solde, :uid)");
            $stmt->bindValue(':nom', $group->nom);
            $stmt->bindValue(':devise', $group->devise);
            $stmt->bindValue(':symbole', $group->symbole);
            $stmt->bindValue(':solde', $group->solde);
            $stmt->bindValue(':uid', $group->uid);
            if ($stmt->execute() && $stmt->rowCount() == 1){
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

    public function add_participant($gid, $uid, $isConfirmed, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_REL_NAME.
                "(uid, gid, estConfirme) VALUES (:uid, :gid, :estConfirme)");
            $stmt->bindValue(':uid', $uid);
            $stmt->bindValue(':gid', $gid);
            $stmt->bindValue(':estConfirme', $isConfirmed ? 1 : 0);
            if ($stmt->execute() && $stmt->rowCount() == 1){
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

    public function get_group($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE gid = :gid");
            $stmt->bindValue(':gid', $id);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = new group();
                    $result->id = $row[0];
                    $result->nom = $row[1];
                    $result->devise = $row[2];
                    $result->symbole = $row[3];
                    $result->solde = $row[4];
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

    public function get_groups_from_owner($id, &$message){
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

    public function get_groups_from_participant_part($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_REL_NAME." WHERE uid = :uid");
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

    public function get_groups_from_participant_group($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_REL_NAME." WHERE gid = :gid");
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

    public function get_group_participation($gid, $uid, &$message){
        $result = -1;
        $bdd = null;
        $gid = (int) $gid;
        $uid = (int) $uid;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE gid = :gid AND uid = :uid");
            $stmt->bindValue(':gid', $gid);
            $stmt->bindValue(':uid', $uid);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = $row[2];
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

    public function update_group(group $group, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET nom = :nom, devise = :devise, symbole = :symbole, solde = :solde, uid = :uid WHERE gid = :gid");
            $stmt->bindValue(':gid', $group->id);
            $stmt->bindValue(':nom', $group->nom);
            $stmt->bindValue(':devise', $group->devise);
            $stmt->bindValue(':symbole', $group->symbole);
            $stmt->bindValue(':solde', $group->solde);
            $stmt->bindValue(':uid', $group->uid);
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

    public function update_group_participation($uid, $gid, $parti, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_REL_NAME.
                " SET estConfirme = :estConfirme WHERE uid = :uid AND gid = :gid");
            $stmt->bindValue(':estConfirme', $parti);
            $stmt->bindValue(':uid', $uid);
            $stmt->bindValue(':gid', $gid);
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

    public function delete_group(group $group, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME.
                " WHERE gid = :gid");
            $stmt->bindValue(':gid', $group->id);
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

    public function delete_group_participation($uid, $gid, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_REL_NAME.
                " WHERE gid = :gid AND uid = :uid");
            $stmt->bindValue(':gid', $gid);
            $stmt->bindValue(':uid', $uid);
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