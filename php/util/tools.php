<?php

namespace NoDebt;

require 'php/PHPMailer/src/PHPMailer.php';
require 'php/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

setlocale(LC_TIME, 'fr_FR.utf8','fra');

class tools
{
    public function check_if_empty(...$vars){
        $response = False;
        foreach ($vars as $v) {
            if(empty($v) || strlen(trim($v)) == 0){
                $response = True;
            }
        }
        return $response;
    }

    public function check_email_valid($email){
        if(filter_var(strtolower($email), FILTER_VALIDATE_EMAIL)){
            return True;
        }
        return False;
    }

    public function send_email($to, $subject, $body, $to_admin){
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('a.demany@student.helmo.be');
            if($to_admin){
                $mail->addAddress('a.demany@student.helmo.be');
                $mail->addCC($to);
            }else{
                $mail->addAddress($to);
            }
            $mail->addReplyTo('no-reply@helmo.be');
            $mail->Subject = $subject;
            $mail->Body = $body;
            if($to_admin){
                $mail->isHTML(false);
            }else{
                $mail->isHTML(true);
            }
            $mail->send();
        } catch(Exception $e){
            return '<strong class="warning">Erreur survenue lors de l\'envoi de l\'email<br>'. $mail->ErrorInfo . '</strong>';
        }
        return false;
    }

    public function generate_password($length){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
}