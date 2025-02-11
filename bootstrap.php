<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/api/helpers/UrlHelper.php');

$urlHelper = new UrlHelper();

$phrases = array_slice($urlHelper->decodeCurrentSearchQuery(), 0, $config['max_phrases']);

if (count($phrases) >= $config['min_phrases']) {
    define('META_DESC', str_replace('{0}', "'" . implode("' or '", $phrases) . "'", $config['site']['meta_desc_q']));
} else {
    define('META_DESC', $config['site']['meta_desc']);
}