<?php
header('Content-Type: application/json'); // Zet de content type header naar JSON
require_once 'db.php'; // Inclusief het database verbindingsbestand

function haalFormatieveScoresOp($conn) { // Definieer een functie om formatieve scores op te halen
    $sql = "SELECT 
                SUM(CASE WHEN Behaald = 'Ja' AND formatief = 'Ja' THEN score ELSE 0 END) AS behaalde_score,
                SUM(CASE WHEN formatief = 'Ja' THEN score ELSE 0 END) AS totale_formatieve_score
            FROM resultaat"; // SQL query om behaalde en totale formatieve scores op te halen

    $result = $conn->query($sql); // Voer de query uit
    if (!$result) { // Controleer of de query succesvol was
        die(json_encode(array('error' => $conn->error))); // Geef een foutmelding als de query mislukt
    }

    return $result->fetch_assoc(); // Retourneer de resultaten als een associatieve array
}

$total_formatieve_score = 160; // Stel de totale formatieve score in op 160
$conn = connectDatabase(); // Maak verbinding met de database
$scores = haalFormatieveScoresOp($conn); // Haal de formatieve scores op

$behaalde_score = $scores['behaalde_score'] ?? 0; // Haal de behaalde score op, standaard naar 0 als niet beschikbaar
$nog_te_doen_score = $total_formatieve_score - $behaalde_score; // Bereken de nog te doen score

if ($nog_te_doen_score < 0) { // Controleer of de nog te doen score negatief is
    $nog_te_doen_score = 0; // Stel de nog te doen score in op 0 als deze negatief is
}

$behaalde_percentage = ($total_formatieve_score > 0) ? ($behaalde_score / $total_formatieve_score) * 100 : 0; // Bereken het behaalde percentage
$nog_te_doen_percentage = ($total_formatieve_score > 0) ? ($nog_te_doen_score / $total_formatieve_score) * 100 : 0; // Bereken het nog te doen percentage

$total_percentage = $behaalde_percentage + $nog_te_doen_percentage; // Bereken het totale percentage
if ($total_percentage > 100) { // Controleer of het totale percentage groter is dan 100
    $nog_te_doen_percentage = 100 - $behaalde_percentage; // Pas het nog te doen percentage aan zodat het totale percentage 100 is
}

$data = array( // Maak een array met de gegevens
    'behaald' => $behaalde_score, // Voeg de behaalde score toe aan de array
    'behaald_percentage' => round($behaalde_percentage, 2), // Voeg het behaalde percentage toe aan de array, afgerond op 2 decimalen
    'nog_te_doen' => $nog_te_doen_score, // Voeg de nog te doen score toe aan de array
    'nog_te_doen_percentage' => round($nog_te_doen_percentage, 2) // Voeg het nog te doen percentage toe aan de array, afgerond op 2 decimalen
);

$conn->close(); // Sluit de databaseverbinding
echo json_encode($data); // Geef de gegevens terug als JSON
?>
