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
    require_once 'php/repo/accountRepository.php';
    use NoDebt\accountRepository;
    $ARepo = new accountRepository();
    require_once 'php/repo/groupRepository.php';
    use NoDebt\groupRepository;
    $GRepo = new groupRepository();
    require_once 'php/util/tools.php';
    use NoDebt\tools;
    $Tools = new tools();

    $account = unserialize($_SESSION['account']);
    $message = "";
    $group = null;
    $gid = -1;
    $accept = -1;

    if(isset($_GET['id'])){
        $gid = htmlentities($_GET['id']);
        $group = $GRepo->get_group($gid, $message);
        $participants = $GRepo->get_groups_from_participant_group($gid, $message);
        $part_of_group = false;
        foreach ($participants as $parti) {
            if($parti[0] == $account->id){
                $part_of_group = true;
            }
        }
        if(!$part_of_group){
            $group = null;
        }else{
            if(isset($_GET['accept'])){
                $accept = $_GET['accept'];
                if($accept == 0){
                    $GRepo->delete_group_participation($account->id, $gid, $message);
                    header("Location: index.php");
                    die();
                }elseif ($accept == 1){
                    $GRepo->update_group_participation($account->id, $gid, 1, $message);
                    $location = "Location: group.php?id=" . $gid;
                    header($location);
                    die();
                }else{
                    $group = null;
                }
            }
        }
    }

    if(!isset($_GET['id']) || is_null($group)){
        header("Location: index.php");
        die();
    }

    if (isset($_POST['soumettre'])){
        $courriel = htmlentities($_POST['courriel']);
        if($Tools->check_if_empty($courriel)){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }elseif(!$Tools->check_email_valid($courriel)) {
            $message .= "<strong class='warning'>L'email entré n'est pas valide</strong><br>";
        }elseif ($courriel == $account->courriel){
            $message .= "<strong class='warning'>Vous ne pouvez pas vous inviter vous-même</strong><br>";
        }elseif($ARepo->existsInDB(strtolower(htmlentities($_POST['courriel'])), $message) != -1) {
            $invite_account_id = $ARepo->existsInDB(strtolower($courriel), $message);
            $courriel_co = $account->courriel;
            $body_from = "<h1>NoDebt</h1><br/><p>Vous avez invité une personne au groupe $group->nom</p>";
            $Tools->send_email($courriel_co, "NoDebt - invitation", $body_from, false);
            $body_to = "<h1>NoDebt</h1><br/><p>Vous avez été invité au groupe $group->nom</p><br/><p>Rendez-vous sur votre page d'invitations</p>";
            $Tools->send_email($courriel, "NoDebt - invitation", $body_to, false);
            $GRepo->add_participant($gid, $invite_account_id, false,$message);
            $message .= "<strong class='success'>L'invitation a été envoyée</strong><br>";
        }else{
            $message .= "<strong class='warning'>Ce compte n'existe pas</strong><br>";
        }
    }
?>

<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <h1>Inviter</h1>
            <section>
                <h2>Soirée Halloween</h2>
                <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                ?>
                <form action="invite.php?id=<?php echo $gid; ?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <label for="courriel">Courriel </label>
                    <input id="courriel" name="courriel" type="email" required placeholder="duboisrobert@exemple.com" >
                    <input type="submit" name="soumettre" value="Inviter">
                </form>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
