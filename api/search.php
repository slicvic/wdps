<?php 

header('Content-Type: application/json');

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/helpers/DbHelper.php');
require_once(__DIR__ . '/helpers/SearchHelper.php');
require_once(__DIR__ . '/helpers/UrlHelper.php');

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
$total = 0;
$totalByPhrase = [];
$searchHelper = new SearchHelper($config['search_engine']);

foreach ($input['q'] as &$phrase) {
    $phrase = substr($phrase, 0, $config['phrase_max_length'] );
    $phraseTotal = $searchHelper->getResultCount($phrase);
    $total += $phraseTotal;
    $totalByPhrase[] = [
        'phrase' => $phrase,
        'total' => $phraseTotal
    ];
}

// Log search
try {
    $dbHelper = new DbHelper($config['db']['host'], $config['db']['name'], $config['db']['user'], $config['db']['pass']);
    $dbHelper->logSearch(
        implode(', ', $input['q']), 
        !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
        !empty($input['referer']) ? $input['referer'] : ''
    );
} catch (Exception $e) {}

// Exit early if no results
if ($total < 1) {
    exit(json_encode(['results' => false]));
}

// Sort totals desc
usort($totalByPhrase, function($a, $b) {
    if ($a['total'] === $b['total']) {
        return 0;
    }
    return ($a['total'] > $b['total']) ? -1 : 1;
});

// Prepare response
$urlHelper = new UrlHelper();

$response = [];
$response['share_url'] = $urlHelper->createShareUrl($input['q']);
$response['results'] = [];

// Calculate percents
foreach ($totalByPhrase as $t) {
    $response['results'][] = [
        'phrase' => $t['phrase'],
        'percent' => round(($t['total'] / $total) * 100, 2)
    ];
}

exit(json_encode($response));