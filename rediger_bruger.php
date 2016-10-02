<?php
// Inkludér config-fil der etablerer forbindelse til databasen
require 'config.php';
// Starter session så man kan gemme data heri og tilgå det via $_SESSION
session_start();
// Start output buffer. Indhold vises og buffer tømmes når ob_end_flush() kaldes, hvilket gøres til sidst i filer
ob_start();

// Hvis man ikke er logget ind skal man ikke have adgang til denne side og vi smider derfor brugeren til login-siden
if ( !isset($_SESSION['bruger_id']) )
{
	header('Location: login.php');
	exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rediger bruger</title>
</head>

<body>
	<h2>Rediger bruger</h2>
    
    <?php
	// Hent den aktuelle brugers id fra session, escape værdien og gem som variabel
	$bruger_id	= $mysqli->escape_string($_SESSION['bruger_id']);

	// Hent brugerens navn og email fra databasen som matcher det ovenstående id
	$query		= "SELECT bruger_navn, bruger_email FROM brugere WHERE bruger_id = $bruger_id";

	// Send forespørgslen til databasen	
	$result		= $mysqli->query($query);

	// Tjek om ovenstående forespørgsel fejlede (hvis $result returnede false)
	if ($result == false)
	{
		// Udskriv fejlbesked fra databasen, samt forespørgslen for at se evt. fejl heri
		die ( $mysqli->error . '<pre>Query: ' . $query . '</pre>');
	}

	// Returner data fra db, som PHP object og gem i variablen row
	$row = $result->fetch_object();
	
	// Overskriv variabeler med værdier fra databasen
	$bruger_navn	= $row->bruger_navn;
	$bruger_email	= $row->bruger_email;
	$password_sql	= '';
	
	// Hvis vi har sendt formular, køres følgende kodeblok
	if ( isset($_POST['opret_bruger']) )
	{
		// Hent og escape de indtastede værdier fra formular
		$bruger_navn	= $mysqli->escape_string($_POST['navn']);
		$bruger_email	= $mysqli->escape_string($_POST['email']);
		
		// Hvis ikke de indtastede adgangskoder er ens, udskrives denne fejl på siden
		if ( $_POST['adgangskode'] != $_POST['bekraeft_adgangskode'] )
		{
			echo '<p>Fejl! De indtastede adgangskoder er ikke identiske</p>';
		}
		// Hvis de er ens køres koden til at opdatere brugere
		else
		{
			// Hvis feltet adgangskode ikke er tomt, ønsker vi at ændre det og kører dette afsnit kode
			if ( !empty($_POST['adgangskode']) )
			{
				// Vi henter og escaper den indtastede adgangskode
				$bruger_adgangskode	= $mysqli->escape_string($_POST['adgangskode']);

				// Vi bruger den indbyggede funktion/api password_hash() (kræver PHP v5.5), til at hashe den indtastede adgangskode. Første parameter er den adgangskode der skal hashed. Andet parameter er den algoritme der skal bruges og angives som en konstanst. PASSWORD_DEFAULT og PASSWORD_BCRYPT er tilgængelig. Brug PASSWORD_DEFAULT, som altid vil opdateres til at bruge den bedste algoritme, som dog er BCRYPT på nuværende tidspunkt, men den vil blive opdateret løbende. Det anbefales at anvende VARCHAR(255) til feltet i databasen, hvor passwordet skal gemmes, da den over tiden vil generere længere og længere hash. Tredje parameter er options i et array, hvor der kan angives cost og salt. Det anbefales kraftigt at lade funktionen generere salt selv, og hvis der benyttes PHP over v7.0.0 fås en notice. Funktionen returner den hashede adgangskode, eller false, hvis den fejlede i at generere adgangskode.
				// LÆS MERE HER: http://php.net/manual/en/function.password-hash.php
				$bruger_hashed_adgangskode = password_hash($_POST['adgangskode'], PASSWORD_DEFAULT);

				// Vi gemmer den stump af sql-kode der skal opdatere felter i databasen der vedrører adgangskode
				$password_sql = ", bruger_adgangskode = '$bruger_adgangskode', bruger_hashed_adgangskode = '$bruger_hashed_adgangskode'";
			}
			
			// Lav forespørgsel til at opdatere brugerens oplysninger i databasen
			$query =
				"UPDATE 
					brugere 
				SET 
					bruger_navn = '$bruger_navn', bruger_email = '$bruger_email' $password_sql 
				WHERE
					bruger_id = $bruger_id";

			// Send forespørgslen til databasen
			$result = $mysqli->query($query);

			// Tjek om ovenstående forespørgsel fejlede (hvis $result returnede false)
			if (!$result)
			{
				// Udskriv fejlbesked fra databasen, samt forespørgslen for at se evt. fejl heri
				die ( $mysqli->error . '<pre>Query: ' . $query . '</pre>');
			}
		}
	}
	?>
	<form method="post">
    	<label for="navn">Navn:</label><br>
    	<input type="text" name="navn" id="navn" required value="<?php echo $bruger_navn ?>"><br>
        <br>
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required value="<?php echo $bruger_email ?>"><br>
        <br>
        <p>Udfyld kun adgangskode, hvis du ønsker at skifte det</p>
        <label for="adgangskode">Adgangskode:</label><br>
        <input type="password" name="adgangskode" id="adgangskode"><br>
        <br>
        <label for="bekraeft_adgangskode">Bekræft adgangskode:</label><br>
        <input type="password" name="bekraeft_adgangskode" id="bekraeft_adgangskode"><br>
        <br>
        <input type="submit" name="opret_bruger" value="Opdatér bruger!">
    </form>
</body>
</html>
<?php
// Lukker forbindelsen til databasen
$mysqli->close();
// Tøm buffer og vis indhold til bruger fra buffer
ob_end_flush();