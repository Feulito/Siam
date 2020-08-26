<?php
require_once("config.php");
session_start();
include_once('base/Bdd.php');
include_once('php/inc/acount.inc');
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

            <?php if(isset($_SESSION['pseudo'])) { ?>
            <div class="row">
                <p>Vous êtes connecté sur le compte <?php echo $_SESSION['pseudo'] ?></p>
                <?php
                if (isset($chgtmdp) && $chgtmdp) {?>
                    <p>Votre mot de passe a bien été changé</p>
                <?php
                } else if (isset($chgtmdp)) {?>
                    <p>Votre ancien mot de passe n'est pas bon</p>
                <?php
                }
                ?>
            </div>
            <div class="row">
                <h2>Rappel des règles du jeu :</h2>
                <h3>But du jeu :</h3>
                <p>Après avoir choisi votre animal, être le premier à sortir
                    une région montagneuse (bloc de rochers) à l'extérieur du plateau.
                </p>
                <h3>Comment jouer :</h3>
                <p>
                    Le joueur qui crée la partie est éléphant, celui qui la rejoint est rhinocéros.<br/>
                    Chaque joueur joue à tour de rôle.<br/>
                    Au début du jeu les animaux sont à l'extérieur du plateau (réserve), et les trois
                    blocs de rochers sont au centre du plateau.<br/>
                    Le joueur éléphant commence à jouer.<br/>
                    Les joueur ne pourront jouer à chaque tour de jeu qu'un seul de leur animal, et ne faire
                    qu'une de ces 5 actions :
                    <ul>
                        <li>- Entrer un animal sur le plateau</li>
                        <li>- Se déplacer</li>
                        <li>- Changer l'orientation de son animal</li>
                        <li>- Sortir un de ses animaux du plateau</li>
                        <li>- Se déplacer en poussant d'autres pièces</li>
                    </ul>
                </p>
            </div>
            <div class="row">
                <div class="row"><h2>Changer son mot de passe utilisateur</h2></div>
                <form method="post">
                    <div class="row">
                        <label for="oldpw" class="col-md-6">Ancien mot de passe :</label>
                        <input type="password" name="oldpw" id="oldpw" class="col-md-2" placeholder="Ancien mot de passe">
                    </div>
                    <div class="row">
                        <label for="newpw" class="col-md-6">Nouveau mot de passe :</label>
                        <input type="password" name="newpw" id="newpw" class="col-md-2" placeholder="Nouveau mot de passe">
                    </div>
                    <div class="row">
                        <input type="submit" value="Changer de mot de passe">
                    </div>
                </form>
            </div>
            <?php }
            else { ?>

            <div class="row">
                <h2>Connexion</h2>
                <form method="POST">
                    <div class="row">
                        <label for="pseudo" class="col-md-6">Pseudo du joueur :</label>
                        <input type="text" class="col-md-2" placeholder="Pseudo" name="pseudo" id="pseudo">
                    </div>
                    <div class="row">
                        <label for="password" class="col-md-6">Mot de passe :</label>
                        <input type="password" class="col-md-2" placeholder="Mot de passe" name="password" id="password">
                    </div>
                    <div class="row">
                        <input type="submit" value="Se connecter">
                    </div>
                </form>
            </div>

            <div class="row">
                <h2>Inscription</h2>
                <form method="POST">
                    <div class="row">
                        <label for="i-pseudo" class="col-md-6">Pseudo du joueur :</label>
                        <input type="text" class="col-md-2" placeholder="Pseudo" name="i-pseudo" id="i-pseudo">
                    </div>
                    <div class="row">
                        <label for="i-password" class="col-md-6">Mot de passe :</label>
                        <input type="password" class="col-md-2" placeholder="Mot de passe" name="i-password" id="i-password">
                    </div>
                    <div class="row">
                        <input type="submit" value="S'inscrire">
                    </div>
                </form>
            </div>
            <?php } ?>

            <?php
                if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {?>
                    <div class="row">
                        <div class="row"><h2>Changer son mot de passe administrateur</h2></div>
                        <form method="POST">
                            <div class="row">
                                <label for="oldpwadmin" class="col-md-6">Ancien mot de passe :</label>
                                <input type="password" name="oldpwadmin" id="oldpwadmin" class="col-md-2" placeholder="Ancien mot de passe">
                            </div>
                            <div class="row">
                                <label for="newpwadmin" class="col-md-6">Nouveau mot de passe :</label>
                                <input type="password" name="newpwadmin" id="newpwadmin" class="col-md-2" placeholder="Nouveau mot de passe">
                            </div>
                            <div class="row">
                                <input type="submit" value="Changer de mot de passe">
                            </div>
                        </form>
                    </div>
                <?php
                }
            ?>
        </div>
        <?php include('php/inc/footer.inc'); ?>
    </body>
</html>