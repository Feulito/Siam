<?php
    session_start();
    require_once("config.php");
    include('php/inc/verifSignedIn.inc');

    include_once('base/Bdd.php');
    $bd = new Bdd();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Mes parties</title>
        <?php include("php/inc/head.inc"); ?>
        <link href="css/plateau.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include('php/inc/header.inc'); ?>

        <div class="container text-center">
            <div class="row">
                <h1>Liste de mes parties</h1>
            </div>
            <div class="row">
                <div class="list-party col-md-12">
                    <?php
                        $games = $bd->getPlayerGames($_SESSION['id']);
                        $bd->printPlayerGames($games);
                    ?>
                </div>
            </div>
        </div>
        <?php include('php/inc/footer.inc'); ?>
    </body>
</html>