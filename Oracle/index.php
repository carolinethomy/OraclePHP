<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Projet Web Oracle</title>
    </head>

    <body>
		<h1>Projet Web Oracle</h1>
	<?php
	try
	{
		//Connexion
		$utilisateur = "util";
		$mdp = "util";
		$conn = new pdo('oci:dbname=//localhost:1521/xe', $utilisateur, $mdp);
		if ($conn )
		{
			echo "Connexion reussie en tant que <i>".$utilisateur."</i>";
			echo "<br/>";
			//Préparation de la requête
			$rsql = "select * from DEPT";
			$stmt = $conn->prepare($rsql, array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
			$stmt->execute();
			$nrows = 1;//$stmt->rowcount();
			if ($nrows > 0)
			{
				echo "<table border=\"1\">\n";
				echo "<tr>\n";
				$nbcol = $stmt->columncount(); // nombre colonnes ramenées par la requête
				/*for ($j = 1; $j < $nbcol ; $j++)
				{
					$colonne = $stmt->getColumnMeta($j); // recherche libelle
					echo "<th>".$colonne['name']."</th>\n";
				}*/
				echo "</tr>\n";
				
				while ($results = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT))
				{
					for ($j = 1; $j < $nbcol ;$j++)
					{
						echo "<td>$results[$j]</td>\n";
					}
					echo "</tr>\n";
					$nrows++;
				}
				$nrows--;
				echo "</table>\n";
			}
			else
			{
				echo "Pas de ligne<br />\n";
			}
			echo " ";
			echo "$nrows Lignes lues<br />\n";
		}
		else
		{
			echo "Connexion pas ok.";
		}
	} catch (PDOException $m)
	{ 
		print "Probleme particulier ".$m->getMessage();
	}
	?>
		<a href="RequeteLibre.php">Exécuter une requête</a>
    </body>
</html>