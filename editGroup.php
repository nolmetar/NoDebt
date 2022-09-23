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
    use NoDebt\account;
    use NoDebt\accountRepository;
    $ARepo = new accountRepository();
    require_once 'php/repo/groupRepository.php';
    use NoDebt\group;
    use NoDebt\groupRepository;
    $GRepo = new groupRepository();
    require_once 'php/util/tools.php';
    use NoDebt\tools;
    $Tools = new tools();

    $account = unserialize($_SESSION['account']);
    $id_account = $account->id;

    $message = "";
    $Group = null;
    $is_creator = false;

    $editOrCreate = "";
    if (isset($_GET['id'])) {
        $editOrCreate = "Éditer";
        $Group = $GRepo->get_group($_GET['id'], $message);
        if($Group == null){
            header("Location: index.php");
            die();
        }
        if($Group->uid == $account->id){
            $is_creator = true;
        }
    }else{
        $editOrCreate = "Choisir";
    }

    if (isset($_POST['soumettre'])){
        $nom = htmlspecialchars($_POST['nom']);
        $devise = htmlspecialchars($_POST['devise']);
        if($Tools->check_if_empty($nom, $devise)){
            $message .= "<strong class='warning'>Un ou plusieurs champs sont vides</strong><br>";
        }else{
            if (isset($_GET['id'])) {
                if($is_creator){
                    $Group->nom = $nom;
                    $Group->devise = $devise;
                    $Group->symbole = '$';
                    switch($devise){
                        case "EUR":
                            $Group->symbole = '€';
                            break;
                        case "USD":
                            $Group->symbole = '$';
                            break;
                        case "GBP":
                            $Group->symbole = '£';
                            break;
                        case "JPY":
                            $Group->symbole = '¥';
                            break;
                        case "ILS":
                            $Group->symbole = 'Sh.';
                            break;
                        default:
                            $Group->symbole = '';
                    }
                    $GRepo->update_group($Group, $message);
                    $message .= "<strong class='success'>Le groupe a été modifié.</strong>";
                }
            }else{
                $Group = new group();
                $Group->nom = $nom;
                $Group->devise = $devise;
                $Group->uid = $id_account;
                switch($devise){
                    case "EUR":
                        $Group->symbole = '€';
                        break;
                    case "USD":
                        $Group->symbole = '$';
                        break;
                    case "GBP":
                        $Group->symbole = '£';
                        break;
                    case "JPY":
                        $Group->symbole = '¥';
                        break;
                    case "ILS":
                        $Group->symbole = 'Sh.';
                        break;
                    default:
                        $Group->symbole = '';
                }
                $GRepo->create_group($Group, $message);
                $message .= "<strong class='success'>Le groupe a été créé.</strong>";
            }
        }
    }
?>

<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <?php
                if (isset($_GET['id'])) {
                    echo '<h1>Éditer Groupe</h1>';
                }else{
                    echo '<h1>Créer Groupe</h1>';
                }
            ?>
            <section>
                <?php
                    if (isset($_GET['id'])) {
                        echo '<h2>Groupe "'. $Group->nom .'"</h2>';
                    }
                ?>

                <?php if(isset($_GET['id']) && !$is_creator): ?>
                    <strong class="warning">Vous n'êtes pas le créateur du groupe</strong>
                <?php else: ?>
                    <?php
                    if($message != ""){
                        echo "<p>" . $message . "</p>";
                    }
                    if(isset($_GET['id'])){
                        $page_dest = "editGroup.php?id=" . $_GET['id'];
                    }else{
                        $page_dest = "editGroup.php";
                    }
                    ?>
                    <form action="<?php echo $page_dest; ?>" method="POST" enctype="application/x-www-form-urlencoded">
                        <label for="nom"><?php echo $editOrCreate; ?> nom </label>
                        <input id="nom" name="nom" type="text" required placeholder="Halloween" > <br />
                        <label for="desc"><?php echo $editOrCreate; ?> description </label>
                        <input id="desc" name="desc" type="text" required placeholder="La fête d'Halloween" > <br />
                        <label for="devise"><?php echo $editOrCreate; ?> Devise</label>
                        <select id="devise" name="devise">
                            <option value="EUR" selected>EUR (€)</option>
                            <option value="USD">USD ($)</option>
                            <option value="JPY">JPY (¥)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="ILS">ILS (₪)</option>
                        </select> <br />
                        <input type="submit" name="soumettre" value="Valider">
                    </form><hr />
                    <?php if(isset($_GET['id'])): ?>
                    <form>
                        <input type="submit" name="soumettre2" value="Supprimer le groupe">
                    </form>
                    <?php endif ?>
                <?php endif ?>
            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
