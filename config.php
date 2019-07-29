<?php

$config = [];

// Assets cache buster
$config['cb'] = 31;

$config['search_engine'] = 'bing';
$config['phrase_max_length'] = 100;
$config['min_phrases'] = 2;
$config['max_phrases'] = 2;

$config['examples'][0] = 'Trump&nbsp;is&nbsp;Smart';
$config['examples'][1] = 'Trump&nbsp;is&nbsp;Dumb';

$config['db']['host'] = 'localhost';
$config['db']['name'] = 'wdps';
$config['db']['user'] = 'homestead';
$config['db']['pass'] = 'secret';

$config['site']['title'] = 'What do people say?';
$config['site']['meta_desc'] = "Search multiple phrases and see which one is more popular. For example, do most people say 'Trump is Smart' or 'Trump is Dumb'";
$config['site']['meta_desc_q'] = '{0}?';