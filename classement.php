<p>Ce classement utilise un syst&egrave;me similaire &agrave; celui en vigueur sur Tenhou, qui est lui m&ecirc;me inspir&eacute; du <a href="https://fr.wikipedia.org/wiki/Classement_Elo">classement ELO</a>.</p>
<p><strong>Classement</strong></p>
<p />
<?php

// Connexion a la BD

include("connect.php");

// Definition des variables :

if ( isset($_GET["Ajustement"])) {
$Ajustement = $_GET["Ajustement"];
} else {
$Ajustement = "0.2" ;
}

if ( isset($_GET["Attenuation"])) {
$Attenuation = $_GET["Attenuation"];
} else {
$Attenuation = "15" ;
}

if ( isset($_GET["Base"])) {
$Base = $_GET["Base"];
} else {
$Base = "10" ;
}

if ( isset($_GET["DelaiPenalite"])) {
$DelaiPenalite = $_GET["DelaiPenalite"];
} else {
$DelaiPenalite = "90" ;
}

if ( isset($_GET["Penalite"])) {
$Penalite = $_GET["Penalite"];
} else {
$Penalite = "0" ;
}


// Boucle avec les joueurs :


//$Joueurs = $bdd->query('SELECT * FROM Ligue_Joueurs ORDER BY ID ASC');

$Joueurs = $bdd->prepare("SELECT * FROM Ligue_Joueurs ORDER BY ID ASC");
$Joueurs -> execute();

$nb = 0;
while( $Joueur = $Joueurs->fetch())
{
	$nb++;
	$ELO[$nb] = 1500;
	$ListeJoueurs[$nb]['ID'] = $Joueur['ID'];
	$ListeJoueurs[$nb]['Pseudo'] = $Joueur['Pseudo'];
	$ListeJoueurs[$nb]['DernierePartie'] = date("Y-m-d", time()+86400); ;
}

$Joueurs->closeCursor();

// Boucle avec toutes les parties :


$Parties = $bdd->query('SELECT * FROM Ligue_Parties ORDER BY id ASC');

while( $Partie = $Parties->fetch() )
{
	$Date = $Partie['Date'];
	$ID = $Partie['ID'];

	$Parti = $bdd->query('SELECT * FROM Ligue_Scores WHERE IDPartie='.$ID.' ORDER BY Score ASC');

	$i = 4;
	while( $Part = $Parti->fetch() )
	{
		$Joueur[$i] = $Part['IDJoueur'];
		$Score[$i] = $Part['Score'];
		$DernierePartie[$i] = $ListeJoueurs[$Joueur[$i]]['DernierePartie'];
		$ListeJoueurs[$Joueur[$i]]['DernierePartie'] = $Date;
		$i--;
	}


	$Parti->closeCursor();

	$Jours1 = max( floor((strtotime($Date) - strtotime($DernierePartie[1]) )/86400), 0);
	$Jours2 = max( floor((strtotime($Date) - strtotime($DernierePartie[2]) )/86400), 0);
	$Jours3 = max( floor((strtotime($Date) - strtotime($DernierePartie[3]) )/86400), 0);
	$Jours4 = max( floor((strtotime($Date) - strtotime($DernierePartie[4]) )/86400), 0);

	$Malus1 = floor($Jours1 / $DelaiPenalite)*(-$Penalite);
	$Malus2 = floor($Jours2 / $DelaiPenalite)*(-$Penalite);
	$Malus3 = floor($Jours3 / $DelaiPenalite)*(-$Penalite);
	$Malus4 = floor($Jours4 / $DelaiPenalite)*(-$Penalite);

	$ELO1 = $ELO[$Joueur[1]] + $Malus1;
	$ELO2 = $ELO[$Joueur[2]] + $Malus2;
	$ELO3 = $ELO[$Joueur[3]] + $Malus3;
	$ELO4 = $ELO[$Joueur[4]] + $Malus4;
	$mean = ( $ELO1 + $ELO2 + $ELO3 + $ELO4 )/4;

	$Var1 = round($Ajustement * ($Base*3 + ($mean - $ELO1) / $Attenuation), 2);
	$ELO[$Joueur[1]] = max($ELO1 + $Var1,1200);
	$Var2 = round($Ajustement * ($Base + ($mean - $ELO2) / $Attenuation), 2);
	$ELO[$Joueur[2]] = max($ELO2 + $Var2,1200);
	$Var3 = round($Ajustement * (-$Base + ($mean - $ELO3) / $Attenuation), 2);
	$ELO[$Joueur[3]] = max($ELO3 + $Var3,1200);
	$Var4 = round($Ajustement * (-$Base*3 + ($mean - $ELO4) / $Attenuation), 2);
	$ELO[$Joueur[4]] = max($ELO4 + $Var4,1200);

	$NewELO1 = $ELO[$Joueur[1]];
	$NewELO2 = $ELO[$Joueur[2]];
	$NewELO3 = $ELO[$Joueur[3]];
	$NewELO4 = $ELO[$Joueur[4]];


	$Pseudo1 = $ListeJoueurs[$Joueur[1]]['Pseudo'];
	$Pseudo2 = $ListeJoueurs[$Joueur[2]]['Pseudo'];
	$Pseudo3 = $ListeJoueurs[$Joueur[3]]['Pseudo'];
	$Pseudo4 = $ListeJoueurs[$Joueur[4]]['Pseudo'];
	$NbParties[$Joueur[1]]=$NbParties[$Joueur[1]]+1;
	$NbParties[$Joueur[2]]=$NbParties[$Joueur[2]]+1;
	$NbParties[$Joueur[3]]=$NbParties[$Joueur[3]]+1;
	$NbParties[$Joueur[4]]=$NbParties[$Joueur[4]]+1;

}

$Parties->closeCursor();

// Regularisation des malus des joueurs n'ayant pas rejoue

$count = 1;
while ($count <= $nb) {
	$Jours = max( floor((strtotime(date('Y-m-d')) - strtotime($Pseudo = $ListeJoueurs[$count]['DernierePartie']) )/86400), 0);
	$Malus = floor($Jours / $DelaiPenalite)*(-$Penalite);
	$JoueurDebug = $ListeJoueurs[$count]['Pseudo'];
	$ELO[$count] = max( $ELO[$count] + $Malus, 1200 );
	$count = $count + 1;
}

// Mise en forme du resultat
$count = 1;
while ($count <= $nb) {
	$ListeJoueurs[$count]['ELO'] = $ELO[$count];
	$count = $count + 1;
}

array_multisort($ELO, SORT_DESC, $ListeJoueurs);

$count = 0;
$classement = "<table border=1>
<tr><td>Rang</td><td>Pseudo</td><td>ELO</td></tr>";

$classementfiltre1 = "<table border=1>
<tr><td>Rang</td><td>Pseudo</td><td>ELO</td></tr>";
$count1 = 0;

$classementfiltre2 = "<table border=1>
<tr><td>Rang</td><td>Pseudo</td><td>ELO</td></tr>";
$count2 = 0;

while ($count < $nb) {
	$ID = $ListeJoueurs[$count]['ID'];
	$Pseudo = $ListeJoueurs[$count]['Pseudo'];
	$ELO = $ListeJoueurs[$count]['ELO'];
	if ($NbParties[$ID] > 9) {
		$count1 = $count1 + 1;
		$classementfiltre1= "$classementfiltre1\n<tr><td>$count1</td><td><a href=\"?page=joueur&id=$ID\">$Pseudo</a></td><td>$ELO</td></tr>";
	}

	if ($NbParties[$ID] > 19) {
		$count2 = $count2 + 1;
		$classementfiltre2= "$classementfiltre2\n<tr><td>$count2</td><td><a href=\"?page=joueur&id=$ID\">$Pseudo</a></td><td>$ELO</td></tr>";
	}

	$count = $count + 1;
	$classement= "$classement\n<tr><td>$count</td><td><a href=\"?page=joueur&id=$ID\">$Pseudo</a></td><td>$ELO</td></tr>";

}
$classement="$classement</table>";

$classementfiltre1="$classementfiltre1</table>";


$classementfiltre2="$classementfiltre2</table>";

?>
<script type="text/javascript">
function display(id) {
  document.getElementById("classement").style.display = "none";
  document.getElementById("classementfiltre1").style.display = "none";
  document.getElementById("classementfiltre2").style.display = "none";
  document.getElementById(id).style.display = "block";
}
</script>
<?php

echo "<div id=\"classement\"><strong>Tous les joueurs</strong> - <a href=\"javascript:void(0)\" onclick=\"display('classementfiltre1')\">10+ parties</a> - <a href=\"javascript:void(0)\" onclick=\"display('classementfiltre2')\">20+ parties</a>$classement</div>";
echo "<div id=\"classementfiltre1\"  style=\"display:none\"><a href=\"javascript:void(0)\" onclick=\"display('classement')\">Tous les joueurs</a> - <strong>10+ parties</strong> - <a href=\"javascript:void(0)\" onclick=\"display('classementfiltre2')\">20+ parties</a>$classementfiltre1</div>";
echo "<div id=\"classementfiltre2\"  style=\"display:none\"><a href=\"javascript:void(0)\" onclick=\"display('classement')\">Tous les joueurs</a> - <a href=\"javascript:void(0)\" onclick=\"display('classementfiltre1')\">10+ parties</a> - <strong>20+ parties</strong>$classementfiltre2</div>";

?>
