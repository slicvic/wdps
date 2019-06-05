<?php

$config = [];

$config['base_url'] = 'http://whatdopeoplesay.com';
$config['cb'] = 20;

$config['site']['name'] = 'What Do People Say';
$config['site']['desc'] = 'Search multiple phrases and see what people say the most';

$config['search_engine'] = 'bing';
$config['phrase_max_length'] = 100;
$config['min_phrases'] = 2;
$config['max_phrases'] = 3;

$config['db']['host'] = 'localhost';
$config['db']['name'] = 'wdps';
$config['db']['user'] = 'homestead';
$config['db']['pass'] = 'secret';