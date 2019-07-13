<?php

$config = [];

$config['cb'] = 30;

$config['search_engine'] = 'bing';
$config['phrase_max_length'] = 100;
$config['min_phrases'] = 2;
$config['max_phrases'] = 2;

$config['examples'][0] = 'Trump is Smart';
$config['examples'][1] = 'Trump is Dumb';

$config['db']['host'] = 'localhost';
$config['db']['name'] = 'wdps';
$config['db']['user'] = 'homestead';
$config['db']['pass'] = 'secret';

$config['site']['title'] = 'What do people say?';
$config['site']['meta_desc'] = 'Search multiple phrases to see which one do most people say.';
$config['site']['meta_desc_s'] = 'Do most people say {0}';