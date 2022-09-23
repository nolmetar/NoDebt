<?php
    if (!isset($_SESSION))
    {
        session_start();
    }
?>
<?php
    require_once 'php/util/tools.php';
    require_once 'php/model/account.php';
    use NoDebt\tools;
    use NoDebt\account;

    $email = "";
    $message = "";
    $Tools = new tools();
    $Account = new account();

    if (isset($_POST['soumettre'])){
        if($Tools->check_if_empty($_POST['objet'], $_POST['courriel'], $_POST['type'], $_POST['corps'])){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(!$Tools->check_email_valid(htmlentities($_POST['courriel']))){
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }else{
            $response = $Tools->send_email($_POST['courriel'], $_POST['objet'], $_POST['corps'], true);
            if($response){
                $message .= $response;
            }else{
                $message .= "<strong class='success'>L'email a été envoyé</strong>";
            }
        }
    }

    if(isset($_SESSION['account'])){
        $account = unserialize($_SESSION['account']);
        $email = $account->courriel;
    }
?>

<?php require 'inc/head.inc.php'; ?>

        <main>
            <h1>Contact</h1>
            <section>
                <form action="contact.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <?php
                    if(!empty($message)){
                        echo "<p>" . $message . "</p>";
                    }
                    ?>
                    <label for="objet">Objet </label>
                    <input id="objet" name="objet" type="text" autofocus required placeholder="Problème de ..." > <br />
                    <label for="courriel">Courriel </label>
                    <input id="courriel" name="courriel" type="email" required placeholder="jean@exemple.com" <?php if($email != ""){ echo "value='". $email . "'"; } ?> > <br />
                    <label>Type</label>
                    <label class="radio"><input id="compte" name="type" type="radio" value="Compte" required >Problème de compte</label>
                    <label class="radio"><input id="autre" name="type" type="radio" value="Autre">Autre</label> <br />
                    <label for="corps">Corps </label>
                    <textarea id="corps" name="corps" cols="40" rows="5" required placeholder="J'ai un problème avec mon compte" ></textarea> <br />

                    <input type="reset" name="reinitialiser" value="Réinitialiser"> <br />
                    <input type="submit" name="soumettre" value="Valider">
                </form>
                <a href="index.php">Retrouver son chemin</a>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
