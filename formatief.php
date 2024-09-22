<?php
// Stel de content-type header in op JSON
header('Content-Type: application/json');

// Databaseconfiguratie
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "mydb"; 

// Maak verbinding met de MySQL-database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    die(json_encode(array('error' => $conn->connect_error)));
}

// Definieer de totale score voor formatieve toetsen
$total_formatieve_score = 160; // Totale mogelijke score voor formatieve toetsen

// SQL-query om de behaalde en de totale formatieve scores op te halen
$sql = "SELECT 
            SUM(CASE WHEN Behaald = 'Ja' AND formatief = 'Ja' THEN score ELSE 0 END) AS behaalde_score,
            SUM(CASE WHEN formatief = 'Ja' THEN score ELSE 0 END) AS totale_formatieve_score
        FROM resultaat";
// Voer de query uit en sla het resultaat op
$result = $conn->query($sql);

// Haal de rijen op als een associatieve array
$data = $result->fetch_assoc();

// Bereken de behaalde score
$behaalde_score = $data['behaalde_score'] ?? 0; // De behaalde score uit de query, standaard op 0 als null

// Bereken de nog te doen score
$nog_te_doen_score = $total_formatieve_score - $behaalde_score; 

// Zorg ervoor dat de scores niet negatief zijn
if ($nog_te_doen_score < 0) {
    $nog_te_doen_score = 0;
}

// Bereken percentages
$behaalde_percentage = ($total_formatieve_score > 0) ? ($behaalde_score / $total_formatieve_score) * 100 : 0; 
$nog_te_doen_percentage = ($total_formatieve_score > 0) ? ($nog_te_doen_score / $total_formatieve_score) * 100 : 0; 

// Zorg ervoor dat de percentages bij elkaar exact 100% zijn
$total_percentage = $behaalde_percentage + $nog_te_doen_percentage;
if ($total_percentage > 100) {
    $nog_te_doen_percentage = 100 - $behaalde_percentage; 
}

// Maak een array met de behaalde en nog te doen scores en percentages
$data = array(
    'behaald' => $behaalde_score,
    'behaald_percentage' => round($behaalde_percentage, 2), // Rond af op 2 decimalen
    'nog_te_doen' => $nog_te_doen_score,
    'nog_te_doen_percentage' => round($nog_te_doen_percentage, 2) // Rond af op 2 decimalen
);

// Sluit de verbinding met de database
$conn->close();

// Stuur de verzamelde gegevens terug naar de client in JSON-formaat
echo json_encode($data);
?>
