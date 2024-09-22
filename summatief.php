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

// Definieer het totale aantal summatieve examens
$totale_examens = 23; // Totaal aantal summatieve examens

// Definieer het minimale cijfer voor een voldoende (5.5)
$minimum_voldoende = 5.5; 

// SQL-query om het aantal behaalde voldoendes op te halen
$sql = "SELECT 
            COUNT(CASE WHEN Behaald = 'Ja' AND formatief = 'Nee' AND score >= $minimum_voldoende THEN 1 END) AS voldoende_behaald
        FROM resultaat";
$result = $conn->query($sql);

// Controleer of de query succesvol was
if (!$result) {
    die(json_encode(array('error' => $conn->error)));
}

$data = $result->fetch_assoc();
$voldoende_behaald = (int)$data['voldoende_behaald']; // Zorg ervoor dat dit een integer is
$nog_te_doen = $totale_examens - $voldoende_behaald;

// Zorg ervoor dat de aantallen correct zijn
if ($nog_te_doen < 0) {
    $nog_te_doen = 0; // Zorg dat dit niet negatief is
}

// Zorg ervoor dat het totaal altijd gelijk is aan 23
if ($voldoende_behaald + $nog_te_doen > $totale_examens) {
    $voldoende_behaald = $totale_examens - $nog_te_doen; // Corrigeer indien nodig
}

// Maak een array met de behaalde en nog te doen aantallen
$data = array(
    'behaald' => $voldoende_behaald, // Aantal behaalde voldoendes
    'nog_te_doen' => $nog_te_doen, // Aantal examens die nog te doen zijn
    'totaal_examens' => $totale_examens // Totaal aantal examens
);

// Sluit de verbinding met de database
$conn->close();

// Stuur de verzamelde gegevens terug naar de client in JSON-formaat
echo json_encode($data);
?>
