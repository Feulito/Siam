<header>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-lg-12">
                <a href='index.php'>Accueil</a>
                <?php
                if (isset($_SESSION['id'])) {?>
                <a href='listegameplayer.php'>Mes parties</a>
                <a href='creerpartie.php'>Créer une partie</a>
                <a href="listeparties.php">Rejoindre une partie</a>
                <a href='index.php?d'>Se déconnecter</a>
                <?php }
                if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {?>
                    <a href="index.php?da">Se déconnecter de l'administration</a>
                <?php
                } else {
                ?>
                    <a href="admin.php">Administration</a>
                <?php
                }
                ?>
            </nav>
        </div>
    </div>
</header>