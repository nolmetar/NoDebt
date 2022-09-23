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
    require_once 'php/repo/invoiceRepository.php';
    use NoDebt\invoiceRepository;
    $IRepo = new invoiceRepository();

    $message = "";
    $group = null;
    $gid = -1;
    $expenses = null;
    $participants = null;
    $part_number = 0;
    $totalExpense = 0;
    $account = unserialize($_SESSION['account']);

    if(isset($_GET['id'])){
        $gid = htmlentities($_GET['id']);
        $group = $GRepo->get_group($gid, $message);
        if(!is_null($group)){
            $expenses = $ERepo->get_expenses_from_group($gid, $message);
            foreach ($expenses as $expense){
                $totalExpense += $expense[2];
            }
            $participants = $GRepo->get_groups_from_participant_group($gid, $message);
            $part_number = sizeof($participants);
            $part_of_group = false;
            foreach ($participants as $parti) {
                if($parti[2] == 1 && $parti[0] == $account->id){
                    $part_of_group = true;
                }
            }
            if(!$part_of_group){
                $group = null;
            }
        }
    }

    if(!isset($_GET['id']) || is_null($group)){
        header("Location: index.php");
        die();
    }
?>
<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <?php
                if($message != ""){
                    echo "<p>" . $message . "</p>";
                }
            ?>
            <?php if(isset($_GET['id']) && !is_null($group)): ?>
                <h1>Groupe - <?php echo $group->nom; ?> - <?php echo number_format($totalExpense, 2, ',', ' ') . $group->symbole; ?> (<?php echo number_format($totalExpense/$part_number, 2, ',', ' ') . $group->symbole; ?>/pers)</h1>
                <cite>Options du groupe</cite><br />
                <a href="invite.php?id=<?php echo $gid; ?>">Inviter des participants</a>
                <a href="editGroup.php?id=<?php echo $gid; ?>">Éditer le groupe</a>
                <section>
                    <h2>Recherche</h2>
                    <form>
                        <label for="search">Libellé/tags</label>
                        <input id="search" name="search" type="text" required placeholder="Chapeau rigolo" ><br />
                        <input type="submit" name="soumettre" value="Valider">
                    </form><br />
                    <form>
                        <label for="search">Libellé</label>
                        <input id="search" name="search" type="text" required placeholder="Chapeau rigolo" > <br />
                        <label >Montants </label>
                        <input id="montant1" name="montant1" type="number" required placeholder="15" >
                        <input id="montant2" name="montant2" type="number" required placeholder="50" ><br />
                        <label for="tags">Tags</label>
                        <input id="tags" name="tags" type="text" required placeholder="déguisements" ><br />
                        <label >Dates </label>
                        <input id="date1" name="date1" type="date" required >
                        <input id="date2" name="date2" type="date" required ><br />
                        <input type="submit" name="soumettre2" value="Valider">
                    </form><br />
                    <form>
                        <input type="submit" name="soumettre3" value="Réinitialiser recherche">
                    </form>
                </section>
                <section>
                    <h2>Dépenses</h2>
                    <p>
                        <a href="editExpense.php?id=<?php echo $gid; ?>">Ajouter une dépense</a>
                    </p>
                    <section class="table">
                        <article>
                            <p><strong>Quand</strong></p>
                            <p><strong>Qui</strong></p>
                            <p><strong>Quoi</strong></p>
                            <p><strong>Combien</strong></p>
                            <p><strong>Facture</strong></p>
                            <p><strong>Éditer</strong></p>
                        </article>
                        <!--<article>
                            <p>21/08/21</p>
                            <p>Basile</p>
                            <p>Chapeau</p>
                            <p>21.00€</p>
                            <p><a href="#">Nomdefacture.pdf</a></p>
                            <p><a href="editExpense.php?id=415">Éditer</a></p>
                        </article>-->
                        <?php
                            foreach ($expenses as $expens){
                                $account = $ARepo->get_account($expens[5], $message);
                                $invoice = $IRepo->get_invoices_from_expense($expens[0], $message);
                                echo '<article>';
                                echo '<p>' . $expens[1] . '</p>';
                                echo '<p>' . $account->prenom . ' ' . $account->nom . '</p>';
                                echo '<p>' . $expens[3] . '</p>';
                                echo '<p>' . number_format($expens[2], 2, ',', ' ') . $group->symbole . '</p>';
                                echo '<p>' . is_null($invoice) ? "Pas de facture" : "<a href='$invoice->scan'>$invoice->scan</a>" . '</p>';
                                echo '<p>' . "<a href='editExpense.php?id=$gid&ex=$expens[0]'>Éditer</a>" . '</p>';
                                echo '</article>';
                            }
                        ?>
                    </section>
                    <p>Moyenne des dépenses : <?php echo number_format($totalExpense/$part_number, 2, ',', ' ') . $group->symbole; ?></p>
                </section>
                <section>
                    <h2>Participants</h2>
                    <section class="table">
                        <article>
                            <p><strong>Noms</strong></p>
                            <p><strong>Dépenses</strong></p>
                            <p><strong>Écarts</strong></p>
                        </article>
                        <!--<article>
                            <p>Basile Blaise *</p>
                            <p>122.05€</p>
                            <p>-.--€</p>
                        </article>-->
                        <?php
                            foreach ($participants as $parti){
                                if($parti[2] == 1){
                                    $par_exp = $ERepo->get_expenses_from_group_and_owner($gid, $parti[0], $message);
                                    $total_exp = 0;
                                    foreach ($par_exp as $exp){
                                        $total_exp += $exp[2];
                                    }
                                    $ecart = $total_exp-($totalExpense/$part_number);
                                    $account = $ARepo->get_account($parti[0], $message);
                                    $is_creator = $group->uid == $parti[0] ? "*" : "";
                                    echo '<article>';
                                    echo '<p>' . $account->prenom . " " . $account->nom . " " . $is_creator . '</p>';
                                    echo '<p>' . number_format($total_exp, 2, ',', ' ') . $group->symbole . '</p>';
                                    echo '<p>' . number_format($ecart, 2, ',', ' ') . $group->symbole . '</p>';
                                    echo '</article>';
                                }
                            }
                        ?>
                    </section>
                </section>
                <section>
                    <h2>Solder le groupe</h2>
                    <form action="payment.php">
                        <input type="submit" name="soumettre" value="Solder le groupe">
                    </form>
                </section>
            <?php else: ?>
                <strong class="warning">Groupe : pas de groupe trouvé</strong>
            <?php endif ?>
        </main>

<?php require 'inc/footer.inc.php'; ?>
