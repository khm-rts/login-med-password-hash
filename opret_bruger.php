<?php
// Inkludér config-fil der etablerer forbindelse til databasen
require 'config.php';
// Starter session så man kan gemme data heri og tilgå det via $_SESSION
session_start();
// Start output buffer. Indhold vises og buffer tømmes når ob_end_flush() kaldes, hvilket gøres til sidst i filer
ob_start();

/**
 * Denne kode vil teste din server, for at beregne hvor høj "cost" (Vægt på forbrug af server ressourcer),
 * der kan håndteres. Du bør sætte "cost" så højt som muligt, uden at serveren bliver sløvet for meget.
 * 8-10, er et godt interval mht. sikkerhed og gerne lidt højere hvis serveren kan klare det. Standard er 10. Koden nedenfor, forøger "cost" med en, for hver gang løkken kører. Løkken fortsætter kun hvis den beregnede tid for at hashe adgangskode med den aktuelle "cost", er lavere end 50 milisekunder, hvilket er en god tid for at logge brugere ind.
 */
$timeTarget = 0.05; // 50 millisekunder

$cost = 8;
do
{
	$cost++;
	$start = microtime(true);
	password_hash("test", PASSWORD_DEFAULT, ["cost" => $cost]);
	$end = microtime(true);
}
while ( ($end - $start) < $timeTarget );

echo "Anbefalet cost beregnet: " . $cost . "\n"
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Opret bruger</title>
</head>

<body>
	<h2>Opret bruger</h2>
    
    <?php
	// Definer variabler med tom værdi til echo i formular
	$bruger_navn = '';
	$bruger_email =  '';
	
	// Hvis vi har sendt formular, køres følgende kodeblok
	if ( isset($_POST['opret_bruger']) )
	{
		// Hent og escape de indtastede værdier fra formular
		$bruger_navn		= $mysqli->escape_string($_POST['navn']);
		$bruger_email		= $mysqli->escape_string($_POST['email']);
		
		// Hvis ikke de indtastede adgangskoder er ens, udskrives denne fejl på siden
		if ( $_POST['adgangskode'] != $_POST['bekraeft_adgangskode'] )
		{
			echo '<p>Fejl! De indtastede adgangskoder er ikke identiske</p>';
		}
		// Hvis de er ens køres koden til at opdatere brugere
		else
		{
			// Vi henter og escaper den indtastede adgangskode
			$bruger_adgangskode	= $mysqli->escape_string($_POST['adgangskode']);

			// Vi bruger den indbyggede funktion/api password_hash() (kræver PHP v5.5), til at hashe den indtastede adgangskode. Første parameter er den adgangskode der skal hashed. Andet parameter er den algoritme der skal bruges og angives som en konstanst. PASSWORD_DEFAULT og PASSWORD_BCRYPT er tilgængelig. Brug PASSWORD_DEFAULT, som altid vil opdateres til at bruge den bedste algoritme, som dog er BCRYPT på nuvære
			//nde tidspunkt, men den vil blive opdateret løbende. Det anbefales at anvende VARCHAR(255) til feltet i databasen, hvor passwordet skal gemmes, da den over tiden vil generere længere og længere hash. Tredje parameter er options i et array, hvor der kan angives cost og salt. Det anbefales kraftigt at lade funktionen generere salt selv, og hvis der benyttes PHP over v7.0.0 fås en notice. Funktionen returner den hashede adgangskode, eller false, hvis den fejlede i at generere adgangskode.
			// LÆS MERE HER: http://php.net/manual/en/function.password-hash.php
			$bruger_hashed_adgangskode = password_hash($_POST['adgangskode'], PASSWORD_DEFAULT);
			
			// Lav forespørgsel til at oprette brugerens oplysninger i databasen
			$query =
				"INSERT INTO 
					brugere (bruger_navn, bruger_email, bruger_adgangskode, bruger_hashed_adgangskode)
				VALUES
					('$bruger_navn', '$bruger_email', '$bruger_adgangskode', '$bruger_hashed_adgangskode')";

			// Send forespørgslen til databasen			
			$result = $mysqli->query($query);

			// Tjek om ovenstående forespørgsel fejlede (hvis $result returnede false)
			if (!$result)
			{
				// Udskriv fejlbesked fra databasen, samt forespørgslen for at se evt. fejl heri
				die ( $mysqli->error . '<pre>Query: ' . $query . '</pre>');
			}
			
			echo '<p>Din bruger blev oprettet</p>';
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
        <label for="adgangskode">Adgangskode:</label><br>
        <input type="password" name="adgangskode" id="adgangskode" required><br>
        <br>
        <label for="bekraeft_adgangskode">Bekræft adgangskode:</label><br>
        <input type="password" name="bekraeft_adgangskode" id="bekraeft_adgangskode" required><br>
        <br>
        <input type="submit" name="opret_bruger" value="Opret bruger!">
    </form>
</body>
</html>
<?php
// Lukker forbindelsen til databasen
$mysqli->close();
// Tøm buffer og vis indhold til bruger fra buffer
ob_end_flush();