<?php
    require_once 'php/repo/accountRepository.php';
    use NoDebt\accountRepository;

    //$account = (account);
    $account = unserialize($_SESSION['account']);
    $nom = "?";
    $prenom = "?";
    if($account != NULL){
        $nom = $account->nom;
        $prenom = $account->prenom;
    }
?>
        <header>
            <nav>
                <div>
                    <a href="login.php" title="Se connecter">NoDebt</a>
                    <a href="home.php" title="Vers l'accueil">Home</a>
                    <a href="editGroup.php" title="Nouveau groupe">Créer Groupe</a>
                </div>
                <div>
                    <a href="editProfile.php" title="Vers le profil">Profil (<?php echo $prenom . " " . $nom ?>)</a>
                    <a href="index.php?reset=true" title="Se déconnecter">Se déconnecter</a>
                </div>
            </nav>
        </header>