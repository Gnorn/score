<html>
<body>

<?php

if ( isset($_POST[CSV])){
	echo "<em>Pré-rempli via un CSV...</em><br />";
	$CSV=$_POST[CSV];
}

?>
<p><strong>Ajouter partie</strong></p>
<FORM METHOD="POST" ACTION="add2.php">

<div id="parties">

<?php

// Connexion a la BD

include("connect.php");

// Recuperer liste des joueurs

$Joueurs = $bdd->query('SELECT * FROM Ligue_Joueurs ORDER BY Pseudo ASC');

while( $Joueur = $Joueurs->fetch())
{

// Debut de la boucle des joueurs

	$IDJoueur = $Joueur['ID'];
	$Pseudo = $Joueur['Pseudo'];
	$Couple=[$IDJoueur,$Pseudo];
  $ListeJoueurs[]=$Couple;

	$Choix = "$Choix<option value=\\\"$IDJoueur\\\">$Pseudo</option>";

// Fin de la boucle des joueurs

}

$Joueurs->closeCursor();
$Partie = "0";
if (isset($CSV)){
	echo "<div id=\"partie$Partie\"><p /><table border=1>

	<tr><th colspan=3>Partie #$Partie <a href=\"javascript:void(0)\" onclick=\"SupprimerPartie($Partie);\">(Supprimer)</a></th></tr>

	<tr><td># Joueur</td><td>Joueur</td><td>Score</td></tr>";
	$Partie++;
	$JoueurCourant=1;
	$PartieCourante=[];
	$CSV = str_getcsv($CSV,"\n");
  foreach($CSV as $line){
		if ($line=="---"){
			while ($JoueurCourant <5){
				echo "<tr><td>$JoueurCourant</td><td><select name=\"joueur[$JoueurCourant][".($Partie-1)."]\">\n<option value=\"vide\" selected></option>".stripslashes($Choix)."</select></td><td><input type=\"text\" name=\"score[$JoueurCourant][".($Partie-1)."]\"/></td></tr>\n";
					  $JoueurCourant++;
			}
		  echo "</table></div><div id=\"partie$Partie\"><p /><table border=1>

<tr><th colspan=3>Partie #$Partie <a href=\"javascript:void(0)\" onclick=\"SupprimerPartie($Partie);\">(Supprimer)</a></th></tr>

<tr><td># Joueur</td><td>Joueur</td><td>Score</td></tr>\n";
      $Partie++;
			$JoueurCourant=1;
		} else {
			$line=str_getcsv($line);
			if ($JoueurCourant < 5){
				$search = array_search($line[0],array_column($ListeJoueurs,1));
				if ( $search != "" ) {
					$IDCourant=$ListeJoueurs[$search][0];
		  	  echo "<tr><td>$JoueurCourant</td><td><select name=\"joueur[$JoueurCourant][".($Partie-1)."]\">\n<option value=\"$IDCourant\" selected>$line[0]</option>".stripslashes($Choix)."</select></td><td><input type=\"text\" name=\"score[$JoueurCourant][".($Partie-1)."]\" value=\"$line[1]\" /></td></tr>\n";
				} else {
	  			echo "<tr><td>$JoueurCourant</td><td><select name=\"joueur[$JoueurCourant][".($Partie-1)."]\">\n<option value=\"vide\" selected></option>".stripslashes($Choix)."</select><br /><em>($line[0])</em></td><td><input type=\"text\" name=\"score[$JoueurCourant][".($Partie-1)."]\" value=\"$line[1]\" /></td></tr>\n";
				}
			}
			$JoueurCourant++;
		}

	}
	while ($JoueurCourant <5){
		echo "<tr><td>$JoueurCourant</td><td><select name=\"joueur[$JoueurCourant][".($Partie-1)."]\">\n<option value=\"vide\" selected></option>".stripslashes($Choix)."</select></td><td><input type=\"text\" name=\"score[$JoueurCourant][".($Partie-1)."]\"/></td></tr>\n";
			  $JoueurCourant++;
	}
	echo "</table></div>";
}
?>

<script type="text/javascript">
Partie = "<?php echo $Partie; ?>";
	function AjouterPartie() {
		var newdiv = document.createElement('div');
		newdiv.id="partie"+Partie;
	  document.getElementById('parties').appendChild(newdiv);
		document.getElementById('partie'+Partie).innerHTML = "<p /><table border=1>\n<tr><th colspan=3>Partie #"+Partie+" <a href=\"javascript:void(0)\" onclick=\"SupprimerPartie("+Partie+");\">(Supprimer)</a></th></tr>\n<tr><td># Joueur</td><td>Joueur</td><td>Score</td></tr>\n<tr><td>1</td><td><select name=\"joueur[1]["+Partie+"]\"><option value=\"vide\" selected></option><? echo $Choix; ?></select></td><td><input type=\"text\" name=\"score[1]["+Partie+"]\" /></td></tr>\n<tr><td>2</td><td><select name=\"joueur[2]["+Partie+"]\"><option value=\"vide\" selected></option><? echo $Choix; ?></select></td><td><input type=\"text\" name=\"score[2]["+Partie+"]\" /></td></tr>\n<tr><td>3</td><td><select name=\"joueur[3]["+Partie+"]\"><option value=\"vide\" selected></option><? echo $Choix; ?></select></td><td><input type=\"text\" name=\"score[3]["+Partie+"]\" /></td></tr>\n<tr><td>4</td><td><select name=\"joueur[4]["+Partie+"]\"><option value=\"vide\" selected></option><? echo $Choix; ?></select></td><td><input type=\"text\" name=\"score[4]["+Partie+"]\" /></td></tr>\n</table>";
		Partie++;
	}

	function SupprimerPartie(numero) {
    document.getElementById('partie'+numero).innerHTML ="<input type=\"hidden\" name=\"joueur[1]["+numero+"]\" value=\"0\">";
	}
</script>


</div>

<a href="javascript:void(0)" onclick="AjouterPartie();">Ajouter partie</a><br />
<input type="submit" value="Soumettre">
</FORM>
</body>
</html>
