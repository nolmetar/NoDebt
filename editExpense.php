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
    use NoDebt\expense;
    use NoDebt\expenseRepository;
    $ERepo = new expenseRepository();
    require_once 'php/repo/invoiceRepository.php';
    use NoDebt\invoiceRepository;
    $IRepo = new invoiceRepository();
    require_once 'php/util/tools.php';
    use NoDebt\tools;
    $Tools = new tools();

    $account = unserialize($_SESSION['account']);
    $eid = -1;
    $gid = -1;
    $edit_mode = false;
    $group = null;
    $expense = null;
    $participants = null;
    $scan = null;
    $message = "";

    if(isset($_GET['id'])){
        $gid = htmlentities($_GET['id']);
        $group = $GRepo->get_group($gid, $message);
        if(!is_null($group)){
            $participants = $GRepo->get_groups_from_participant_group($gid, $message);
        }
    }

    if (isset($_GET['id']) && isset($_GET['ex'])) {
        $edit_mode = true;
        $eid = htmlentities($_GET['ex']);
        $expense = $ERepo->get_expense($eid, $message);
        if(is_null($expense)){
            $group = null;
        }else{
            $scan = $IRepo->get_invoices_from_expense($eid, $message);
        }
    }

    if(!isset($_GET['id']) || is_null($group)){
        header("Location: index.php");
        die();
    }

    if (isset($_POST['soumettre'])){
        if($Tools->check_if_empty($_POST['date'], $_POST['participant'], $_POST['label'], $_POST['tags'], $_POST['prix'])){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }else{
            $date = new DateTime($_POST['date']);
            if($edit_mode){
                $expense = $ERepo->get_expense($eid, $message);
                $expense->dateHeure = $date->getTimestamp();
                $expense->montant = $_POST['prix'];
                $expense->libelle = $_POST['label'];
                $expense->gid = $gid;
                $expense->uid = $_POST['participant'];
                $ERepo->update_expense($expense, $message);
                $message .= "<strong class='success'>La dépense a été modifiée.</strong>";
            }else{
                $expense = new expense();
                $expense->dateHeure = $date->getTimestamp();
                $expense->montant = $_POST['prix'];
                $expense->libelle = $_POST['label'];
                $expense->gid = $gid;
                $expense->uid = $_POST['participant'];
                $ERepo->create_expense($expense, $message);
                $message .= "<strong class='success'>La dépense a été créée.</strong>";
            }
            $page = "Location: group.php?id=" . $gid;
            header($page);
            die();
        }
    }

    if (isset($_POST['soumettre2'])){
        if(!is_null($expense)){
            $ERepo->delete_expense($expense, $message);
            $message .= "<strong class='success'>La dépense a été supprimée.</strong>";
            $page = "Location: group.php?id=" . $gid;
            header($page);
            die();
        }
    }
?>
<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>
<?php $today = date("Y-m-d"); ?>

        <main>
            <h1><?php echo $edit_mode ? "Éditer" : "Créer" ?> dépense</h1>
            <section>
                <?php
                    $expense_text = "";
                    if($edit_mode){
                        $expense_text = ': Dépense "' . $expense->libelle . '"';
                    }
                ?>
                <h2>Groupe "<?php echo $group->nom ?>" <?php echo $expense_text ?></h2>
                <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                    $page_dest = "";
                    if($edit_mode){
                        $page_dest = "editExpense.php?id=" . $gid . "&ex=" . $eid;
                    }else{
                        $page_dest = "editExpense.php?id=" . $gid;
                    }
                ?>
                <form action="<?php echo $page_dest; ?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <label for="date"><?php echo $edit_mode ? "Éditer" : "" ?> Date </label>
                    <?php $dateExpense = date("YYYY-mm-dd", strtotime($expense->dateHeure)); ?>
                    <input id="date" name="date" type="date" required value="<?php echo $edit_mode ? $dateExpense : $today; ?>" > <br />
                    <label for="participant"><?php echo $edit_mode ? "Éditer" : "" ?> Participant </label>
                    <select id="participant" name="participant">
                        <?php
                            foreach ($participants as $par){
                                $parti = $ARepo->get_account($par[0], $message);
                                echo "<option value='$par[0]'>$parti->prenom $parti->nom</option>";
                            }
                        ?>
                    </select> <br />
                    <label for="label"><?php echo $edit_mode ? "Éditer" : "" ?> Label </label>
                    <input id="label" name="label" type="text" required placeholder="chapeau" value="<?php echo $edit_mode ? $expense->libelle : '' ?>" > <br />
                    <label for="tags"><?php echo $edit_mode ? "Éditer" : "" ?> Tags </label>
                    <input id="tags" name="tags" type="text" required placeholder="déguisements, fun, ..." > <br />
                    <label for="prix"><?php echo $edit_mode ? "Éditer" : "" ?> Prix </label>
                    <input id="prix" name="prix" type="number" required placeholder="21.05€" value="<?php echo $edit_mode ? $expense->montant : '' ?>"> <br />
                    <?php
                        $scan_text = "pas de scan";
                        $scan_text_link = "Ajouter un scan";
                        $scan_link = "#";
                        if(!is_null($scan)){
                            $scan_text = $scan->scan;
                            $scan_link = "./uploads/" . $scan->scan;
                            $scan_text_link = "Modifier le scan";
                        }
                    ?>
                    <?php echo $edit_mode ? "Éditer" : "" ?> Scan <a href="<?php echo $scan_link ?>"><?php echo $scan_text ?></a> <a href="editScan.php?ex=<?php echo $eid ?>"><?php echo $scan_text_link ?></a>
                    <br />
                    <input type="submit" name="soumettre" value="Valider">
                </form>
                <?php if($edit_mode): ?>
                    <form action="<?php echo $page_dest; ?>" method="POST" enctype="application/x-www-form-urlencoded">
                        <input type="submit" name="soumettre2" value="Supprimer la dépense">
                    </form>
                <?php endif ?>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
