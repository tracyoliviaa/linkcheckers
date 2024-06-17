<?php
require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('local_linkchecker');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_linkchecker'));

global $DB;
$links = $DB->get_records('local_linkchecker_links', array('status' => 0));

if ($links) {
    echo html_writer::start_tag('table', array('class' => 'generaltable'));
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', get_string('course'));
    echo html_writer::tag('th', get_string('url', 'local_linkchecker'));
    echo html_writer::tag('th', get_string('timechecked', 'local_linkchecker'));
    echo html_writer::end_tag('tr');

    foreach ($links as $link) {
        $course = $DB->get_record('course', array('id' => $link->courseid));
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', format_string($course->fullname));
        echo html_writer::tag('td', format_string($link->url));
        echo html_writer::tag('td', userdate($link->timechecked));
        echo html_writer::end_tag('tr');
    }

    echo html_writer::end_tag('table');
} else {
    echo $OUTPUT->notification(get_string('nolinks', 'local_linkchecker'), 'notifymessage');
}

echo $OUTPUT->footer();

