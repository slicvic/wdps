<?php 

header('Content-Type: application/json');

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/helpers/Db.php');
require_once(__DIR__ . '/helpers/Search.php');

$input = isset($_GET) ? $_GET : null;

// Exit early if input is not valid
$validInput = isset($input['q']) && is_array($input['q']) && count($input['q']) >= $config['min_phrases'];
if (!$validInput) {
    http_response_code(400);
    exit;
}

// Limit phrases
$input['q'] = array_slice($input['q'], 0, $config['max_phrases']);

// Perform searches and tally totals
$totalResults = 0;
$totalResultsByPhrase = [];
$searchSvc = new Search($config['search_engine']);

foreach ($input['q'] as &$phrase) {
    $phrase = substr($phrase, 0, $config['phrase_max_length'] );
    $twitterTotal = $searchSvc->search($phrase, 'twitter.com');
    $redditTotal = $searchSvc->search($phrase, 'reddit.com');
    $total = $twitterTotal + $redditTotal;
    $totalResults += $total;
    $totalResultsByPhrase[] = [
        'phrase' => $phrase,
        'total' => $total
    ];
}

// Log search
try {
    $db = new Db($config['db']['host'], $config['db']['name'], $config['db']['user'], $config['db']['pass']);
    $db->logSearch(
        json_encode($input['q']), 
        isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
    );
} catch (Exception $e) {}

// Exit early if no results
if ($totalResults < 1) {
    exit(json_encode(['results' => false]));
}

// Sort totals desc
usort($totalResultsByPhrase, function($a, $b) {
    if ($a['total'] === $b['total']) {
        return 0;
    }
    return ($a['total'] > $b['total']) ? -1 : 1;
});

$response = [];

// Calculate percents
foreach ($totalResultsByPhrase as $t) {
    $response[] = [
        'phrase' => $t['phrase'],
        'percent' => round(($t['total'] / $totalResults) * 100, 2)
    ];
}

exit(json_encode(['results' => $response]));