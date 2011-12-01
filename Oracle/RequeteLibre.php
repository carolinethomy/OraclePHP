﻿<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Projet Web Oracle</title>
    </head>

    <body>
		<h1>Exécuter une requête</h1>
		
		<form action="RequeteLibre.php" method="post">
			<div style="margin-left: 20px; width: 500px">
				Requête : <input type="text-area" width="300px" height="100px" rows="5" name="requete" /><br />
				<br />
				<div style="text-align: right">
					<input type="submit" value="Exécuter"/>
				</div>
			</div>
		</form>
		
	<?php
	if (isset($_POST['requete']))
	{
		echo "<br /><br />";
		try
		{
			//Connexion
			$utilisateur = "util";
			$mdp = "util";
			$conn = new pdo('oci:dbname=//localhost:1521/xe', $utilisateur, $mdp);
			if ($conn )
			{
				//Préparation de la requête
				$rsql = $_POST['requete'];
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
					echo "Pas de ligne<br />";
				}
				echo "<br />";
				echo "$nrows Lignes lues<br />";
			}
			else
			{
				echo "Connexion pas ok.";
			}
		} catch (PDOException $m)
		{ 
			print "Probleme particulier ".$m->getMessage();
		}
	}
	?>
    </body>
</html>