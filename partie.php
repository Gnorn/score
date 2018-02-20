
<?php

$id=$_GET["id"];

// Connexion a la BD

include("connect.php");

// Recuperer les infos de la partie

$Partie = $bdd->prepare('SELECT * FROM Ligue_Parties WHERE ID=?');
$Partie->bindParam(1, $id);
$Partie->execute();
$Partie = $Partie->fetch();
$Date = $Partie['Date'];
$IDJ1 = $Partie['J1'];
$IDJ2 = $Partie['J2'];
$IDJ3 = $Partie['J3'];
$IDJ4 = $Partie['J4'];


// Recuperer les noms des joueurs

$Joueur1 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$IDJ1);
$Joueur1 = $Joueur1->fetch();
$Joueur1 = $Joueur1['Pseudo'];

$Joueur2 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$IDJ2);
$Joueur2 = $Joueur2->fetch();
$Joueur2 = $Joueur2['Pseudo'];

$Joueur3 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$IDJ3);
$Joueur3 = $Joueur3->fetch();
$Joueur3 = $Joueur3['Pseudo'];

$Joueur4 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$IDJ4);
$Joueur4 = $Joueur4->fetch();
$Joueur4 = $Joueur4['Pseudo'];

// Recuperer les scores

$Score1 = $bdd->prepare('SELECT * FROM Ligue_Scores WHERE IDPartie=? AND IDJoueur=?');
$Score1->bindParam(1, $id);
$Score1->bindParam(2, $IDJ1);
$Score1->execute();
$Score1 = $Score1->fetch();
$Score1 = $Score1['Score'];

$Score2 = $bdd->prepare('SELECT * FROM Ligue_Scores WHERE IDPartie=? AND IDJoueur=?');
$Score2->bindParam(1, $id);
$Score2->bindParam(2, $IDJ2);
$Score2->execute();
$Score2 = $Score2->fetch();
$Score2 = $Score2['Score'];

$Score3 = $bdd->prepare('SELECT * FROM Ligue_Scores WHERE IDPartie=? AND IDJoueur=?');
$Score3->bindParam(1, $id);
$Score3->bindParam(2, $IDJ3);
$Score3->execute();
$Score3 = $Score3->fetch();
$Score3 = $Score3['Score'];

$Score4 = $bdd->prepare('SELECT * FROM Ligue_Scores WHERE IDPartie=? AND IDJoueur=?');
$Score4->bindParam(1, $id);
$Score4->bindParam(2, $IDJ4);
$Score4->execute();
$Score4 = $Score4->fetch();
$Score4 = $Score4['Score'];


?>
<p><strong><a href="./">Classement</a> > Partie #<? echo $id ?> jou&eacute;e le <? echo $Date; ?></strong></p>
<p /><table border=1>
<tr><td>Joueurs</td><td>Score</td></tr>
<tr><td><a href="?page=joueur&id=<? echo $IDJ1; ?>"><? echo $Joueur1; ?></a></td><td><? echo $Score1; ?></td></tr>
<tr><td><a href="?page=joueur&id=<? echo $IDJ2; ?>"><? echo $Joueur2; ?></a></td><td><? echo $Score2; ?></td></tr>
<tr><td><a href="?page=joueur&id=<? echo $IDJ3; ?>"><? echo $Joueur3; ?></a></td><td><? echo $Score3; ?></td></tr>
<tr><td><a href="?page=joueur&id=<? echo $IDJ4; ?>"><? echo $Joueur4; ?></a></td><td><? echo $Score4; ?></td></tr>

</table>
