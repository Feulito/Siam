<?php
    session_start();
    include_once("../objects/BoardGame.php");
    require_once("../../config.php");
    include_once('../../base/Bdd.php');
    if (isset($_POST['plateau']) && isset($_POST['id_game']) && isset($_POST['player']) && isset($_SESSION['id'])) {
        $plat = new BoardGame();

        $plat = $plat->decode($_POST['plateau']);
        $bdd = new Bdd();
        $bdd->saveBoard($_POST['id_game'], $plat->getBoard());

        $player = new Player();
        $player = $player->decode($_POST['player']);
        echo $bdd->savePlayer($_POST['id_game'], $_SESSION['id'], $player);
    }
?>