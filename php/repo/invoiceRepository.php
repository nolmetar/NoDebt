<?php

namespace NoDebt;

require_once 'php/db/db_link.php';
require_once 'php/model/invoice.php';

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class invoiceRepository
{
    const TABLE_NAME = 'facture';

    public function create_invoice(invoice $invoice, &$message){
        $noError = false;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME.
                "(scan, did) VALUES (:scan, :did)");
            $stmt->bindValue(':scan', $invoice->scan);
            $stmt->bindValue(':did', $invoice->did);
            if ($stmt->execute() && $stmt->rowCount() == 1){
                $message .= "<strong>La facture a été créée.</strong>";
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

    public function get_invoice($id, &$message){
        $result = null;
        $bdd = null;
        $id = (int) $id;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE fid = :fid");
            $stmt->bindValue(':fid', $id);
            if ($stmt->execute()){
                $row = $stmt->fetch();
                if($row !== false){
                    $result = new invoice();
                    $result->id = $row[0];
                    $result->scan = $row[1];
                    $result->did = $row[2];
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

    public function get_invoices_from_expense($id, &$message){
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
                    $result = new invoice();
                    $result->id = $row[0];
                    $result->scan = $row[1];
                    $result->did = $row[2];
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

    public function update_invoice(invoice $invoice, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME.
                " SET scan = :scan, did = :did WHERE fid = :fid");
            $stmt->bindValue(':fid', $invoice->id);
            $stmt->bindValue(':scan', $invoice->scan);
            $stmt->bindValue(':did', $invoice->did);
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

    public function delete_group(invoice $invoice, &$message){
        $result = null;
        $bdd = null;
        try {
            $bdd  = db_link::connect(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME.
                " WHERE fid = :fid");
            $stmt->bindValue(':fid', $invoice->id);
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