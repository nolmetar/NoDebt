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
    require_once 'php/repo/expenseRepository.php';
    use NoDebt\expenseRepository;
    $ERepo = new expenseRepository();
    require_once 'php/repo/invoiceRepository.php';
    use NoDebt\invoice;
    use NoDebt\invoiceRepository;
    $IRepo = new invoiceRepository();
    require_once 'php/util/tools.php';
    use NoDebt\tools;
    $Tools = new tools();

    $expense = null;
    $invoice = null;
    $eid = -1;
    $message = "";

    if(isset($_GET['ex'])){
        $eid = $_GET['ex'];
        $expense = $ERepo->get_expense($eid, $message);
        if(!is_null($expense)){
            $invoice = $IRepo->get_invoices_from_expense($eid, $message);
        }
    }

    if(!isset($_GET['ex']) || is_null($expense)){
        header("Location: index.php");
        die();
    }

    if (isset($_POST['soumettre'])){

        if(!isset($_FILES['fichier'])){
            $message .= "<strong class='warning'>Aucun fichier envoyé</strong>";
        }elseif ($_FILES['fichier']['error'] > 0){
            $message .= "<strong class='warning'>Une erreur a été détectée</strong>";
        }else{
            $destination = "uploads/" . $_FILES['fichier']['name'];
            move_uploaded_file($_FILES['fichier']['tmp_name'], $destination);
            if(is_null($invoice)){
                $invoice = new invoice();
                $invoice->scan = $_FILES['fichier']['name'];
                $invoice->did = $eid;
                $IRepo->create_invoice($invoice, $message);
            }else{
                $invoice->scan = $_FILES['fichier']['name'];
                $invoice->did = $eid;
                $IRepo->update_invoice($invoice, $message);
            }
            $message .= "<strong class='success'>Votre fichier est enregistré</strong>";
        }
    }
?>
<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <h1><?php echo is_null($invoice) ? "Ajouter" : "Éditer" ?> Scan</h1>
            <section>
                <h2>Dépense "<?php echo $expense->libelle ?>"</h2>
                <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                ?>
                <form action="editScan.php?ex=<?php echo $eid ?>" method="POST" enctype="multipart/form-data">
                    Scan <a href="<?php echo is_null($invoice) ? '#' : './uploads/' . $invoice->scan ?>"><?php echo is_null($invoice) ? 'Pas de scan' : $invoice->scan ?></a> <br />
                    <label for="fichier">Choisir fichier </label>
                    <input id="fichier" name="fichier" type="file" accept="image/*, .pdf, .docx"> <br />
                    <input type="submit" name="soumettre" value="Valider">
                </form>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
