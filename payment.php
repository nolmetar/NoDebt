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

<?php require 'inc/head.inc.php'; ?>
<?php require 'inc/header.inc.php'; ?>

        <main>
            <h1>Versements - Soirée Halloween</h1>
            <section>
                <h2>Statistiques</h2>
                <p>
                    Somme des dépenses: 1150.00€<br/>
                    Moyenne des dépenses: 287.50€<br/>
                    Restant total à rembourser: 120.00€
                </p>
            </section>
            <section>
                <h2>Rappel</h2>
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>Dépense</th>
                        <th>Écart</th>
                        <th>Montant reçu</th>
                        <th>Écart restant</th>
                    </tr>
                    <tr>
                        <td>Basile Blaise *</td>
                        <td>122.05€</td>
                        <td>-.--€</td>
                        <td>20.00€</td>
                        <td>20.00€</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Morgane Garnier</td>
                        <td>214.01€</td>
                        <td>-.--€</td>
                        <td>20.00€</td>
                        <td>20.00€</td>
                    </tr>
                    <tr>
                        <td>Ludivine Bourdillon</td>
                        <td>541.02€</td>
                        <td>-.--€</td>
                        <td>20.00€</td>
                        <td>20.00€</td>
                    </tr>
                    <tr>
                        <td>Godefroy Fabre</td>
                        <td>123.05€</td>
                        <td>-.--€</td>
                        <td>20.00€</td>
                        <td>20.00€</td>
                    </tr>
                </table>
            </section>
            <section>
                <h2>Recommendations</h2>
                <table>
                    <tr>
                        <th>Envoyeur</th>
                        <th>Receveur</th>
                        <th>Montant</th>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                    </tr>
                </table>
            </section>
            <section>
                <h2>Rembourser un participant</h2>
                <form>
                    <label for="participant">Receveur</label>
                    <select id="participant" name="participant">
                        <option value="0" selected>Basile Blaise</option>
                        <option value="1">Morgane Garnier</option>
                        <option value="2">Ludivine Bourdillon</option>
                        <option value="3">Godefroy Fabre</option>
                    </select> <br />
                    <label >Montant </label>
                    <input id="montant" name="montant" type="number" required placeholder="20"><br/>
                    <input type="submit" name="soumettre" value="Valider">
                </form>
            </section>
            <section>
                <h2>Historique des versements</h2>
                <table>
                    <tr>
                        <th>Envoyeur</th>
                        <th>Receveur</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                        <td>Confirmé</td>
                        <td><a href="#">Infirmer</a></td>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                        <td>En attente</td>
                        <td><a href="#">Confirmer</a></td>
                    </tr>
                    <tr>
                        <td>Basile Blaise</td>
                        <td>Ludivine Bourdillon</td>
                        <td>122.05€</td>
                        <td>Confirmé</td>
                        <td><a href="#">Infirmer</a></td>
                    </tr>
                </table>
            </section>
            <section>
                <h2>
                    Désolder le groupe
                    <form action="group.php">
                        <input type="submit" name="soumettre" value="Désolder le groupe">
                    </form>
                </h2>

            </section>
        </main>

<?php require 'inc/footer.inc.php'; ?>
