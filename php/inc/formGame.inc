<div id='game-control'>
    <h2 id='nbr'>Reserve : </h2>
    <div id="reserve"></div>
    <button id='rotation'><i class="fas fa-sync-alt"></i></button>
    <button id='retirer'>Retirer</button>
    <div id="finbouton"></div>
    <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {?>
    <button id='chgtype'>Devenir Rhinoceros</button>
    <?php } ?>
    <h2 id="winner"></h2>
    <div id="Info">
        <p>Pour faire entrer un pion : Cliquez sur la réserve, choisissez son orientation, cliquez sur une case au bord du plateau
        Orienter son pion vers la droite rendra possible une insertion en poussée vers la droite.
        Pour une insertion en poussée vers le bas, orientez votre pion vers le bas.
        Pour une insertion en poussée vers le haut, orientez votre pion vers le haut.
        Pour une insertion en poussée vers la gauche, orientez votre pion vers la gauche.
        </p>
        <p>Pour faire bouger un pion : Cliquez sur le pion à bouger, cliquez sur une case à proximité du pion pour le faire bouger. Une poussée sera effectuée si c'est possible</p>
        <p>Pour changer l'orientation de votre pion, selectionnez-le et faite le rouler avec le bouton sous la réserve. Quand vous avez trouvé votre orientation, cliquez sur le bouton "fin de tour" qui apparaît.</p>
    </div>
</div>