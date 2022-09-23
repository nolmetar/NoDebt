<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/tag.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class tagRepository
{
    const TABLE_NAME = 'tag';

    public function create_tag(tag $tag, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(tag, gid) VALUES (:tag, :gid)");
            $stmt->bindValue(':tag', $tag->tag);
            $stmt->bindValue(':gid', $tag->gid);
            if ($stmt->execute() && $stmt->rowCount() == 1){
                $message .= "<strong>Le groupe a été créé.</strong>";
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

    public function get_tag($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE tid = :tid");
            $stmt->bindValue(':tid', $id);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = new tag();
                    $result->id = $row[0];
                    $result->tag = $row[1];
                    $result->gid = $row[2];
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

    public function get_tags_from_group($id, &$message){
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

    public function update_tag(tag $tag, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET tag = :tag, gid = :gid WHERE tid = :tid");
            $stmt->bindValue(':tid', $tag->id);
            $stmt->bindValue(':tag', $tag->tag);
            $stmt->bindValue(':gid', $tag->gid);
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

    public function delete_tag(tag $tag, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME.
                " WHERE tid = :tid");
            $stmt->bindValue(':tid', $tag->id);
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