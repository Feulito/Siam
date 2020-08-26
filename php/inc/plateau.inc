<div class="container">
    <div class="row">
        <img
        src="images/logo_sia.gif"
        id="logo"
        alt="Un logo écrit SIAM en majuscule de couleur orangée avec un dégradé interne blanc">
    </div>
    <div class="row">
        <h1 id='gamename'></h1>
        <h2 id="round"><?php
            if (isset($round)) {
                if ($round%2 == 0) echo "C'est le tour de l'éléphant";
                else echo "C'est le tour du rhinocéros";
            }
        ?></h2>
    </div>
    <div class="row gameframe">
        <div id="boardcontainer">
            <div class="plateau col-lg-6" id="board"></div>
        </div>
        <div class="col-lg-6" id="form">
            <?php include("php/inc/formGame.inc");?>
        </div>
    </div>
</div>