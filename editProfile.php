<?php
    if (!isset($_SESSION))
    {
        session_start();
        if(!isset($_SESSION['account'])){
            header("Location: index.php");
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
    $account_id = -1;
    $del = false;
    $account = unserialize($_SESSION['account']);
    if($account == NULL){
        $account = new account();
    }

    if(isset($_GET['del'])){
        $del = true;
    }

    if (isset($_POST['soumettre'])){
        if($Tools->check_if_empty($_POST['nom'], $_POST['prenom'], $_POST['courriel'])){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(!$Tools->check_email_valid(htmlentities($_POST['courriel']))){
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }else{
            if(htmlentities($_POST['courriel']) == $account->courriel){
                $account->prenom = htmlentities($_POST['prenom']);
                $account->nom = htmlentities($_POST['nom']);
                $result = $ARepo->update_account($account, $message);
                if($result >= 1){
                    $message .= "<strong class='success'>Votre compte a été mis à jour</strong><br>";
                }else{
                    $message .= "<strong class='warning'>Une erreur est survenue</strong><br>";
                }
            }else{
                if($ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message) != -1) {
                    $message .= "<strong class='warning'>L'email entré est déjà utilisé</strong><br>";
                }else{
                    $account->prenom = htmlentities($_POST['prenom']);
                    $account->nom = htmlentities($_POST['nom']);
                    $account->courriel = htmlentities($_POST['courriel']);
                    $result = $ARepo->update_account($account, $message);
                    if($result >= 1){
                        $account_id = $ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message);
                        unset($_SESSION['account']);
                        $_SESSION['account'] = serialize($ARepo->get_account($account_id, $message));
                        $message .= "<strong class='success'>Votre compte a été mis à jour</strong><br>";
                    }else{
                        $message .= "<strong class='warning'>Une erreur est survenue</strong><br>";
                    }
                }
            }
        }
    }

    if (isset($_POST['soumettre2'])){
        if($Tools->check_if_empty($_POST['motPasse'], $_POST['motPasse2'])){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(htmlentities($_POST['motPasse']) != htmlentities($_POST['motPasse2'])){
            $message .= "<strong class='warning'>Les mots de passe ne correspondent pas</strong><br>";
        }else{
            $account->motPasse = htmlentities($_POST['motPasse']);
            $result = $ARepo->update_account($account, $message);
            if($result >= 1){
                $message .= "<strong class='success'>Votre compte a été mis à jour</strong><br>";
            }else{
                $message .= "<strong class='warning'>Une erreur est survenue</strong><br>";
            }
        }
    }

    if (isset($_POST['soumettre4'])){
        $account->estActif = 0;
        $result = $ARepo->update_account($account, $message);
        if($result >= 1){
            $message .= "<strong class='success'>Ce compte a été désactivé</strong><br>";
            header("Location: index.php?reset=true");
            die();
        }else{
            $message .= "<strong class='warning'>Une erreur a été encontrée</strong><br>";
        }
    }
?>
<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <h1>Éditer Profil</h1>
            <section>
                <h2><?php echo $account->prenom . " " . $account->nom ?></h2>
                <?php if(!$del): ?>
                    <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                    ?>
                    <form action="editProfile.php" method="POST" enctype="application/x-www-form-urlencoded">
                        <label for="nom">Nom </label>
                        <input id="nom" name="nom" type="text" autofocus required placeholder="Dubois" value="<?php echo $account->nom; ?>"> <br />
                        <label for="prenom">Prénom </label>
                        <input id="prenom" name="prenom" type="text" required placeholder="Robert" value="<?php echo $account->prenom; ?>"> <br />
                        <label for="courriel">Courriel </label>
                        <input id="courriel" name="courriel" type="email" required placeholder="duboisrobert@exemple.com" value="<?php echo $account->courriel; ?>"> <br />
                        <input type="submit" name="soumettre" value="Valider">
                    </form><hr/>
                    <form action="editProfile.php" method="POST" enctype="application/x-www-form-urlencoded">
                        <label for="motPasse">Mot de passe </label><input id="motPasse" name="motPasse" type="password" required > <br>
                        <label for="motPasse2">Mot de passe * </label><input id="motPasse2" name="motPasse2" type="password" required > <br>
                        <input type="submit" name="soumettre2" value="Valider">
                    </form><hr/>
                    <form action="editProfile.php?del=1" method="POST" enctype="application/x-www-form-urlencoded">
                        <input type="submit" name="soumettre3" value="Supprimer compte">
                    </form>
                <?php else: ?>
                    <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                    ?>
                    <p>Êtes-vous certain de vouloir supprimer votre compte ?</p>
                    <form action="editProfile.php?del=1" method="POST" enctype="application/x-www-form-urlencoded">
                        <input type="submit" name="soumettre4" value="Oui, supprimer mon compte">
                    </form>
                    <p><a href="editProfile.php">Non, revenir en arrière</a></p>
                <?php endif ?>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
