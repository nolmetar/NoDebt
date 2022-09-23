<?php
    if (!isset($_SESSION))
    {
        session_start();
        if(isset($_SESSION['account'])){
            header("Location: home.php");
            die();
        }
    }
?>
<?php
    require_once 'php/util/tools.php';
    require_once 'php/repo/accountRepository.php';
    use NoDebt\tools;
    use NoDebt\account;
    use NoDebt\accountRepository;

    $message = "";
    $Tools = new tools();
    $ARepo = new accountRepository();
    $Account = new account();
    $recup = false;

    if(isset($_GET['rec'])){
        $rec = htmlspecialchars($_GET['rec']);
        if($rec == 1){
            $recup = true;
        }
    }

    if (isset($_POST['soumettre2'])){
        $courriel = htmlentities($_POST['courriel']);
        if($Tools->check_if_empty($courriel)){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(!$Tools->check_email_valid(htmlentities($_POST['courriel']))) {
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }elseif($ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message) != -1) {
            $account_id = $ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message);
            $Account = $ARepo->get_account($account_id, $message);
            $password = $Tools->generate_password(8);
            $Account->motPasse = $password;
            $result = $ARepo->update_account($Account, $message);
            if($result >= 1){
                $body = "<h1>NoDebt</h1><br/><p>Votre nouveau mot de passe : $password</p>";
                $response = $Tools->send_email($Account->courriel, "Récupération de mot de passe", $body, false);
                if($response){
                    $message .= $response;
                }else{
                    $message .= "<strong class='success'>Un email de récupération vous a été envoyé</strong><br>";
                }
            }
        }else{
            $message .= "<strong class='warning'>Aucun comptes liés à cette adresse</strong><br>";
        }
    }

    if (isset($_POST['soumettre'])){
        $courriel = htmlentities($_POST['courriel']);
        $motPass = htmlentities($_POST['motPasse']);
        if($Tools->check_if_empty($courriel, $motPass)){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(!$Tools->check_email_valid($courriel)){
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }elseif($ARepo->existsInDB($courriel, $message) != -1) {
            $account_id = $ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message);
            $Account = $ARepo->get_account($account_id, $message);
            if($Account->estActif == 1){
                if($ARepo->isPasswordCorrect($courriel, $motPass, $message)){
                    $message .= '<strong class="success">Succès.</strong><br>';
                    $_SESSION['account'] = serialize($ARepo->get_account($account_id, $message));
                    if($_SESSION['account'] != null){
                        if(!isset($_SESSION['account'])){
                            header("Location: login.php");
                        }else{
                            header("Location: home.php");
                        }
                        die();
                    }else{
                        $message .= "<strong class='warning'>Erreur dans l'exportation.</strong><br>";
                    }
                }else{
                    $message .= "<strong class='warning'>Mot de passe incorrect</strong><br>";
                }
            }else{
                $message .= "<strong class='warning'>Ce compte est désactivé</strong><br>";
            }
        }else{
            $message .= "<strong class='warning'>Aucun comptes liés à cette adresse</strong><br>";
        }
    }
?>

<?php require 'inc/head.inc.php'; ?>

        <main>
            <h1>Connexion</h1>
            <section>
                <?php if(!$recup): ?>
                    <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                    ?>
                    <form action="login.php" method="POST" enctype="application/x-www-form-urlencoded">
                        <label for="courriel">Courriel </label>
                        <input id="courriel" name="courriel" type="email" required placeholder="duboisrobert@exemple.com" > <br />
                        <label for="motPasse">Mot de passe </label>
                        <input id="motPasse" name="motPasse" type="password" required > <br>
                        <input type="submit" name="soumettre" value="Valider">
                    </form>
                    <p><a href="login.php?rec=1">Vous avez oublié votre mot de passe ?</a></p>
                    <p>Vous n'avez pas de compte ? <a href="register.php">Créez un compte</a></p>
                <?php else: ?>
                    <p>Récupération de mot de passe</p>
                    <form action="login.php?rec=1" method="POST" enctype="application/x-www-form-urlencoded">
                        <label for="courriel">Courriel </label>
                        <input id="courriel" name="courriel" type="email" required placeholder="duboisrobert@exemple.com" > <br />
                        <input type="submit" name="soumettre2" value="Valider">
                    </form>
                    <p><a href="login.php">Revenir en arrière</a></p>
                <?php endif ?>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
