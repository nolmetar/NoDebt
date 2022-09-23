<?php
    if (!isset($_SESSION))
    {
        session_start();
    }
?>

<?php require 'inc/head.inc.php'; ?>

<main>
    <h1>404</h1>
    <section>
        <cite>Êtes-vous perdu ?</cite><br />
        <a href="index.php">Retrouver son chemin</a>
    </section>
</main>

<?php require 'inc/footer.inc.php'; ?>
