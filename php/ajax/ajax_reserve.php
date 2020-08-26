<?php
    session_start();
    require_once("../../config.php");
    include_once('../inc/verifSignedIn.inc');
    include_once('../../base/Bdd.php');

    if(isset($_POST['id_game'])) {
        $bdd = new Bdd();

        $p = $bdd->getPlayer($_SESSION['id'], $_POST['id_game']);

        echo $p->encode();
    }
?>