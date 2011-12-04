<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Projet Web Oracle</title>
    </head>

    <body>
		<h1>Tablespaces</h1>
		<br />
		<br />
		
	<?php
	try
	{
		//Connexion
		$utilisateur = "system";
		$mdp = "admin";
		$conn = new pdo('oci:dbname=//localhost:1521/xe', $utilisateur, $mdp);
		if ($conn )
		{
			//Préparation de la requête
			$rsql = 'SELECT f.tablespace_name, SUM(f.bytes) / 1024000 AS Taille_totale, SUM(free.bytes) / 1024000 AS libre, 
					(SUM(f.bytes) - SUM(free.bytes)) / 1024000 AS Utilise, ROUND(100 * (SUM(f.bytes) - SUM(free.bytes)) / SUM(f.bytes), 2) AS pourcentage_utilisation
					FROM dba_data_files f, dba_free_space free
					WHERE f.tablespace_name = free.tablespace_name
					GROUP BY f.tablespace_name
					UNION
					SELECT f.tablespace_name, SUM(f.bytes) / 1024000 AS Taille_totale, SUM(free.bytes_free) / 1024000 AS libre, 
					SUM(free.bytes_used) / 1024000 AS Utilise, ROUND(100 * SUM(free.bytes_used) / SUM(f.bytes), 2) AS pourcentage_utilisation
					FROM dba_temp_files f, v$temp_space_header free
					WHERE f.tablespace_name = free.tablespace_name
					GROUP BY f.tablespace_name';
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
					for ($j = 0; $j < $nbcol ;$j++)
					{
						if ($j == 4)
							echo '<td width="105px"><div id="Pourcentage" style="background-color: red;" width="' . floor($results[$j]) . 'px" height="10px"></div></td>';
						else 
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
	?>
    </body>
</html>