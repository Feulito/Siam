<?php
    if (!isset($_POST['id_game'])) {
        header('Location: listeparties.php');
    }
    require_once("../../config.php");
    include_once("../../base/Bdd.php");
    include_once('../objects/Game.php');
    $bd = new Bdd();

    $g = $bd->getGame($_POST['id_game']);

    $game = new Game($g['id_game'], $g['name'], $g['round'], $g['nbPlayers']);
    echo $game->encode();
?>