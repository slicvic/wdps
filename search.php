<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/lib/SearchService.php');

$input = !empty($_GET) ? $_GET : null;

// Exit early if input is invalid
$validInput = !empty($input['phrases']) && is_array($input['phrases']) && count($input['phrases']) > 1;
if (!$validInput) {
    http_response_code(400);
    exit;
}

$response = [
    'total' => 0,
    'totalFormatted' => 0,
    'phrases' => []
];

// Perform searches and tally totals
$searchSvc = new SearchService();
foreach ($input['phrases'] as $phrase) {
    $twitterTotal = $searchSvc->search($phrase, 'twitter.com');
    $redditTotal = $searchSvc->search($phrase, 'reddit.com');
    $total = $twitterTotal + $redditTotal;
    $response['total'] += $total;
    $response['phrases'][] = [
        'text' => $phrase,
        'total' => $total
    ];
}

// Exit early if no results
if (empty($response['total'])) {
    exit(json_encode(['no_results' => true]));
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

// Unset deprecated data
unset($response['total'], $response['totalFormatted']);
foreach ($response['phrases'] as &$phrase) {
    unset($phrase['total']);
}

exit(json_encode($response));