<?php

// Definition des variables

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


$id=$_GET["id"];


// Connexion a la BD

include("connect.php");

// Recuperer pseudo du joueur


//$JoueurCourant = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$id);
$JoueurCourant = $bdd->prepare("SELECT * FROM Ligue_Joueurs WHERE ID=?");
$JoueurCourant->bindParam(1, $id);
$JoueurCourant->execute();
$JoueurCourant = $JoueurCourant->fetch();
$JoueurCourant = $JoueurCourant['Pseudo'];

// Boucle avec les joueurs :

$Joueurs = $bdd->query('SELECT * FROM Ligue_Joueurs ORDER BY ID ASC');

$nb = 0;
$ELOJoueur = array();
$ELOJoueur[] = 1500;
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
	$mean = round(( $ELO1 + $ELO2 + $ELO3 + $ELO4 )/4, 2);

	$Var1 = round($Ajustement * ($Base*3 + ($mean - $ELO1) / $Attenuation), 2);
	$ELO[$Joueur[1]] = max($ELO1 + $Var1,1200);
	$Var2 = round($Ajustement * ($Base + ($mean - $ELO2) / $Attenuation), 2);
	$ELO[$Joueur[2]] = max($ELO2 + $Var2,1200);
	$Var3 = round($Ajustement * (-$Base + ($mean - $ELO3) / $Attenuation), 2);
	$ELO[$Joueur[3]] = max($ELO3 + $Var3,1200);
	$Var4 = round($Ajustement * (-$Base*3 + ($mean - $ELO4) / $Attenuation), 2);
	$ELO[$Joueur[4]] = max($ELO4 + $Var4,1200);

	if ( $id == $Joueur[1] || $id == $Joueur[2] || $id == $Joueur[3] || $id == $Joueur[4] )
		$ELOJoueur[] = $ELO[$id];
}
$Parties->closeCursor();


// Regularisation des malus des joueurs n'ayant pas rejoue

$Jours = max( floor((strtotime(date('Y-m-d')) - strtotime($Pseudo = $ListeJoueurs[$id]['DernierePartie']) )/86400), 0);
$Malus = floor($Jours / $DelaiPenalite)*(-$Penalite);
$JoueurDebug = $ListeJoueurs[$id]['Pseudo'];
$ELO[$id] = max( $ELO[$id] + $Malus, 1200 );

if ( end($ELOJoueur) != $ELO[$id] )
	$ELOJoueur[] = $ELO[$id];

require_once("jpgraph/jpgraph.php");
require_once("jpgraph/jpgraph_line.php");

$ELO = [ 1500, 1494, 1500, 1506];

 // Width and height of the graph
$width = 600; $height = 400;

// Create a graph instance
$graph = new Graph($width,$height);

// Specify what scale we want to use,
// int = integer scale for the X-axis
// int = integer scale for the Y-axis
$graph->SetScale('intint');

// Setup a title for the graph
$graph->title->Set('Progression de '.$JoueurCourant);

// Setup titles and X-axis labels
$graph->xaxis->title->Set('(Numero de la partie)');

// Setup Y-axis title
//$graph->yaxis->title->Set('(Score)');

// Create the linear plot
$lineplot=new LinePlot($ELOJoueur);

// Add the plot to the graph
$graph->Add($lineplot);

// Display the graph
$graph->Stroke();

?>
