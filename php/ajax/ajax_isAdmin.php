<?php
session_start();

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) echo "true";
else if (isset($_SESSION['isAdmin'])) echo "false";
?>