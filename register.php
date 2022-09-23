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
    $Account = new account();
    $ARepo = new accountRepository();
    $account_id = -1;

    if(isset($_GET['act']) && isset($_GET['email']) && isset($_GET['token'])){
        $act = htmlspecialchars($_GET['act']);
        if($act == 1){
            $email = htmlspecialchars($_GET['email']);
            $token = htmlspecialchars($_GET['token']);
            if($ARepo->existsInDB(strtolower($email), $message) != -1){
                if($ARepo->isPasswordCorrect(strtolower(htmlentities($email)), $token, $message)){
                    $account_id = $ARepo->existsInDB(strtolower(htmlentities($email)), $message);
                    $Account = $ARepo->get_account($account_id, $message);
                    if($Account->estActif == 0){
                        $Account->estActif = 1;
                        $result = $ARepo->update_account($Account, $message);
                        if($result >= 1){
                            $message .= "<strong class='success'>Votre compte a été récupéré, connectez-vous</strong>";
                        }
                    }
                }
            }
        }
    }

    if (isset($_POST['soumettre'])){
        if($Tools->check_if_empty($_POST['nom'], $_POST['prenom'], $_POST['courriel'], $_POST['motPasse'], $_POST['motPasse2'])){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(htmlentities($_POST['motPasse']) != htmlentities($_POST['motPasse2'])){
            $message .= "<strong class='warning'>Les mots de passe ne correspondent pas</strong><br>";
        }elseif(!$Tools->check_email_valid(htmlentities($_POST['courriel']))){
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }elseif($ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message) != -1) {
            $account_id = $ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message);
            $Account = $ARepo->get_account($account_id, $message);
            if($Account->estActif == 1){
                $message .= "<strong class='warning'>Ce compte existe déjà</strong><br>";
            }else{
                $link = "http://192.168.128.13/~e200819/EVAL_V5/register.php?act=1&email=". $Account->courriel ."&token=". $Account->motPasse;
                $body = "<h1>NoDebt</h1><br/><p>Cliquez sur le lien pour récupérer votre compte</p><br/><a href='$link'>$link</a>";
                $response = $Tools->send_email($Account->courriel, "Récupération du compte", $body, false);
                if($response){
                    $message .= $response;
                }else{
                    $message .= "<strong class='success'>Un email de récupération vous a été envoyé</strong><br>";
                }
            }
        }else{
            $Account->courriel = htmlentities($_POST['courriel']);
            $Account->nom = htmlentities($_POST['nom']);
            $Account->prenom = htmlentities($_POST['prenom']);
            $Account->motPasse = htmlentities($_POST['motPasse2']);
            $Account->estActif = 1;
            $noError = $ARepo->create_account($Account, $message);
            if(!$noError){
                $message .= "<strong class='success'>Votre compte a été créé</strong><br>";
            }
        }
    }
?>
<?php require 'inc/head.inc.php'; ?>

        <main>
            <h1>Créer un compte</h1>
            <section>
                <form action="register.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <?php
                        if($message != ""){
                            echo "<p>" . $message . "</p>";
                        }
                    ?>
                    <label for="nom">Nom </label>
                    <input id="nom" name="nom" type="text" autofocus required placeholder="Dubois" > <br />
                    <label for="prenom">Prénom </label>
                    <input id="prenom" name="prenom" type="text" required placeholder="Robert" > <br />
                    <label for="courriel">Courriel </label>
                    <input id="courriel" name="courriel" type="email" required placeholder="duboisrobert@exemple.com" > <br />
                    <label for="motPasse">Mot de passe </label>
                    <input id="motPasse" name="motPasse" type="password" required > <br>
                    <label for="motPasse2">Mot de passe * </label>
                    <input id="motPasse2" name="motPasse2" type="password" required > <br>

                    <input type="reset" name="reinitialiser" value="Réinitialiser"> <br />
                    <input type="submit" name="soumettre" value="Valider">
                </form>
                <p>Vous avez un compte ? <a href="login.php">Connectez-vous</a></p>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
