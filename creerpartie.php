<?php
    session_start();
    require_once("config.php");
    include('php/inc/verifSignedIn.inc');
    include_once('base/Bdd.php');

    if (isset($_POST['partyname'])) {
        $bdd->createGame($_POST['partyname']);
        header('Location: listegameplayer.php');
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Créer une partie - Siam</title>
    <?php include("php/inc/head.inc"); ?>
</head>
<body>
    <?php include('php/inc/header.inc'); ?>

    <div class="container">
        <div class="row">
            <h1>Créer une partie :</h1>
            <form method="post">
                <div class="row">
                    <label for="name" class="col-md-5">Nom de la partie :</label>
                    <input type="text" class="col-md-3" name="partyname" id="name" placeholder="Nom de votre partie">
                </div>
                <div class="row">
                    <input type="submit" value="Créer la partie">
                </div>
            </form>
        </div>
    </div>

    <?php include('php/inc/footer.inc'); ?>
</body>
</html>