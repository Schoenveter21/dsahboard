<?php
header('Content-Type: application/json');
require_once 'db.php';

function haalFormatieveScoresOp($conn) {
    $sql = "SELECT 
                SUM(CASE WHEN Behaald = 'Ja' AND formatief = 'Ja' THEN score ELSE 0 END) AS behaalde_score,
                SUM(CASE WHEN formatief = 'Ja' THEN score ELSE 0 END) AS totale_formatieve_score
            FROM resultaat";

    $result = $conn->query($sql);
    if (!$result) {
        die(json_encode(array('error' => $conn->error)));
    }

    return $result->fetch_assoc();
}

$total_formatieve_score = 160;
$conn = connectDatabase();
$scores = haalFormatieveScoresOp($conn);

$behaalde_score = $scores['behaalde_score'] ?? 0;
$nog_te_doen_score = $total_formatieve_score - $behaalde_score;

if ($nog_te_doen_score < 0) {
    $nog_te_doen_score = 0;
}

$behaalde_percentage = ($total_formatieve_score > 0) ? ($behaalde_score / $total_formatieve_score) * 100 : 0;
$nog_te_doen_percentage = ($total_formatieve_score > 0) ? ($nog_te_doen_score / $total_formatieve_score) * 100 : 0;

$total_percentage = $behaalde_percentage + $nog_te_doen_percentage;
if ($total_percentage > 100) {
    $nog_te_doen_percentage = 100 - $behaalde_percentage; 
}

$data = array(
    'behaald' => $behaalde_score,
    'behaald_percentage' => round($behaalde_percentage, 2),
    'nog_te_doen' => $nog_te_doen_score,
    'nog_te_doen_percentage' => round($nog_te_doen_percentage, 2)
);

$conn->close();
echo json_encode($data);
