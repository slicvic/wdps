<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/api/helpers/UrlHelper.php');

$urlHelper = new UrlHelper();

$phrases = array_slice($urlHelper->decodeCurrentSearchQuery(), 0, $config['max_phrases']);

if (count($phrases) >= $config['min_phrases']) {
    $meta_desc = 'Do more people say ' .  "'" . implode("' or '", $phrases) . "'?";
} else {
    $meta_desc = $config['site']['meta_desc'];
}