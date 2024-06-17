<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $settings = new admin_settingpage('local_linkchecker', get_string('pluginname', 'local_linkchecker'));

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_linkchecker/checkinterval',
        get_string('checkinterval', 'local_linkchecker'),
        get_string('checkinterval_desc', 'local_linkchecker'),
        '24',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtime(
        'local_linkchecker/checktime',
        get_string('checktime', 'local_linkchecker'),
        get_string('checktime_desc', 'local_linkchecker'),
        array('h' => 2, 'm' => 0)
    ));

    $settings->add(new admin_setting_configtext(
        'local_linkchecker/youtubeapikey',
        get_string('youtubeapikey', 'local_linkchecker'),
        get_string('youtubeapikey_desc', 'local_linkchecker'),
        '',
        PARAM_ALPHANUMEXT
    ));
}

$ADMIN->add('reports', new admin_externalpage('local_linkchecker_report', get_string('report', 'local_linkchecker'), new moodle_url('/local/linkchecker/report.php')));
