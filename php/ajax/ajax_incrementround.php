<?php
    if (!isset($_POST['id_game'])) {
        header('Location: listeparties.php');
    }
    require_once("../../config.php");
    include_once("../../base/Bdd.php");
    include_once('../objects/Game.php');
    $bd = new Bdd();

    $bd->incrementRound($_POST['id_game']);
?>