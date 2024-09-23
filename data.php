<?php
header('Content-Type: application/json');
require_once 'db.php';

function haalBehaaldeToetsenPerMaandOp($conn) {
    $sql = "SELECT DATE_FORMAT(datum, '%b') AS maand, COUNT(*) AS aantal
            FROM resultaat
            WHERE (datum >= '2023-08-01' AND datum < '2024-08-01') AND Behaald = 'Ja'
            GROUP BY MONTH(datum)
            ORDER BY MONTH(datum)";

    $result = $conn->query($sql);
    if (!$result) {
        die(json_encode(array('error' => $conn->error)));
    }

    return $result;
}

$conn = connectDatabase();
$result = haalBehaaldeToetsenPerMaandOp($conn);

$maanden = [
    'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 
    'Nov' => 0, 'Dec' => 0, 'Jan' => 0, 
    'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 
    'May' => 0, 'Jun' => 0, 'Jul' => 0
];

while ($row = $result->fetch_assoc()) {
    $maanden[$row['maand']] = (int)$row['aantal'];
}

$conn->close();

$cumulatief = 0;
$formattedData = [];
foreach ($maanden as $maand => $aantal) {
    $cumulatief += $aantal;
    $formattedData[] = ['maand' => $maand, 'aantal' => $cumulatief];
}

echo json_encode($formattedData);
