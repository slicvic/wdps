<?php
header('Content-Type: application/json');

function getGoogleHitCount($q) {
    $url = 'https://www.google.com/search?q="' . urlencode($q) . '"';
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $res = curl_exec($ch);
    curl_close($ch); 
    preg_match('/About ([0-9,]+) results/', $res, $matches, PREG_OFFSET_CAPTURE);
    if (!empty($matches[1][0])) {
        return preg_replace('/[^0-9]/', '', $matches[1][0]);
    }
    return 0;
}

if (!empty($_GET['q']) && is_array($_GET['q'])) {
    $res = [];

    foreach ($_GET['q'] as $q) {
        $hits = getGoogleHitCount($q);
        $res[] = [
            'q' => $q,
            'hits' => $hits,
            'hitsFormatted' => number_format($hits)
        ];
    }

    usort($res, function($a, $b) {
        if ($a['hits'] === $b['hits']) {
            return 0;
        }
        return ($a['hits'] > $b['hits']) ? -1 : 1;
    });

    echo json_encode($res);
}

exit;