<?php
header('Content-Type: application/json');
require_once 'db.php';

function haalSummatieveCijfersOp($conn) {
    // Query om zowel de scores als de datums op te halen
    $sql = "SELECT datum, score FROM resultaat WHERE formatief = 'Nee' ORDER BY datum ASC";
    $result = $conn->query($sql);
    
    if (!$result) {
        die(json_encode(array('error' => $conn->error)));
    }

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;  // Voeg de datum en score toe aan de array
    }

    return $data;
}

$conn = connectDatabase();
$cijfers = haalSummatieveCijfersOp($conn);
$conn->close();

echo json_encode($cijfers);  // Retourneer de JSON met de cijfers
?>
