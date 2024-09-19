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
    // Als de verbinding mislukt, geef een foutmelding terug in JSON-formaat en stop de uitvoering
    die(json_encode(array('error' => $conn->connect_error)));
}

// Definieer de totale score voor summatieve toetsen
$totale_summatief_score = 50; // Totale mogelijke score voor summatieve toetsen

// SQL-query om de behaalde en totale summatieve scores op te halen
$sql = "SELECT 
            SUM(CASE WHEN Behaald = 'Ja' AND formatief = 'Nee' THEN score ELSE 0 END) AS behaalde_score,
            SUM(CASE WHEN formatief = 'Nee' THEN score ELSE 0 END) AS totale_summatief_score
        FROM resultaat";
// Voer de query uit en sla het resultaat op
$result = $conn->query($sql);

// Haal de rijen op als een associatieve array
$data = $result->fetch_assoc();

// Bereken de behaalde en nog te doen scores
$behaalde_score = $data['behaalde_score']; // De behaalde score uit de query
$nog_te_doen_score = $data['totale_summatief_score'] - $behaalde_score; // Resterende score om te behalen

// Bereken het percentage behaalde score ten opzichte van de totale score
$behaalde_percentage = ($behaalde_score / $totale_summatief_score) * 100; // Percentage behaalde score
$nog_te_doen_percentage = 100 - $behaalde_percentage; // Percentage resterende score

// Indien de behaalde score meer dan 100% is, stel deze in op maximaal 100%
if ($behaalde_percentage > 100) {
    $behaalde_percentage = 100; // Beperk tot 100%
    $nog_te_doen_percentage = 0; // Geen resterende score
}

// Als het percentage voor nog te doen lager is dan 0, stel dit in op 0
if ($nog_te_doen_percentage < 0) {
    $nog_te_doen_percentage = 0; // Beperk tot minimaal 0%
}

// Zorg ervoor dat de percentages bij elkaar opgeteld exact 100% zijn
if ($behaalde_percentage + $nog_te_doen_percentage > 100) {
    $nog_te_doen_percentage = 100 - $behaalde_percentage; // Correctie zodat het totaal 100% is
}

// Maak een array met de behaalde en nog te doen percentages
$data = array(
    'behaald' => $behaalde_percentage, // Percentage behaalde toetsen
    'nog_te_doen' => $nog_te_doen_percentage // Percentage nog te doen toetsen
);

// Sluit de verbinding met de database
$conn->close();

// Stuur de verzamelde gegevens terug naar de client in JSON-formaat
echo json_encode($data);
?>
trait_exists