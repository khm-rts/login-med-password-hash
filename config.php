<?php
// Konfiguration af databaseforbindelse
$db_host	= 'localhost';
$db_user	= 'root';
$db_pass	= '';
$db_name	= 'login';

// Opretter forbindelse til databasen
$mysqli		= new mysqli($db_host, $db_user, $db_pass, $db_name);

// Hvis forbindelsesfejl er true, udskriv fejlnr. og fejlbeskrivelse
if ( $mysqli->connect_error )
{
	die('Connect Error: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Sætter tegnsætning til utf8
$mysqli->set_charset('utf8');