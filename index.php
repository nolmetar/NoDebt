<?php
if (!isset($_SESSION))
{
    session_start();
}
?>

<?php
    if (isset($_GET['reset'])) {
        session_destroy();
    }
?>

<?php
    header("Location: login.php");


    require_once 'php/repo/accountRepository.php';
    use NoDebt\account;
    use NoDebt\accountRepository;
    $ARepo = new accountRepository();

    require_once 'php/repo/expenseRepository.php';
    use NoDebt\expense;
    use NoDebt\expenseRepository;
    $ERepo = new expenseRepository();

    require_once 'php/repo/groupRepository.php';
    use NoDebt\group;
    use NoDebt\groupRepository;
    $GRepo = new groupRepository();

    require_once 'php/repo/invoiceRepository.php';
    use NoDebt\invoice;
    use NoDebt\invoiceRepository;
    $IRepo = new invoiceRepository();

    require_once 'php/repo/paymentRepository.php';
    use NoDebt\payment;
    use NoDebt\paymentRepository;
    $PRepo = new paymentRepository();

    require_once 'php/repo/tagRepository.php';
    use NoDebt\tag;
    use NoDebt\tagRepository;
    $TRepo = new tagRepository();

    require_once 'php/util/tools.php';
    use NoDebt\tools;
    $Tools = new tools();

    $result = null;
    $message = null;

    //$Account = new account();
    //$result = $ARepo->existsInDB("savannahchang@mail.com", $message);
    //$result = $ARepo->existsInDB("pomme", $message);
    //$result = $ARepo->get_account($result, $message);
    //$result->motPasse = "123";
    //$result = $ARepo->update_account($result, $message);
    //$result = $ARepo->delete_account($result, $message);
    //$Group = new group();
    //$Group->id = 3;
    //$Group->nom = "La fête en gros partie 2";
    //$Group->devise = "EUR";
    //$Group->symbole = "€";
    //$Group->solde = 0;
    //$Group->uid = 1;
    //$result = $GRepo->create_group($Group, $message);
    //$result = $GRepo->get_groups_from_owner(1, $message);
    //$result = $result[1]["nom"];
    //$result = $GRepo->get_group(1,$message);
    //$result = $result->nom;
    //$result = $GRepo->get_groups_from_participant(1, $message);
    //$result = $result[1][1];
    //$result = $GRepo->delete_group($Group, $message);
    //$result = $Tools->check_if_empty('pomme');

    if($result == null){
        $result = "/";
    }
    if($message == null){
        $message = "/";
    }
?>

<?php require 'inc/head.inc.php'; ?>

        <main>
            <h1>Index</h1>
            <p>$message = <?php echo $message; ?></p>
            <p>$result = <?php echo $result; ?></p>
        </main>

<?php require 'inc/footer.inc.php'; ?>