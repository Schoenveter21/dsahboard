<?php
header('Content-Type: application/json'); // Zet de content-type header naar JSON
require_once 'db.php'; // Inclusief het database verbindingsbestand

function haalVoldoendesOp($conn, $minimum_voldoende) { // Definieer een functie om voldoende scores op te halen
    $sql = "SELECT 
                COUNT(CASE WHEN Behaald = 'Ja' AND formatief = 'Nee' AND score >= $minimum_voldoende THEN 1 END) AS voldoende_behaald
            FROM resultaat"; // SQL query om het aantal voldoende scores te tellen
    $result = $conn->query($sql); // Voer de query uit
    if (!$result) { // Controleer of de query succesvol was
        die(json_encode(array('error' => $conn->error))); // Stop de uitvoering en geef een foutmelding als de query mislukt
    }

    $data = $result->fetch_assoc(); // Haal het resultaat op als een associatieve array
    return (int)$data['voldoende_behaald']; // Geef het aantal voldoende scores terug
}

$totale_examens = 23; // Totaal aantal examens
$minimum_voldoende = 5.5; // Minimum score voor een voldoende
$conn = connectDatabase(); // Maak verbinding met de database
$voldoende_behaald = haalVoldoendesOp($conn, $minimum_voldoende); // Haal het aantal voldoende scores op

$nog_te_doen = $totale_examens - $voldoende_behaald; // Bereken het aantal examens dat nog gedaan moet worden
if ($nog_te_doen < 0) { // Controleer of het aantal nog te doen examens negatief is
    $nog_te_doen = 0; // Zet het aantal nog te doen examens op 0 als het negatief is
}

if ($voldoende_behaald + $nog_te_doen > $totale_examens) { // Controleer of het totaal van voldoende en nog te doen examens groter is dan het totale aantal examens
    $voldoende_behaald = $totale_examens - $nog_te_doen; // Pas het aantal voldoende examens aan als dat het geval is
}

$data = array( // Maak een array met de resultaten
    'behaald' => $voldoende_behaald, // Aantal voldoende examens
    'nog_te_doen' => $nog_te_doen, // Aantal nog te doen examens
    'totaal_examens' => $totale_examens // Totaal aantal examens
);

$conn->close(); // Sluit de databaseverbinding
echo json_encode($data); // Geef de resultaten terug als JSON
?>
