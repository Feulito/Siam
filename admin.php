<?php
require_once("config.php");
session_start();
include_once('base/Bdd.php');
include_once('php/inc/acount.inc');
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) header('Location: index.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Siam</title>
        <?php include('php/inc/head.inc'); ?>    
    </head>
    <body>
        <?php include('php/inc/header.inc') ?>

        <div class="container">
            <div class="row">
                <h1>Jeu de plateau : Siam</h1>
            </div>
            <div class="row">
                <h2>Administration</h2>
                <form method="POST">
                    <div class="row">
                        <label for="pseudo" class="col-md-6">Pseudo admin :</label>
                        <input type="text" class="col-md-2" placeholder="Pseudo" name="pseudoadmin" id="pseudo">
                    </div>
                    <div class="row">
                        <label for="password" class="col-md-6">Mot de passe :</label>
                        <input type="password" class="col-md-2" placeholder="Mot de passe" name="passwordadmin" id="password">
                    </div>
                    <div class="row">
                        <input type="submit" value="Se connecter">
                    </div>
                </form>
            </div>

            <!--
                Ici est le commentaire pour inscrire un administrateur.
                Décommenter permet d'afficher le formulaire
                Cela permet d'inscrire un administrateur manuellement afin d'avoir un mot de passe crypté dans la base
            -->
            <div class="row">
                <h2>Inscription</h2>
                <form method="POST">
                    <div class="row">
                        <label for="i-pseudo" class="col-md-6">Pseudo du joueur :</label>
                        <input type="text" class="col-md-2" placeholder="Pseudo" name="i-pseudoadmin" id="i-pseudo">
                    </div>
                    <div class="row">
                        <label for="i-password" class="col-md-6">Mot de passe :</label>
                        <input type="password" class="col-md-2" placeholder="Mot de passe" name="i-passwordadmin" id="i-password">
                    </div>
                    <div class="row">
                        <input type="submit" value="S'inscrire">
                    </div>
                </form>
            </div>
            
        </div>
        <?php include('php/inc/footer.inc'); ?>
    </body>
</html>