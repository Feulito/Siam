<?php
if (!isset($id)) {
    header('Location: listeparties.php');
}

require_once("config.php");
include_once('php/inc/verifSignedIn.inc');
include_once('base/Bdd.php');

$bd = new Bdd();

$players = $bd->getPlayersOfGame($id);

$p1 = $players[0]['id_user'];
if (sizeof($players) >= 2) $p2 = $players[1]['id_user'];
else if ($_SESSION['id'] != $p1) {
    $bd->addPlayerToGame($_SESSION['id'], $id);
}

if (($_SESSION['id'] != $p1) && (isset($p2) && $_SESSION['id'] != $p2)) {
    header('Location: listeparties.php');
}
?>