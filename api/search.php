<?php 

header('Content-Type: application/json');

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/helpers/Db.php');
require_once(__DIR__ . '/helpers/Search.php');

$input = !empty($_GET) ? $_GET : null;

// Exit early if input is invalid
$validInput = !empty($input['q']) && is_array($input['q']) && count($input['q']) > 1;
if (!$validInput) {
    http_response_code(400);
    exit;
}

$totalHits = 0;
$totalHitsByPhrase = [];

// Perform searches and tally totals
$searchSvc = new Search($config['search_engine']);
foreach ($input['q'] as &$phrase) {
    $phrase = substr($phrase, 0, $config['phrase_max_length'] );
    $twitterTotal = $searchSvc->search($phrase, 'twitter.com');
    $redditTotal = $searchSvc->search($phrase, 'reddit.com');
    $total = $twitterTotal + $redditTotal;
    $totalHits += $total;
    $totalHitsByPhrase[] = [
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
if ($totalHits < 1) {
    exit(json_encode(['results' => false]));
}

// Sort totals desc
usort($totalHitsByPhrase, function($a, $b) {
    if ($a['total'] === $b['total']) {
        return 0;
    }
    return ($a['total'] > $b['total']) ? -1 : 1;
});

$response = [];

// Calculate percents
foreach ($totalHitsByPhrase as $t) {
    $response[] = [
        'phrase' => $t['phrase'],
        'percent' => round(($t['total'] / $totalHits) * 100, 2)
    ];
}

exit(json_encode(['results' => $response]));