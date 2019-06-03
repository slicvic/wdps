<?php 

header('Content-Type: application/json');

require_once(__DIR__ . '/lib/SearchService.php');

define('SEARCH_ENGINE', 'bing');

define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

$input = !empty($_GET) ? $_GET : null;

// Exit early if input is invalid
$validInput = !empty($input['phrases']) && is_array($input['phrases']) && count($input['phrases']) > 1;
if (!$validInput) {
    http_response_code(400);
    exit;
}

$totalHits = 0;
$totalHitsByPhrase = [];

// Perform searches and tally totals
$searchSvc = new SearchService(SEARCH_ENGINE);
foreach ($input['phrases'] as $phrase) {
    $twitterTotal = $searchSvc->search($phrase, 'twitter.com');
    $redditTotal = $searchSvc->search($phrase, 'reddit.com');
    $total = $twitterTotal + $redditTotal;
    $totalHits += $total;
    $totalHitsByPhrase[] = [
        'phrase' => $phrase,
        'total' => $total
    ];
}

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

try {
    $pdo = new PDO('mysql:host=' .DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->prepare('INSERT INTO searches SET q = :q, ip = :ip, dt = :dt')
        ->execute([
            'q'  => json_encode($input['phrases']),
            'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'dt' => date('Y-m-d H:i:s')
        ]);
} catch (Exception $e) {}

exit(json_encode(['results' => $response, 'x' => $_SERVER]));