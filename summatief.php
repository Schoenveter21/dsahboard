<?php
header('Content-Type: application/json');
require_once 'db.php';

function haalVoldoendesOp($conn, $minimum_voldoende) {
    $sql = "SELECT 
                COUNT(CASE WHEN Behaald = 'Ja' AND formatief = 'Nee' AND score >= $minimum_voldoende THEN 1 END) AS voldoende_behaald
            FROM resultaat";
    $result = $conn->query($sql);
    if (!$result) {
        die(json_encode(array('error' => $conn->error)));
    }

    $data = $result->fetch_assoc();
    return (int)$data['voldoende_behaald'];
}

$totale_examens = 23;
$minimum_voldoende = 5.5;
$conn = connectDatabase();
$voldoende_behaald = haalVoldoendesOp($conn, $minimum_voldoende);

$nog_te_doen = $totale_examens - $voldoende_behaald;
if ($nog_te_doen < 0) {
    $nog_te_doen = 0;
}

if ($voldoende_behaald + $nog_te_doen > $totale_examens) {
    $voldoende_behaald = $totale_examens - $nog_te_doen;
}

$data = array(
    'behaald' => $voldoende_behaald,
    'nog_te_doen' => $nog_te_doen,
    'totaal_examens' => $totale_examens
);

$conn->close();
echo json_encode($data);
