<html>
<body>

<?php

$joueur=$_POST['joueur'];
$score=$_POST['score'];



// Connexion a la BD

include("connect.php");

$partie = 0;
foreach ($joueur[1] as $zob) {

  $joueur1 = $joueur[1][$partie];
	$score1 = $score[1][$partie];
	$joueur2 = $joueur[2][$partie];
	$score2 = $score[2][$partie];
	$joueur3 = $joueur[3][$partie];
	$score3 = $score[3][$partie];
	$joueur4 = $joueur[4][$partie];
	$score4 = $score[4][$partie];


	// Recuperer pseudos joueurs



	$pseudo1 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur1);
	$pseudo1 = $pseudo1->fetch();
	$pseudo1 = $pseudo1['Pseudo'];

	$pseudo2 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur2);
	$pseudo2 = $pseudo2->fetch();
	$pseudo2 = $pseudo2['Pseudo'];

	$pseudo3 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur3);
	$pseudo3 = $pseudo3->fetch();
	$pseudo3 = $pseudo3['Pseudo'];

	$pseudo4 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur4);
	$pseudo4 = $pseudo4->fetch();
	$pseudo4 = $pseudo4['Pseudo'];

	// Ajout de la partie dans la BD
	// Ajout de la partie dans la table Ligue_Parties

	$Date="2014-01-22";

	$bdd->exec('INSERT INTO Ligue_Parties(Date, J1, J2, J3, J4) VALUES(CURDATE(), '.$joueur1.', '.$joueur2.', '.$joueur3.', '.$joueur4.')');
	$IDPartie = $bdd->lastInsertId();

	// Ajout des scores dans la table Score
	$bdd->exec('INSERT INTO Ligue_Scores(IDPartie, IDJoueur, Score) VALUES('.$IDPartie.', '.$joueur1.', '.$score1.')');

	$bdd->exec('INSERT INTO Ligue_Scores(IDPartie, IDJoueur, Score) VALUES('.$IDPartie.', '.$joueur2.', '.$score2.')');

	$bdd->exec('INSERT INTO Ligue_Scores(IDPartie, IDJoueur, Score) VALUES('.$IDPartie.', '.$joueur3.', '.$score3.')');

	$bdd->exec('INSERT INTO Ligue_Scores(IDPartie, IDJoueur, Score) VALUES('.$IDPartie.', '.$joueur4.', '.$score4.')');


?>
<p><strong>Partie #<? echo $IDPartie; ?> ajout&eacute;e !</strong></p>

<p /><table border=1>
<tr><td># Joueur</td><td>Joueur</td><td>Score</td></tr>
<tr><td>1</td><td><? echo $pseudo1; ?></td><td><? echo $score1; ?></td></tr>
<tr><td>2</td><td><? echo $pseudo2; ?></td><td><? echo $score2; ?></td></tr>
<tr><td>3</td><td><? echo $pseudo3; ?></td><td><? echo $score3; ?></td></tr>
<tr><td>4</td><td><? echo $pseudo4; ?></td><td><? echo $score4; ?></td></tr>
</table>

<?php


$partie++;

}
?>

</body>
</html>
