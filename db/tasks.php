<?php
defined('MOODLE_INTERNAL') || die();

$interval = get_config('local_linkchecker', 'checkinterval');
$checktime = get_config('local_linkchecker', 'checktime');
$hour = isset($checktime['h']) ? $checktime['h'] : '2';
$minute = isset($checktime['m']) ? $checktime['m'] : '0';

$tasks = array(
    array(
        'classname' => 'local_linkchecker\task\check_links',
        'blocking' => 0,
        'minute' => $minute,
        'hour' => $hour,
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);
