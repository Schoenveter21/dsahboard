<?php
header('Content-Type: application/json'); // Stel de content type in op JSON
require_once 'db.php'; // Importeer de database verbindingsbestand

function haalBehaaldeToetsenPerMaandOp($conn) { // Definieer een functie om behaalde toetsen per maand op te halen
    $sql = "SELECT DATE_FORMAT(datum, '%b') AS maand, COUNT(*) AS aantal
            FROM resultaat
            WHERE (datum >= '2023-08-01' AND datum < '2024-08-01') AND Behaald = 'Ja'
            GROUP BY MONTH(datum)
            ORDER BY MONTH(datum)"; // SQL query om behaalde toetsen per maand op te halen

    $result = $conn->query($sql); // Voer de query uit
    if (!$result) { // Controleer of de query succesvol was
        die(json_encode(array('error' => $conn->error))); // Geef een foutmelding terug als de query mislukt
    }

    return $result; // Retourneer het resultaat van de query
}

$conn = connectDatabase(); // Maak verbinding met de database
$result = haalBehaaldeToetsenPerMaandOp($conn); // Haal behaalde toetsen per maand op

$maanden = [ // Initialiseer een array met maanden en zet de waarden op 0
    'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 
    'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 
    'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 
    'May' => 0, 'Jun' => 0, 'Jul' => 0
];

while ($row = $result->fetch_assoc()) { // Loop door de resultaten van de query
    $maanden[$row['maand']] = (int)$row['aantal']; // Zet het aantal behaalde toetsen per maand in de array
}

$conn->close(); // Sluit de databaseverbinding

$cumulatief = 0; // Initialiseer een variabele voor cumulatieve telling
$formattedData = []; // Initialiseer een array voor geformatteerde data
foreach ($maanden as $maand => $aantal) { // Loop door de maanden array
    $cumulatief += $aantal; // Tel het aantal behaalde toetsen op bij de cumulatieve telling
    $formattedData[] = ['maand' => $maand, 'aantal' => $cumulatief]; // Voeg de maand en cumulatieve telling toe aan de geformatteerde data array
}

echo json_encode($formattedData); // Geef de geformatteerde data terug als JSON
?>
