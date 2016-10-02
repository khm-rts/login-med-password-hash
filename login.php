<?php
// Inkludér config-fil der etablerer forbindelse til databasen
require 'config.php';
// Starter session så man kan gemme data heri og tilgå det via $_SESSION
session_start();
// Start output buffer. Indhold vises og buffer tømmes når ob_end_flush() kaldes, hvilket gøres til sidst i filer
ob_start()
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>

<body>
	<?php
	// Hvis man er logget ind vises brugerens navn og et link til logud
	if ( isset($_SESSION['bruger_id']) )
	{
		?>
    	<p>Du er logget ind som: <strong><?php echo $_SESSION['bruger_navn'] ?></strong></p>
        <a href="login.php?logud">Log ud</a>
        <?php
		// Hvis logud er sat i vores query string (adresselinjen)
		if ( isset($_GET['logud']) )
		{
			// Slet session for at logge ud
			unset($_SESSION['bruger_id']);
			unset($_SESSION['bruger_navn']);

			// Da der er sket ændringer i session, genereres et nyt id
			session_regenerate_id();

			// Opdater siden
			header('Location: login.php');
			// Sikrer at der ikke udføres mere kode i filen
			exit;
		}			
	}
	// Ellers vises formular og kode til login
	else
	{
		?>
        <h2>Log ind</h2>
        
        <?php		
        // Formular til login
        // VIGTIGT: Attributterne name på input-felter må ikke matche kolonnenavne i database
        ?>
        <form method="post">
            <label for="email">E-mail:</label><br>
            <input type="email" name="email" id="email" required><br>
            
            <label for="adgangskode">Adgangskode:</label><br>
            <input type="password" name="adgangskode" id="adgangskode" required><br>
            <br>
            <input type="submit" name="login" value="Log ind!">
        </form>
        
        <?php
		// Hvis vi har sendt formular, køres følgende kodeblok
        if ( isset($_POST['login']) )
        {
            // Tjek begge felter er udfyldt. HTML5-validering er ikke sikker, men er til stede for brugervenligheden skyld
            if ( !empty($_POST['email']) && !empty($_POST['adgangskode']) )
            {
				// Hent indtastede oplysninger fra form og gemmer i variabler
				// VIGTIGT: Brug escape_string til at sikre databasen imod SQL-injections
				$bruger_email		= $mysqli->escape_string($_POST['email']);
				$bruger_adgangskode	= $mysqli->escape_string($_POST['adgangskode']);

				// Forespørgsel til at hente indtastede brugers id, navn og hashed adgangskode
				$query	=
					"SELECT 
						bruger_id, bruger_navn, bruger_hashed_adgangskode 
					FROM 
						brugere 
					WHERE 
						bruger_email = '$bruger_email'";

				// Send forespørgslen til databasen
				$result	= $mysqli->query($query);

				// Tjek om ovenstående forespørgsel fejlede (hvis $result returnede false)
				if (!$result)
				{
					// Udskriv fejlbesked fra databasen, samt forespørgslen for at se evt. fejl heri
					die ( $mysqli->error . '<pre>Query: ' . $query . '</pre>');
				}

				// Tjek at der blev fundet præcis 1 bruger
				if ($result->num_rows == 1)
				{
					// Returner data fra db, som PHP object og gem i variablen row
					$row = $result->fetch_object();

					// Brug den indbyggede funktion password_verify() (fra PHP v.5.5), til at tjekke om den indtastede adgangskode, matcher den hashede fra databasen. Er det tilfældet logges ind, ved at gemme brugerens id og navn i session
					if ( password_verify($bruger_adgangskode, $row->bruger_hashed_adgangskode) )
					{
						// Da der sker ændringer i session, genereres et nyt id
						session_regenerate_id();

						// Gem brugers id i browserens session til login. VIGTIGT: Kræver session_start();
						$_SESSION['bruger_id']		= $row->bruger_id;
						// Gem brugers navn til at vise hvem der er logget ind
						$_SESSION['bruger_navn']	= $row->bruger_navn;

						// Opdatér siden
						header('Location: login.php');
						// Sikrer at der ikke udføres mere kode i filen
						exit;
					}
					// Hvis det indtastede ikke matchede vores hashed adgangskode fra dabasen, vises denne fejlbesked. Besked i parantes skal kun vises for at kunne fejlsøge - man vil aldrig oplyse evt. hackere at det lykkedes at finde en e-mail og det kun er koden der er forkert.
					else
					{
						echo '<p>Fejl i e-mail eller adgangskode (Forkert adgangskode)</p>';
					}
				}
				// Hvis ikke der blev funder præcist 1 bruger, vises denne fejlbesked. Husk ikke at vise beskeden i parantes på færdige sites. Læs ovenstående kommentar.
				else
				{
					echo '<p>Fejl i e-mail eller adgangskode (E-mail blev ikke fundet i databasen)</p>';
				}
            }
            // Hvis et af felter ikke blev udfyldt, vises denne besked
			else
			{
				echo '<p>Fejl! Du har ikke udfyldt begge felter</p>';
			}
        } // Slutter if ( isset($_POST['login']) )
	} // Slutter else til if ( isset($_SESSION['bruger_id']) )
	?>
</body>
</html>
<?php
// Lukker forbindelsen til databasen
$mysqli->close();
// Tøm buffer og vis indhold til bruger fra buffer
ob_end_flush();