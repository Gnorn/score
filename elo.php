<?
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
$Penalite = "18" ;
}

?>

<html>
<body>
<p>

<?




// Connexion a la BD

include("connect.php");

// Boucle avec les joueurs :

$Joueurs = $bdd->query('SELECT * FROM Ligue_Joueurs ORDER BY ID ASC');

$nb = 0;
while( $Joueur = $Joueurs->fetch())
{
	$nb++;
	$ELO[$nb] = 1500;
	$ListeJoueurs[$nb]['ID'] = $Joueur['ID'];
	$ListeJoueurs[$nb]['Pseudo'] = $Joueur['Pseudo'];
	$ListeJoueurs[$nb]['DernierePartie'] = date("Y-m-d", time()+86400); ;
}

echo "Il y a $nb joueurs.<br />";

echo "<table border=1>
<tr><td>Date (ELO moyen table)</td><td>Joueur 1 : Score (Dur&eacute;e depuis derni&egrave;re partie / Malus / ELO actuel / Variation) -> Nouvel ELO</td><td>Joueur 2 : Score (Dur&eacute;e depuis derni&egrave;re partie / Malus / ELO actuel / Variation) -> Nouvel ELO</td><td>Joueur 3 : Score (Dur&eacute;e depuis derni&egrave;re partie / Malus / ELO actuel / Variation) -> Nouvel ELO</td><td>Joueur 4 : Score (Dur&eacute;e depuis derni&egrave;re partie / Malus / ELO actuel / Variation) -> Nouvel ELO</td></tr>\n";


$Joueurs->closeCursor();

// Boucle avec toutes les parties :


$Parties = $bdd->query('SELECT * FROM Ligue_Parties ORDER BY Date ASC');

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

	echo "<tr><td>$ID - $Date ($mean)</td><td>$Pseudo1 : $Score[1] ($Jours1 / $Malus1 / $ELO1 / $Var1) -> $NewELO1</td><td>$Pseudo2 : $Score[2] ($Jours2 / $Malus2 / $ELO2 / $Var2) -> $NewELO2</td><td>$Pseudo3 : $Score[3] ($Jours3 / $Malus3 / $ELO3 / $Var3) -> $NewELO3</td><td>$Pseudo4 : $Score[4] ($Jours4 / $Malus4 / $ELO4 / $Var4) -> $NewELO4</td></tr>";
	echo "\n";
}

echo "</table>";

// Regularisation des malus des joueurs n'ayant pas rejoue

$count = 1;
while ($count <= $nb) {
	$Jours = max( floor((strtotime(date('Y-m-d')) - strtotime($Pseudo = $ListeJoueurs[$count]['DernierePartie']) )/86400), 0);
	$Malus = floor($Jours / $DelaiPenalite)*(-$Penalite);
	$JoueurDebug = $ListeJoueurs[$count]['Pseudo'];
	$ELO[$count] = max( $ELO[$count] + $Malus, 1200 );
	$count = $count + 1;
}

echo "<p>Joueurs :<br />";

$count = 1;
while ($count <= $nb) {
	$ListeJoueurs[$count]['ELO'] = $ELO[$count];
	$count = $count + 1;
}

array_multisort($ELO, SORT_DESC, $ListeJoueurs);

$count = 0;
while ($count < $nb) {
	$ID = $ListeJoueurs[$count]['ID'];
	$Pseudo = $ListeJoueurs[$count]['Pseudo'];
	$ELO = $ListeJoueurs[$count]['ELO'];
	echo "$ID - $Pseudo : $ELO<br />";
	$count = $count + 1;
}

?>
</p>
</body>
</html>
