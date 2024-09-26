<?php
header('Content-Type: application/json'); // Stel de content type header in op JSON
require_once 'db.php'; // Inclusief het database verbindingsbestand

function haalSummatieveCijfersOp($conn) { // Definieer een functie om summatieve cijfers op te halen
    // Query om zowel de scores als de datums op te halen
    $sql = "SELECT datum, score, vakken FROM resultaat WHERE formatief = 'Nee' ORDER BY datum ASC"; // SQL query om datum, score en vakken op te halen waar formatief 'Nee' is
    $result = $conn->query($sql); // Voer de query uit en sla het resultaat op
    
    if (!$result) { // Controleer of de query succesvol was
        die(json_encode(array('error' => $conn->error))); // Als de query faalt, geef een JSON error bericht terug en stop de uitvoering
    }

    $data = array(); // Initialiseer een lege array om de resultaten op te slaan
    while ($row = $result->fetch_assoc()) { // Loop door de resultaten
        $data[] = $row;  // Voeg de datum en score toe aan de array
    }

    return $data; // Retourneer de array met resultaten
}

$conn = connectDatabase(); // Maak verbinding met de database
$cijfers = haalSummatieveCijfersOp($conn); // Haal de summatieve cijfers op
$conn->close(); // Sluit de database verbinding

echo json_encode($cijfers);  // Retourneer de JSON met de cijfers
?>
