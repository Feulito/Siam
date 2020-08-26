<?php
include_once('base/Bdd.php');
    if (isset($_GET['d'])) {
        session_destroy();
        header('Location: index.php');
    } else if (isset($_GET['da'])) {
        unset($_SESSION['idAdmin']);
        $_SESSION['isAdmin'] = false;
        unset($_SESSION['pseudoAdmin']);
        header('Location: index.php');
    }
    else if (isset($bdd) && isset($_POST['i-pseudo']) && isset($_POST['i-password'])) {
        $bdd->registerUser($_POST['i-pseudo'], $_POST['i-password']);
    }
    else if (isset($bdd) && isset($_POST['pseudo']) && isset($_POST['password'])) {
        $bdd->connectUser($_POST['pseudo'], $_POST['password']);
    }
    else if (isset($bdd) && isset($_POST['pseudoadmin']) && isset($_POST['passwordadmin'])) {
        $bdd->connectAdmin($_POST['pseudoadmin'], $_POST['passwordadmin']);
    }
    else if (isset($bdd) && isset($_POST['i-pseudoadmin']) && isset($_POST['i-passwordadmin'])) {
        $bdd->registerAdmin($_POST['i-pseudoadmin'], $_POST['i-passwordadmin']);
    } 
    else if (isset($_SESSION['id']) && isset($bdd) && isset($_POST['oldpw']) && isset($_POST['newpw'])) {
        $chgtmdp = $bdd->changeUserPassword($_SESSION['id'], $_POST['oldpw'], $_POST['newpw']);
    }
    else if (isset($_SESSION['idAdmin']) && isset($bdd) && isset($_POST['oldpwadmin']) && isset($_POST['newpwadmin'])) {
        $chgtmdp = $bdd->changeAdminPassword($_SESSION['idAdmin'], $_POST['oldpwadmin'], $_POST['newpwadmin']);
    }
?>