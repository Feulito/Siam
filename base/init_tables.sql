CREATE TABLE admin
(
    id_admin Integer PRIMARY KEY AUTOINCREMENT,
    pseudo Varchar(15),
    password Varchar(60)
);

CREATE TABLE user
(
    id_user Integer PRIMARY KEY AUTOINCREMENT,
    pseudo Varchar(15),
    password Varchar(60)
);

CREATE TABLE game 
(
    id_game  Integer PRIMARY KEY AUTOINCREMENT,
    name varchar(60) NOT NULL,
    round Integer NOT NULL,
    winner Integer, /*gagnant : 1 pour les rhinoceros, 2 pour les elephants*/
    nbPlayers Integer NOT NULL,
    dernier varchar(2)
);

CREATE TABLE player
(
    id_player Integer PRIMARY KEY AUTOINCREMENT,
    id_game Integer references game(id_game),
    id_user Integer references user(id_user),
    type_animal Varchar(15),
    reserve Integer
);

CREATE TABLE pawns
(
    id_pawn Integer PRIMARY KEY AUTOINCREMENT,
    id_game Integer references game(id_game),
    type Varchar(15), /*type de piece : rocher, rhinoceros, elephant*/
    posX Integer,
    posY Integer,
    orientation Integer
);

