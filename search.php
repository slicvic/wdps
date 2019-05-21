<?php
header('Content-Type: application/json');

$input = !empty($_GET) ? $_GET : null;

// Exit early if input is invalid
$validInput = !empty($input['phrases']) && is_array($input['phrases']) && count($input['phrases']) > 1;
if (!$validInput) {
    http_response_code(400);
    exit;
}

function googleSearch($q) {
    $url = 'https://www.google.com/search?q=' . urlencode($q);
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
    $response = curl_exec($ch);
    curl_close($ch); 
    preg_match('/About ([0-9,]+) results/', $response, $matches, PREG_OFFSET_CAPTURE);
    if (!empty($matches[1][0])) {
        return preg_replace('/[^0-9]/', '', $matches[1][0]);
    }
    return 0;
}

$response = [
    'total' => 0,
    'totalFormatted' => 0,
    'phrases' => []
];

// Perform searches and tally totals
foreach ($input['phrases'] as $phrase) {
    $twitterTotal = googleSearch('site:twitter.com "' . $phrase . '" ');
    $redditTotal = googleSearch('site:reddit.com "' . $phrase . '"');
    $total = $twitterTotal + $redditTotal;
    $response['total'] += $total;
    $response['phrases'][] = [
        'text' => $phrase,
        'total' => $total
    ];
}

// Exit early if no results
if (empty($response['total'])) {
    exit(json_encode($response));
}

// Format total
$response['totalFormatted'] = number_format($response['total']);

// Calculate percents
foreach ($response['phrases'] as &$phrase) {
    $phrase['percent'] = round(($phrase['total'] / $response['total']) * 100, 2);
}

// Sort results
usort($response['phrases'], function($a, $b) {
    if ($a['total'] === $b['total']) {
        return 0;
    }
    return ($a['total'] > $b['total']) ? -1 : 1;
});

exit(json_encode($response));