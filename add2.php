<html>
<body>

<p><strong>Ajouter partie</strong></p>
<FORM METHOD="POST" ACTION="input.php">
<p />

<?php

$joueur=$_POST['joueur'];
$score=$_POST['score'];


// Connexion a la BD

include("connect.php");


$partie = 0;
foreach ($joueur[1] as $zob) {
  // Verifier que la partie doit etre traitee

	if ( $joueur[1][$partie] == "vide" && $joueur[2][$partie] == "vide" & $joueur[3][$partie] == "vide" && $joueur[4][$partie] == "vide") {

		break;
	}

	if ( $joueur[1][$partie] == "0" ){
		$partie++;
		$offset++;
	  continue;
  }

	// Recuperer pseudos joueurs

	if ( $joueur[1][$partie] != "vide" ) {
		$pseudo1 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur[1][$partie]);
		$pseudo1 = $pseudo1->fetch();
		$pseudo1 = $pseudo1['Pseudo'];
	}
	else {
		$joueurmanquant=1;
		$pseudo1 = "<strong><font color=\"red\">JOUEUR MANQUANT</font></strong>";
	}

	if ( $joueur[2][$partie] != "vide" ) {
		$pseudo2 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur[2][$partie]);
		$pseudo2 = $pseudo2->fetch();
		$pseudo2 = $pseudo2['Pseudo'];
	}
	else {
		$joueurmanquant=1;
		$pseudo2 = "<strong><font color=\"red\">JOUEUR MANQUANT</font></strong>";
  }


	if ( $joueur[3][$partie] != "vide" ) {
		$pseudo3 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur[3][$partie]);
		$pseudo3 = $pseudo3->fetch();
		$pseudo3 = $pseudo3['Pseudo'];
	}
	else {
		$pseudo3 = "<strong><font color=\"red\">JOUEUR MANQUANT</font></strong>";
    $joueurmanquant=1;
  }

	if ( $joueur[4][$partie] != "vide" ) {
		$pseudo4 = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$joueur[4][$partie]);
		$pseudo4 = $pseudo4->fetch();
		$pseudo4 = $pseudo4['Pseudo'];
	}
	else {
		$pseudo4 = "<strong><font color=\"red\">JOUEUR MANQUANT</font></strong>";
		$joueurmanquant=1;
	}


	?>
<p />
<table border=1>
<tr><th colspan=3>Partie #<?php echo ($partie-$offset); ?></th></tr>
<tr><td># Joueur</td><td>Joueur</td><td>Score</td></tr>
<tr><td>1</td><td><input type="hidden" name="joueur[1][<?php echo ($partie-$offset); ?>]" value="<? echo $joueur[1][$partie]; ?>" /><? echo $pseudo1; ?></td><td><input type="hidden" name="score[1][<?php echo ($partie-$offset); ?>]" value="<? echo $score[1][$partie]; ?>" /><? if ( ! is_numeric($score[1][$partie]) ) { echo "<strong><font color=\"red\">/!\\ </font></strong>"; $scoremanquant=1;} echo $score[1][$partie]; ?></td></tr>
<tr><td>2</td><td><input type="hidden" name="joueur[2][<?php echo ($partie-$offset); ?>]" value="<? echo $joueur[2][$partie]; ?>" /><? echo $pseudo2; ?></td><td><input type="hidden" name="score[2][<?php echo ($partie-$offset); ?>]" value="<? echo $score[2][$partie]; ?>" /><? if ( ! is_numeric($score[2][$partie]) ) { echo "<strong><font color=\"red\">/!\\ </font></strong>"; $scoremanquant=1;} echo $score[2][$partie]; ?></td></tr>
<tr><td>3</td><td><input type="hidden" name="joueur[3][<?php echo ($partie-$offset); ?>]" value="<? echo $joueur[3][$partie]; ?>" /><? echo $pseudo3; ?></td><td><input type="hidden" name="score[3][<?php echo ($partie-$offset); ?>]" value="<? echo $score[3][$partie]; ?>" /><? if ( ! is_numeric($score[3][$partie]) ) { echo "<strong><font color=\"red\">/!\\ </font></strong>"; $scoremanquant=1;} echo $score[3][$partie]; ?></td></tr>
<tr><td>4</td><td><input type="hidden" name="joueur[4][<?php echo ($partie-$offset); ?>]" value="<? echo $joueur[4][$partie]; ?>" /><? echo $pseudo4; ?></td><td><input type="hidden" name="score[4][<?php echo ($partie-$offset); ?>]" value="<? echo $score[4][$partie]; ?>" /><? if ( ! is_numeric($score[4][$partie]) ) { echo "<strong><font color=\"red\">/!\\ </font></strong>"; $scoremanquant=1;} echo $score[4][$partie]; ?></td></tr>
</table>

	<?php

	// Verification que tout va bien.


	if ( $joueur[1][$partie] == $joueur[2][$partie] || $joueur[1][$partie] == $joueur[3][$partie] || $joueur[1][$partie] == $joueur[4][$partie] || $joueur[2][$partie] == $joueur[3][$partie] || $joueur[2][$partie] == $joueur[4][$partie] || $joueur[3][$partie] == $joueur[4][$partie] )
	{
		$checkpseudo="bad";
		echo "<strong>Doublon joueur</strong><br />";
	}

	$totalcheckscore = $score[1][$partie]+$score[2][$partie]+$score[3][$partie]+$score[4][$partie];
	if ( $totalcheckscore != 0 )
	{
		$checkscore="bad";
		echo "<strong>Total scores non nul : $totalcheckscore</strong><br />";
	}

	$partie++;

}



if ( $joueurmanquant == 1 || $checkpseudo == "bad" || $scoremanquant == 1 )
	echo "<input action=\"action\" type=\"button\" value=\"Corriger\" onclick=\"history.go(-1);\" />";
elseif ( $checkscore != "bad")
	echo "<input action=\"action\" type=\"button\" value=\"Corriger\" onclick=\"history.go(-1);\" /><input type=\"submit\" value=\"Valider\">";
else
	echo "<input action=\"action\" type=\"button\" value=\"Corriger\" onclick=\"history.go(-1);\" /><input type=\"submit\" value=\"Valider (ATTENTION)\">";

?>
</FORM>
</body>
</html>
