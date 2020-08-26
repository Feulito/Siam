<?php
session_start();
require_once("../../config.php");
include_once('../inc/verifSignedIn.inc');
include_once('../../base/Bdd.php');

if(isset($_POST['id_game']) && isset($_POST['dernier'])) {
    $bdd = new Bdd();

    $p = $bdd->updateDernier($_POST['id_game'], $_POST['dernier']);
}
?>
