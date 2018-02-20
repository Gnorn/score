
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

$PremierePlace=0;
$DeuxiemePlace=0;
$TroisiemePlace=0;
$QuatriemePlace=0;

// Connexion a la BD

include("connect.php");

// Recuperer pseudo du joueur


//$JoueurCourant = $bdd->query('SELECT * FROM Ligue_Joueurs WHERE ID='.$id);
$JoueurCourant = $bdd->prepare("SELECT * FROM Ligue_Joueurs WHERE ID=?");
$JoueurCourant->bindParam(1, $id);
$JoueurCourant->execute();
$JoueurCourant = $JoueurCourant->fetch();
$JoueurCourant = $JoueurCourant['Pseudo'];

$Contenu = "";

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

        if($id == $Joueur[1])
          $PremierePlace++;
        if($id == $Joueur[2])
          $DeuxiemePlace++;
        if($id == $Joueur[3])
          $TroisiemePlace++;
        if($id == $Joueur[4])
          $QuatriemePlace++;


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

	$NewELO1 = $ELO[$Joueur[1]];
	$NewELO2 = $ELO[$Joueur[2]];
	$NewELO3 = $ELO[$Joueur[3]];
	$NewELO4 = $ELO[$Joueur[4]];


	$Pseudo1 = $ListeJoueurs[$Joueur[1]]['Pseudo'];
	$Pseudo2 = $ListeJoueurs[$Joueur[2]]['Pseudo'];
	$Pseudo3 = $ListeJoueurs[$Joueur[3]]['Pseudo'];
	$Pseudo4 = $ListeJoueurs[$Joueur[4]]['Pseudo'];

	if ( $id == $Joueur[1] ) {
		$Contenu .= "<tr><td><a href=\"?page=partie&id=$ID\">$ID</a></td><td>$Date</td><td>$Score[1]</td><td>$mean</td><td>$NewELO1<br />($Var1 / $Malus1)</td></tr>";
		$Contenu .= "\n";
	}

	if ( $id == $Joueur[2] ) {
		$Contenu .= "<tr><td><a href=\"?page=partie&id=$ID\">$ID</a></td><td>$Date</td><td>$Score[2]</td><td>$mean</td><td>$NewELO2<br />($Var2 / $Malus2)</td></tr>";
		$Contenu .= "\n";
	}

	if ( $id == $Joueur[3] ) {
		$Contenu .= "<tr><td><a href=\"?page=partie&id=$ID\">$ID</a></td><td>$Date</td><td>$Score[3]</td><td>$mean</td><td>$NewELO3<br />($Var3 / $Malus3)</td></tr>";
		$Contenu .= "\n";
	}

	if ( $id == $Joueur[4] ) {
		$Contenu .= "<tr><td><a href=\"?page=partie&id=$ID\">$ID</a></td><td>$Date</td><td>$Score[4]</td><td>$mean</td><td>$NewELO4<br />($Var4 / $Malus4)</td></tr>";
		$Contenu .= "\n";
	}
}
$Parties->closeCursor();


// Regularisation des malus des joueurs n'ayant pas rejoue

$Jours = max( floor((strtotime(date('Y-m-d')) - strtotime($Pseudo = $ListeJoueurs[$id]['DernierePartie']) )/86400), 0);
$Malus = floor($Jours / $DelaiPenalite)*(-$Penalite);
$JoueurDebug = $ListeJoueurs[$id]['Pseudo'];
$ELO[$id] = max( $ELO[$id] + $Malus, 1200 );

$Contenu .= "<tr><td></td><td colspan=\"3\">Malus de non participation :</td><td>$Malus</td></tr>";
$Contenu .= "<tr><td></td><td colspan=\"3\">ELO Final :</td><td>$ELO[$id]</td></tr>";
?>

<p><strong><a href="./">Classement</a> > <? echo $JoueurCourant ?> </strong></p>

<?
$Total = $PremierePlace + $DeuxiemePlace + $TroisiemePlace + $QuatriemePlace;
echo "Parties jouées : $Total<br />";
$PremierePlace = round($PremierePlace/$Total, 2)*100;
echo "Taux premières places : $PremierePlace %<br />";
$DeuxiemePlace = round($DeuxiemePlace/$Total, 2)*100;
echo "Taux deuxièmes places : $DeuxiemePlace %<br />";
$TroisiemePlace = round($TroisiemePlace/$Total, 2)*100;
echo "Taux troisièmes places : $TroisiemePlace %<br />";
$QuatriemePlace = round($QuatriemePlace/$Total, 2)*100;
echo "Taux quatrièmes places : $QuatriemePlace %<br />";
echo "<strong>ELO actuel : $ELO[$id]</strong><br />";
echo "<img src=\"/score/joueurgraph.php?id=$id\" /><br />";
?>

<p /><table border=1>
<tr><th>Partie #</th><th>Date</th><th>Score</th><th>ELO moyen<br />de la table</th><th>Nouvel ELO<br />(Variation / Malus)</th></tr>

<? echo $Contenu; ?>

</table>
