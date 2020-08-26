const IDGAME = window.document.getElementById("id_game").value;
const DIM = 5;
// nord - est - sud - ouest
const ORIENTATION_ELEPHANT = ["10.gif", "11.gif", "12.gif", "13.gif"];
const ORIENTATION_RHINO = ["20.gif", "21.gif", "22.gif", "23.gif"];

jQuery(document).ready(function($) {
    let isAdmin;
    let dernier;
    let rotation = 0;
    let rouler = 0;
    let clic = 0;
    let pion = null;
    let res = false;
    let round = 0;
    let aroule = false;

    function verifTour(type) {
        return (round%2 == 0 && type == "elephant") || (round%2 != 0 && type == "rhinoceros") || isAdmin;
    }

    let url = './php/ajax/ajax_accessgame.php';
    request = $.post(url, {id_game: IDGAME});
    request.done(function(data) {
        game = JSON.parse(data);
        $("#gamename").html(game.name);
        round = parseInt(game.round);
    });

    url = './php/ajax/ajax_getdernier.php';
    request = $.post(url, {id_game: IDGAME});
    request.done(function(data) {
        dernier = data;
    });

    url = './php/ajax/ajax_isAdmin.php';
    request = $.post(url);
    request.done(function(data) {
        isAdmin = (data == "true") ? true : false;
    });

    url = './php/ajax/ajax_initboard.php';
    request = $.post(url, {id_game: IDGAME});
    request.done(function(data) {
        tab = new Array(DIM);
        for (let i = 0; i < DIM; i++) {
            tab[i] = new Array(DIM);
        }
        let plateau = JSON.parse(data);
        for (let i = 0; i < DIM; i++) {
            for (let j = 0; j < DIM; j++) {
                tab[i][j] = plateau.board[i][j];
            }
        }
        // Fonction pour attribuer le type du pion à la case (i,j)
        // Obligation de le faire dans la fonction request.done, sinon les variables ne sont pas attribuées
        function attributeSpecies(i , j, btn) {
            if (Number.isInteger(i) && Number.isInteger(j)) {
                if (tab[i][j] != null && tab[i][j].type == "rocher") btn.setAttribute("class", "gameBtn rocher");
                else if (tab[i][j] != null) {
                    btn.setAttribute("class", "gameBtn");

                    switch(tab[i][j].type) {
                        case "elephant" :
                            image = "background-image: url(images/" + ORIENTATION_ELEPHANT[tab[i][j]['orientation']] + ");";
                            break;
                        default :
                            image = "background-image: url(images/" + ORIENTATION_RHINO[tab[i][j]['orientation']] + ");";
                            break;
                    }

                    btn.setAttribute("style", "top: " + btn.style.top + ";" + image)
                }
            }
        }
        
        // Fonction pour initialiser le plateau
        // Obligation de le faire dans la fonction request.done, sinon les variables ne sont pas attribuées
        function initGame(player) {
            let board = window.document.getElementById("board");
            let id = 0;
            let top = 19;
            for (let i = 0; i < DIM; i++) {
                for (let j = 0; j < DIM; j++) {
                    let btn = window.document.createElement("div");
                    btn.setAttribute("style", "top: " + top + "px;");
                    btn.setAttribute("id", "" + i + j);
                    btn.setAttribute("class", "gameBtn");
                    attributeSpecies(i, j, btn);
                    id++;

                    board.appendChild(btn);
                    if (!verifTour(player.type)) {
                        $("#"+i+j).prop("onclick", null).off("click");
                    }
                }
            }
            if (dernier != "") {
                let dbtn = document.getElementById(dernier);
                dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid red; background-image:" + dbtn.style.backgroundImage + ";");
            }
        }

        // On récupère les info sur le joueur
        url = './php/ajax/ajax_reserve.php';
        request = $.post(url, {id_game: IDGAME});
        request.done(function(data) {
            let player;
            if (!isAdmin) {
                player = JSON.parse(data);
            } else {
                player = {
                    'name' : "admin",
                    'type' : "elephant",
                    'reserve' : "100"
                }

                $('#chgtype').click(function(){
                    if (player.type == "elephant") {
                        player.type = "rhinoceros";
                        image.setAttribute('src', 'images/' + ORIENTATION_RHINO[rotation]);
                        $('#chgtype').text("Devenir éléphant");
                    }
                    else {
                        player.type = "elephant";
                        image.setAttribute('src', 'images/' + ORIENTATION_ELEPHANT[rotation]);
                        $('#chgtype').text("Devenir rhinocéros");
                    }
                    image.setAttribute('id', 'imgreserve');
                    $('#reserve').html(image);
                });
            }
            initGame(player);
            let image = document.createElement('img');
            if (player.type == "elephant") image.setAttribute('src', 'images/' + ORIENTATION_ELEPHANT[rotation]);
            else image.setAttribute('src', 'images/' + ORIENTATION_RHINO[rotation]);
            image.setAttribute('id', 'imgreserve');
            $('#nbr').text($('#nbr').text() + player.reserve);
            $('#reserve').html(image);
            $('#reserve').click(function() {
                if (player.reserve > 0) {
                    res = true;
                    clic++;
                } else alert("Vous n'avez plus d'animaux en réserve !");
            });

            $('#rotation').click(function() {
                if (pion != null) {
                    if (typeof(pion['orientation']) == "string") pion['orientation'] = parseInt(pion['orientation']);
                    rouler = (pion['orientation']+1)%4;

                    pion['orientation'] = rouler;
                    let x = pion['x'];
                    let y = pion['y'];
                    tab[x][y]['orientation'] = rouler;
                    let btn = document.getElementById("" + x + y);

                    switch (pion['type']) {
                        case "elephant" :
                            btn.setAttribute('style','top:' + btn.style.top +";background-image: url(images/" + ORIENTATION_ELEPHANT[rouler] + ");border: 1px solid red;");
                            break;
                        default :
                            btn.setAttribute('style','top:' + btn.style.top +";background-image: url(images/" + ORIENTATION_RHINO[rouler] + ");border: 1px solid red;");
                            break;
                    }

                    if (!aroule) {
                        let fin = document.getElementById('finbouton');
                        let finbtn = document.createElement('button');
                        finbtn.setAttribute('id', 'fin');
                        finbtn.innerText = "Fin du tour"

                        fin.appendChild(finbtn);

                        $('#fin').click(function() {
                            if (verifTour(player.type)) {
                                round++;
                                if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                                else $('#round').text("C'est au tour du rhinocéros");

                                url = './php/ajax/ajax_incrementround.php';
                                $.post(url,{id_game : IDGAME});

                                dernier = ""+x+y;

                                url = './php/ajax/ajax_updatedernier.php';
                                $.post(url, {id_game : IDGAME, dernier : dernier});

                                url = './php/ajax/ajax_saveboard.php';
                                requete = $.post(url, {plateau: JSON.stringify(tab), id_game: IDGAME, player: JSON.stringify(player)});
                                for (a = 0; a < tab.length; a++) {
                                    for (b = 0; b < tab[a].length; b++) {
                                        if (tab[a][b] !== undefined) {
                                            let elem = document.getElementById("" + a + b);
                                            if (tab[a][b] == null) {
                                                elem.setAttribute('style', "top:" + elem.style.top + ";");
                                                elem.setAttribute('class', 'gameBtn');
                                            } else {
                                                let image;
                                                switch (tab[a][b]['type']) {
                                                    case "elephant" :
                                                        image = "background-image: url(images/" + ORIENTATION_ELEPHANT[tab[a][b]['orientation']] + ");";
                                                        break;
                                                    case "rhinoceros" :
                                                        image = "background-image: url(images/" + ORIENTATION_RHINO[tab[a][b]['orientation']] + ");";
                                                        break;
                                                    default :
                                                        image = "background-image: url(images/rocher.gif);";
                                                }
                                                elem.setAttribute("style", "top:" + elem.style.top + ";" + image);
                                            }
                                        }
                                    }
                                }
                                fin.removeChild(finbtn);
                            }
                            pion = null;
                        });
                        aroule = true;
                    }
                } else {
                    rotation = (rotation+1)%4;
        
                    if (player.type == "elephant") $('#imgreserve').attr('src', 'images/' + ORIENTATION_ELEPHANT[rotation]);
                    else $('#imgreserve').attr('src', 'images/' + ORIENTATION_RHINO[rotation]);
                }
            });

            $('#retirer').click(function(){
                if (pion != null && player.type == pion['type']) {
                    let x = (typeof(pion['x']) == "string") ? parseInt(pion['x']) : pion['x'];
                    let y = (typeof(pion['y']) == "string") ? parseInt(pion['y']) : pion['y'];
                    tab[x][y] = null;

                    player.reserve++;
                    $('#nbr').text("Reserve : " + player.reserve);

                    url = './php/ajax/ajax_saveboard.php';
                    requete = $.post(url, {plateau: JSON.stringify(tab), id_game: IDGAME, player: JSON.stringify(player)});
                    for (a = 0; a < tab.length; a++) {
                        for (b = 0; b < tab[a].length; b++) {
                            if (tab[a][b] !== undefined) {
                                let elem = document.getElementById("" + a + b);
                                if (tab[a][b] == null) {
                                    elem.setAttribute('style', "top:" + elem.style.top + ";");
                                } else {
                                    let image;
                                    switch (tab[a][b]['type']) {
                                        case "elephant" :
                                            image = "background-image: url(images/" + ORIENTATION_ELEPHANT[tab[a][b]['orientation']] + ");";
                                            break;
                                        case "rhinoceros" :
                                            image = "background-image: url(images/" + ORIENTATION_RHINO[tab[a][b]['orientation']] + ");";
                                            break;
                                        default :
                                            image = "background-image: url(images/rocher.gif);";
                                    }
                                    elem.setAttribute("style", "top:" + elem.style.top + ";" + image);
                                }
                            }
                        }
                    }
                    clic = 0;
                    pion = null;
                }
            });

            $('.gameBtn').click(function() {    
                function checkCoords(x, y){
                    return 0<=x && x<5 && 0<=y && y<5;
                }
        
                function isEmpty(x, y){
                    return tab[x][y] == null;
                }
        
                function isRhino(x, y){
                    return tab[x][y] !== undefined && tab[x][y] != null && tab[x][y]["type"] == "rhinoceros"
                }
            
                function isEleph(x, y){
                    return tab[x][y] !== undefined && tab[x][y] != null && tab[x][y]["type"] == "elephant";
                }
        
                function isAnimal(x, y){
                    return isEleph(x, y) || isRhino(x, y);
                }
        
                function isRock(x, y){
                    return tab[x][y]["type"] == "rocher";
                }
        
                function getOpposite(animal){
                    switch(animal["orientation"]){
                        case 0:
                            return 2;
                        case 2:
                            return 0;
                        case 1:
                            return 3;
                        case 3:
                            return 1;
                    }
                }
        
                function isOppositeAnimal(animal1, animal2){
                    return animal1["orientation"] == getOpposite(animal2);    
                }
        
                function isPushers(animal1, animal2){
                    return animal1["orientation"] == animal2["orientation"];
                }

                function makeGoodPush(animal, posX, posY, orientation, res){
                    // On cast si nécessaire car il est possible que animal['x'] devienne un string avec le decodage/encodage
                    let x = (typeof(animal['x']) == "string") ? parseInt(animal['x']) : animal['x'];
                    let y = (typeof(animal['y']) == "string") ? parseInt(animal['y']) : animal['y'];
                    let or = (typeof(orientation) == "string") ? orientation : "" + orientation;
                    switch(or){
                        case "0":
                            if (!(res) && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "2" || tab[posX][posY]['type'] == "rocher")){
                                if (posX <= x && posY == y){
                                    return makePushUp(animal, posX, posY);
                                }
                            } else if (res && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "2" || tab[posX][posY]['type'] == "rocher") && posX == 4) {
                                if (posX <= x && posY == y){
                                    return makePushUp(animal, posX, posY);
                                }
                            }
                            break;
                        case "2":
                            if (!(res) && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "0" || tab[posX][posY]['type'] == "rocher")) {
                                if (posX >= x && posY == y)
                                    return makePushDown(animal, posX, posY);
                            } else if (res && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "0" || tab[posX][posY]['type'] == "rocher") && posX == 0) {
                                if (posX <= x && posY == y){
                                    return makePushDown(animal, posX, posY);
                                }
                            }
                            break;
                        case "1":
                            if (!(res) && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "3" || tab[posX][posY]['type'] == "rocher")) {
                                if (posX == x && posY >= y)
                                    return makePushRight(animal, posX, posY);
                            } else if (res && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "3" || tab[posX][posY]['type'] == "rocher") && posY == 0) {
                                if (posX <= x && posY == y){
                                    return makePushRight(animal, posX, posY);
                                }
                            }
                            break;
                        case "3":
                            if (!(res) && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "1" || tab[posX][posY]['type'] == "rocher")) {
                                if (posX == x && posY <= y)
                                    return makePushLeft(animal, posX, posY);
                            } else if (res && tab[posX][posY] !== undefined && tab[posX][posY] != null && (tab[posX][posY]['orientation'] != "1" || tab[posX][posY]['type'] == "rocher") && posY == 4) {
                                if (posX <= x && posY == y){
                                    return makePushLeft(animal, posX, posY);
                                }
                            }
                            break;
                        default :
                            break;
                    }
                }
                /*pousse a droite est retourne si elle s'est déroulée correctement*/
                function makePushRight(animal, x, y){
                    if (!checkCoords(x, y)) {
                        return false;
                    }
                    let number_pushers = 1;
                    let number_pushable_rocks = 1;
                    for (let i = y; i<5; i++){
                        if (isEmpty(x, i)) {
                            break;
                        }
                        else if (isAnimal(x, i)){
                            if (isOppositeAnimal(animal, tab[x][i])){
                                number_pushable_rocks--;
                                number_pushers--;
                            }
                            else if (isPushers(animal, tab[x][i])){
                                number_pushable_rocks++;
                                number_pushers++;
                            }
                        }
                        else{
                            number_pushable_rocks--;
                        }
                        if (number_pushers==0||number_pushable_rocks<0) {
                            return false;
                        }
                    }
                    pushRight(animal, x, y);
                    return true;
                }

                function makePushLeft(animal, x, y){
                    if (!checkCoords(x, y)) return false;
                    let number_pushers = 1;
                    let number_pushable_rocks = 1;
                    for (let i = y; i>=0; i--){
                        if (isEmpty(x, i)) break;
                        else if (isAnimal(x, i)){
                            if (isOppositeAnimal(animal, tab[x][i])){
                                number_pushable_rocks--;
                                number_pushers--;
                            }
                            else if (isPushers(animal, tab[x][i])){
                                number_pushable_rocks++;
                                number_pushers++;
                            }
                        }
                        else{
                            number_pushable_rocks--;
                        }
                        if (number_pushers==0||number_pushable_rocks<0)
                            return false;
                    }
                    pushLeft(animal, x, y);
                    return true;
                }
                
                function makePushUp(animal, x, y){
                    if (!checkCoords(x, y) && animal['orientation'] == "0" && tab[x][y] != null && (tab[x][y]['type'] == "rocher" || tab[x][y]['orientation'] != "2")){
                        return false;
                    }
                    let number_pushers = 1;
                    let number_pushable_rocks = 1;
                    for (let i = x; i>=0; i--){
                        if (isEmpty(i, y)) break;
                        else if (isAnimal(i, y)){
                            if (isOppositeAnimal(animal, tab[i][y])){
                                number_pushable_rocks--;
                                number_pushers--;
                            }
                            else if (isPushers(animal, tab[i][y])){
                                number_pushable_rocks++;
                                number_pushers++;
                            }
                        }
                        else{
                            number_pushable_rocks--;
                        }
                        if (number_pushers==0||number_pushable_rocks<0){
                            return false;
                            
                        }
                    }
                    pushUp(animal, x, y);
                    return true;
                }
                
                function makePushDown(animal, x, y){
                    if (!checkCoords(x, y)) {
                        return false;
                    }
                    let number_pushers = 1;
                    let number_pushable_rocks = 1;
                    for (let i = x; i<5; i++){
                        if (isEmpty(i, y)) {
                            break;
                        }
                        else if (isAnimal(i, y)){
                            if (isOppositeAnimal(animal, tab[i][y])){
                                number_pushable_rocks--;
                                number_pushers--;
                            }
                            else if (isPushers(animal, tab[i][y])){
                                number_pushable_rocks++;
                                number_pushers++;
                            }
                        }
                        else{
                            number_pushable_rocks--;
                        }
                        if (number_pushers==0||number_pushable_rocks<0) {
                            return false;
                        }
                    }
                    pushDown(animal, x, y);
                    return true;
                }
                
                function updateJs(x, y){
                    let btn = document.getElementById("" + x + y);
                    btn.setAttribute("style", "top:" + btn.style.top + ";");
                    if (isAnimal(x, y))
                        btn.setAttribute("class", "gameBtn");
                    else if (isRock(x, y))
                        btn.setAttribute("class", "Rock");
                }
        
                function pushRight(animal, x, y){
                    let i=y;
                    while (i<5 && (!isEmpty(x, i))){
                        setCoordsRight(animal, tab[x][i], x, i+1);
                        i++;
                    } 
                    if (i==5) i--;
                    while (i>y){
                        tab[x][i] = tab[x][i-1];
                        tab[x][i]["y"]++;
                        i--;
                    }
                }
                
                function pushLeft(animal, x, y){
                    let i=y;
                    while (i>=0 && (!isEmpty(x, i))){
                        setCoordsLeft(animal, tab[x][i], x, i-1);
                        i--;
                    } 
                    if (i<0) i=1;
                    while (i<y){
                        tab[x][i] = tab[x][i+1];
                        tab[x][i]["y"]--;
                        i++;
                    }
                }
                
                function pushUp(animal, x, y){
                    let i=x;
                    while (i>=0 && (!isEmpty(i, y))){
                        setCoordsUp(animal, tab[i][y], i-1);
                        i--;
                    } 
                    if (i<0) i=1;
                    while (i<x){
                        tab[i][y] = tab[i+1][y];
                        tab[i][y]["x"]--;
                        i++;
                    }
                }
                
                function pushDown(animal, x, y){
                    let i=x;
                    while (i<5 && (!isEmpty(i, y))){
                        setCoordsDown(animal, tab[i][y], i+1);
                        i++;
                    } 
                    if (i==5) i--;
                    while (i>x){
                        tab[i][y] = tab[i-1][y];
                        tab[i][y]["x"]++;
                        i--;
                    }
                }

                function setCoordsRight(animal, pawns, newY){
                    url = './php/ajax/ajax_updatePosition.php';
                    if (pawns["y"] == 4){
                        if (isRock(pawns["x"], pawns["y"])){
                            foundWinnerLeft(animal, pawns["x"], 4); 
                        }
                        else{
                            request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY : pawns["y"], type: pawns["type"], delete:true});
                        }
                    }
                    else {
                        request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY: pawns["y"], type : pawns["type"], x: pawns["x"], y: newY});
                        
                    }
                }
                
                function setCoordsLeft(animal, pawns, newY){
                    url = './php/ajax/ajax_updatePosition.php';
                    if (pawns['y'] == 0){
                        if (isRock(pawns["x"], pawns["y"])){
                            foundWinnerRight(animal, pawns["x"], 0); 
                        }
                        else{
                            request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY : pawns["y"], type: pawns["type"], delete:true});
                        }
                    }
                    else {
                        request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY: pawns["y"], type : pawns["type"], x: pawns["x"], y: newY});
                        
                    }   
                }
                
                function setCoordsUp(animal, pawns, newX){
                    url = './php/ajax/ajax_updatePosition.php';
                    if (pawns['x'] == 0){
                        if (isRock(pawns["x"], pawns["y"])){
                            foundWinnerDown(animal, 0, pawns["y"]); 
                        }
                        else{
                            request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY : pawns["y"], type: pawns["type"], delete:true});
                        }
                    }
                    else {
                        request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY: pawns["y"], type : pawns["type"], x: newX, y: pawns["y"]});
                    }    
                }
                
                function setCoordsDown(animal, pawns, newX){
                    url = './php/ajax/ajax_updatePosition.php';
                    if (pawns['x'] == 4){
                        if (isRock(pawns["x"], pawns["y"])){
                            foundWinnerUp(animal, 4, pawns["y"]); 
                        }
                        else{
                            request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY : pawns["y"], type: pawns["type"], delete:true});
                        }
                    }
                    else {
                        request = $.post(url, {id_game: IDGAME, oldX : pawns["x"], oldY: pawns["y"], type : pawns["type"], x: newX, y: pawns["y"]});
                    }    
                }
                
                function foundWinnerLeft(animal, x, y){
                    i = y-1;
                    while (tab[x][i] !== undefined && !isAnimal(x, i) && (!isPushers(animal, tab[x][i]))){
                        i--;
                    }
                    url = './php/ajax/ajax_updateWinner.php';
                    request = $.post(url, {id_game: IDGAME, type: tab[x][i]["type"], winner:true});
                    url = './php/ajax/ajax_winner.php';
                    request = $.post(url, {id_game: IDGAME});
                    request.done(function(data) {
                        $('#winner').text('Le joueur ' + data + ' a gagné !');
                        for (a = 0; a < tab.length; a++) {
                            for (b = 0; b < tab[a].length; b++) {
                                $("#"+a+b).prop("onclick", null).off("click");
                            }
                        }
                    });
                }
                
                function foundWinnerRight(animal, x, y){
                    i = y+1;
                    while (tab[x][i] !== undefined && !isAnimal(x,i) && (!isPushers(animal, tab[x][i]))){
                        i++;
                    }
                    url = './php/ajax/ajax_updateWinner.php';
                    request = $.post(url, {id_game: IDGAME, type: tab[x][i]["type"], winner:true});
                    url = './php/ajax/ajax_winner.php';
                    request = $.post(url, {id_game: IDGAME});
                    request.done(function(data) {
                        $('#winner').text('Le joueur ' + data + ' a gagné !');
                        for (a = 0; a < tab.length; a++) {
                            for (b = 0; b < tab[a].length; b++) {
                                $("#"+a+b).prop("onclick", null).off("click");
                            }
                        }
                    });
                }
                
                function foundWinnerDown(animal, x, y){
                    i = x+1;
                    while (tab[i][y] !== undefined && !isAnimal(i, y) && (!isPushers(animal, tab[i][y]))){
                        i++;
                    }
                    url = './php/ajax/ajax_updateWinner.php';
                    request = $.post(url, {id_game: IDGAME, type: tab[i][y]["type"], winner:true});
                    url = './php/ajax/ajax_winner.php';
                    request = $.post(url, {id_game: IDGAME});
                    request.done(function(data) {
                        $('#winner').text('Le joueur ' + data + ' a gagné !');
                    });
                    for (a = 0; a < tab.length; a++) {
                        for (b = 0; b < tab[a].length; b++) {
                            $("#"+a+b).prop("onclick", null).off("click");
                        }
                    }
                }
                
                function foundWinnerUp(animal, x, y){
                    i = x-1;
                    while (tab[i][y] !== undefined && !isAnimal(i, y) && (!isPushers(animal, tab[i][y]))){
                        i--;
                    }
                    url = './php/ajax/ajax_updateWinner.php';
                    request = $.post(url, {id_game: IDGAME, type: tab[i][y]["type"], winner:true});
                    url = './php/ajax/ajax_winner.php';
                    request = $.post(url, {id_game: IDGAME});
                    request.done(function(data) {
                        $('#winner').text('Le joueur ' + data + ' a gagné !');
                    });
                    for (a = 0; a < tab.length; a++) {
                        for (b = 0; b < tab[a].length; b++) {
                            $("#"+a+b).prop("onclick", null).off("click");
                        }
                    }
                }
                

                let posX = this.id[0];
                let posY = this.id[1];
                if ((!aroule || isAdmin) && tab[posX][posY] !== undefined && ((posX == 0 || posX == 4) || (posY == 0 || posY == 4)) && player.reserve > 0 && verifTour(player.type)) {
                        if (tab[posX][posY] === null && res && clic == 1) {
                            
                            player.reserve--;
                            $('#nbr').text("Reserve : " + player.reserve);
                            tab[posX][posY] = {
                                x : posX,
                                y : posY,
                                type : player.type,
                                orientation : rotation
                            }
                            res = false;
                            clic = 0;
                            dernier = ""+posX+posY;
                            round++;

                            if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                            else $('#round').text("C'est au tour du rhinocéros");

                            url = './php/ajax/ajax_incrementround.php';
                            $.post(url, {id_game : IDGAME});

                            url = './php/ajax/ajax_updatedernier.php';
                            $.post(url, {id_game : IDGAME, dernier : dernier});
                        } else {
                            if (res && clic == 1) {
                                // Ici le joueur veut insérer en poussée !
                                clic = 0;

                                pion = {
                                    x : posX,
                                    y : posY,
                                    type : player.type,
                                    orientation : rotation
                                }

                                if (makeGoodPush(pion, posX, posY, pion.orientation, res)) {
                                    player.reserve--;
                                    tab[posX][posY] = pion;
                                    $('#nbr').text("Reserve : " + player.reserve);
                                    clic = 0;
                                    pion = null;
                                    round++;

                                    if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                                    else $('#round').text("C'est au tour du rhinocéros");

                                    url = './php/ajax/ajax_incrementround.php';
                                    $.post(url,{id_game : IDGAME});

                                    dernier = ""+posX+posY;

                                    url = './php/ajax/ajax_updatedernier.php';
                                    $.post(url, {id_game : IDGAME, dernier : dernier});
                                }
                                res = false;
                                pion = null;
                            } else if (clic == 0 && tab[posX][posY] !== undefined && tab[posX][posY] != null && tab[posX][posY]['type'] == player.type) {
                                // ici le joueur selectionne son pion
                                if (verifTour(player.type)) {
                                    pion = tab[posX][posY];
                                    clic++;
                                }
                            } else if (clic == 1 && tab[posX][posY] !== undefined && tab[posX][posY] == null) {
                                //ici le joueur veut bouger un pion séléctionné dans le bord du plateau
                                if ((Math.abs(pion['x'] - posX) == 1 && pion['y'] == posY) || (Math.abs(pion['y'] - posY) == 1 && pion['x'] == posX)) {
                                    tab[posX][posY] = pion;
                                    tab[pion['x']][pion['y']] = null;

                                    tab[posX][posY]['x'] = posX;
                                    tab[posX][posY]['y'] = posY;
                                    
                                    clic = 0;
                                    pion = null;
                                    round++;

                                    if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                                    else $('#round').text("C'est au tour du rhinocéros");

                                    url = './php/ajax/ajax_incrementround.php';
                                    $.post(url, {id_game : IDGAME});

                                    dernier = ""+posX+posY;

                                    url = './php/ajax/ajax_updatedernier.php';
                                    $.post(url, {id_game : IDGAME, dernier : dernier})
                                }
                            } else if (clic == 1 && tab[posX][posY] !== undefined && tab[posX][posY] != null){
                                // Ici poussée au bord du tableau
                                let pionx = (typeof(pion['x']) == "string") ? parseInt(pion['x']) : pion['x'];
                                let piony = (typeof(pion['y']) == "string") ? parseInt(pion['y']) : pion['y'];
                                if ((posX != pionx || posY != piony) && makeGoodPush(pion, posX, posY, pion.orientation, res)) {
                                    tab[posX][posY] = pion;
                                    tab[pion['x']][pion['y']] = null;

                                    tab[posX][posY]['x'] = posX;
                                    tab[posX][posY]['y'] = posY;
                                
                                    clic = 0;
                                    pion = null;
                                    round++;

                                    if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                                    else $('#round').text("C'est au tour du rhinocéros");

                                    url = './php/ajax/ajax_incrementround.php';
                                    $.post(url,{id_game : IDGAME});

                                    dernier = ""+posX+posY;

                                    url = './php/ajax/ajax_updatedernier.php';
                                    $.post(url, {id_game : IDGAME, dernier : dernier})
                                }
                            }
                        
                    }
                } else if ((!aroule || isAdmin) && clic == 0 && tab[posX][posY] !== undefined && tab[posX][posY] != null && tab[posX][posY]['type'] == player.type && verifTour(player.type)) {
                    // ici le joueur selectionne son pion
                    pion = tab[posX][posY];
                    clic++;
                } else if ((!aroule || isAdmin) && clic == 1 && tab[posX][posY] !== undefined && tab[posX][posY] == null && verifTour(player.type)) {
                    //ici le joueur veut bouger un pion séléctionné et veux bouger à l'intérieur du plateau
                    if ((Math.abs(pion['x'] - posX) == 1 && pion['y'] == posY) || (Math.abs(pion['y'] - posY) == 1 && pion['x'] == posX)) {
                        tab[posX][posY] = pion;
                        tab[pion['x']][pion['y']] = null;

                        tab[posX][posY]['x'] = posX;
                        tab[posX][posY]['y'] = posY;
    
                        clic = 0;
                        pion = null;
                        round++;

                        if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                        else $('#round').text("C'est au tour du rhinocéros");

                        url = './php/ajax/ajax_incrementround.php';
                        $.post(url,{id_game : IDGAME});

                        dernier = ""+posX+posY;

                        url = './php/ajax/ajax_updatedernier.php';
                        $.post(url, {id_game : IDGAME, dernier : dernier});
                    }
                } else if ((!aroule || isAdmin) && clic == 1 && tab[posX][posY] !== undefined && tab[posX][posY] != null && verifTour(player.type)){
                    // Ici poussée dans le tableau 
                    let pionx = (typeof(pion['x']) == "string") ? parseInt(pion['x']) : pion['x'];
                    let piony = (typeof(pion['y']) == "string") ? parseInt(pion['y']) : pion['y'];
                    if ((posX != pionx || posY != piony) && makeGoodPush(pion, posX, posY, pion.orientation, res)) {
                        tab[posX][posY] = pion;
                        tab[pion['x']][pion['y']] = null;

                        tab[posX][posY]['x'] = posX;
                        tab[posX][posY]['y'] = posY;
                        clic = 0;
                        pion = null;
                        round++;

                        if (round%2 == 0) $('#round').text("C'est le tour de l'éléphant");
                        else $('#round').text("C'est au tour du rhinocéros");

                        url = './php/ajax/ajax_incrementround.php';
                        $.post(url,{id_game : IDGAME});

                        dernier = ""+posX+posY;

                        url = './php/ajax/ajax_updatedernier.php';
                        $.post(url, {id_game : IDGAME, dernier : dernier});
                    }
                }
                url = './php/ajax/ajax_saveboard.php';
                requete = $.post(url, {plateau: JSON.stringify(tab), id_game: IDGAME, player: JSON.stringify(player)});
                for (a = 0; a < tab.length; a++) {
                    for (b = 0; b < tab[a].length; b++) {
                        if (tab[a][b] !== undefined) {
                            let elem = document.getElementById("" + a + b);
                            if (tab[a][b] == null) {
                                elem.setAttribute('style', "top:" + elem.style.top + ";");
                                elem.setAttribute('class', 'gameBtn');
                            } else {
                                let image;
                                switch (tab[a][b]['type']) {
                                    case "elephant" :
                                        image = "background-image: url(images/" + ORIENTATION_ELEPHANT[tab[a][b]['orientation']] + ");";
                                        break;
                                    case "rhinoceros" :
                                        image = "background-image: url(images/" + ORIENTATION_RHINO[tab[a][b]['orientation']] + ");";
                                        break;
                                    default :
                                        image = "background-image: url(images/rocher.gif);";
                                }
                                elem.setAttribute("style", "top:" + elem.style.top + ";" + image);
                            }
                        }
                    }
                }
                if (dernier != "") {
                    let dbtn = document.getElementById(dernier);
                    dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid red; background-image:" + dbtn.style.backgroundImage + ";");
                }

                if (pion != null) {
                    let x = (typeof(pion['x']) == "string") ? parseInt(pion['x']) : pion['x'];
                    let y = (typeof(pion['y']) == "string") ? parseInt(pion['y']) : pion['y'];
                    // Si pion selectionné on affiche les cases possibles
                    if (checkCoords(x-1, y)) {
                        let dbtn = document.getElementById(""+(x-1)+y);
                            dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid green; background-image:" + dbtn.style.backgroundImage + ";");     
                    }
                    if (checkCoords(x+1, y)) {
                        let dbtn = document.getElementById(""+(x+1)+y);
                        dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid green; background-image:" + dbtn.style.backgroundImage + ";");
                    }
                    if (checkCoords(x, y-1)) {
                        let dbtn = document.getElementById(""+x+(y-1));
                        dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid green; background-image:" + dbtn.style.backgroundImage + ";");
                    }
                    if (checkCoords(x, y+1)) {
                        let dbtn = document.getElementById(""+x+(y+1));
                        dbtn.setAttribute("style","top: " + dbtn.style.top + ";border: 1px solid green; background-image:" + dbtn.style.backgroundImage + ";");
                    }
                }
            });
        });
    });

    $('#animal').click(function() {
        var url = './php/ajax/ajax_testdecode.php';
        var animal = '{"x":0,"y":2,"species":"elephant","image":"/images/10.gif","orientation":"nord"}';

        request = $.post(url, {object: animal});

        request.done(function(data) {
            $('#test2').html(data);
        });
    });    
});
