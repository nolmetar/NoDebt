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
    require_once 'php/repo/expenseRepository.php';
    use NoDebt\expenseRepository;
    $ERepo = new expenseRepository();

    $message = "";
    $account = unserialize($_SESSION['account']);
    $id_account = $account->id;
    $groups = $GRepo->get_groups_from_participant_part($id_account, $message);

?>

<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <h1>Accueil</h1>
            <?php
                if($message != ""){
                    echo "<p>" . $message . "</p>";
                }
            ?>
            <section>
                <h2>Invitations </h2>
                <?php
                    $count = 0;
                    foreach ($groups as $group){
                        if($group[2] == 0){
                            $gid = $group[1];
                            $selected = $GRepo->get_group($gid, $message);
                            if(!is_null($selected)){
                                echo "<article>";
                                echo "<p>";
                                echo "<strong>". $selected->nom ."</strong>";
                                echo " - " . $ARepo->get_account($selected->uid, $message)->prenom;
                                echo ' - <a href="invite.php?id='. $group[1] .'&accept=1">Accepter</a>';
                                echo ' - <a href="invite.php?id='. $group[1] .'&accept=0">Refuser</a>';
                                echo "</p>";
                                echo "</article><hr/>";
                            }
                            $count += 1;
                        }
                    }
                    if($count == 0){
                        echo "<p>Vous n'avez aucune invitations</p>";
                    }
                ?>
            </section>
            <section>
                <h2>Groupes </h2>
                <?php
                    $count = 0;
                    foreach ($groups as $group){
                        if($group[2] == 1){
                            $gid = $group[1];
                            $selected = $GRepo->get_group($gid, $message);
                            $depenses = $ERepo->get_expenses_from_group($gid, $message);
                            $sumDepenses = 0;
                            foreach ($depenses as $depense){
                                $sumDepenses += $depense[2];
                            }
                            if(!is_null($selected)){
                                echo "<article>";
                                echo "<p>";
                                echo "<strong>". $selected->nom ."</strong>";
                                echo " - " . $ARepo->get_account($selected->uid, $message)->prenom;
                                echo " - " . $sumDepenses . " " . $selected->symbole;
                                echo ' - <a href="group.php?id='. $group[1] .'">Détails</a>';
                                echo "</p>";
                                echo "</article><hr/>";
                            }
                            $count += 1;
                        }
                    }
                    if($count == 0){
                        echo "<p>Vous n'êtes dans aucun groupe</p>";
                    }
                ?>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
