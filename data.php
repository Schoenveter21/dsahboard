<?php
header('Content-Type: application/json');

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "mydb"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(array('error' => $conn->connect_error)));
}

// SQL-query om het aantal behaalde toetsen per maand op te halen
$sql = "SELECT DATE_FORMAT(datum, '%b') AS maand, COUNT(*) AS aantal
        FROM resultaat
        WHERE (datum >= '2023-08-01' AND datum < '2024-08-01') AND Behaald = 'Ja'
        GROUP BY MONTH(datum)
        ORDER BY MONTH(datum)";

$result = $conn->query($sql);

// Maak een array voor de maanden en initialiseer met 0
$maanden = [
    'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 
    'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 
    'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 
    'May' => 0, 'Jun' => 0, 'Jul' => 0
];

// Vul de array met gegevens uit de database
while ($row = $result->fetch_assoc()) {
    $maanden[$row['maand']] = (int)$row['aantal'];
}

// Sluit de verbinding met de database
$conn->close();

// Bereken cumulatieve totalen
$cumulatief = 0;
$formattedData = [];
foreach ($maanden as $maand => $aantal) {
    $cumulatief += $aantal;
    $formattedData[] = ['maand' => $maand, 'aantal' => $cumulatief];
}

echo json_encode($formattedData);



?>
