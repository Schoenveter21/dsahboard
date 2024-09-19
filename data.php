<?php
// Stel de content-type header in op JSON, zodat de browser weet dat het een JSON-antwoord is
header('Content-Type: application/json');

// Databaseconfiguratie: verbindingsinstellingen voor de MySQL-database
$servername = "localhost"; // De server waarop de database draait
$username = "root"; // Gebruikersnaam voor de database
$password = ""; // Wachtwoord voor de database
$dbname = "mydb"; // Naam van de database

// Maak verbinding met de MySQL-database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    // Als de verbinding faalt, geef een foutmelding terug in JSON-formaat en stop de uitvoering
    die(json_encode(array('error' => $conn->connect_error)));
}

// SQL-query om het aantal behaalde toetsen per maand op te halen voor het academisch jaar 2023-2024
$sql = "SELECT DATE_FORMAT(datum, '%b') AS maand, COUNT(*) AS aantal
        FROM resultaat
        WHERE YEAR(datum) = '2023'
        GROUP BY MONTH(datum)
        ORDER BY MONTH(datum)";

// Voer de query uit en sla het resultaat op
$result = $conn->query($sql);

// Maak een lege array om de data op te slaan
$data = array();

// Loop door de rijen van het query-resultaat en sla elke rij op in de array
while ($row = $result->fetch_assoc()) {
    $data[] = $row; // Voeg elke rij toe aan de array $data
}

// Sluit de verbinding met de database
$conn->close();

// Stuur de verzamelde gegevens terug naar de client in JSON-formaat
echo json_encode($data);
?>
