<?php
defined('MOODLE_INTERNAL') || die();

function local_linkchecker_send_notification($courseid, $url) {
    global $DB;

    $admins = get_admins();
    $course = $DB->get_record('course', array('id' => $courseid));
    $subject = get_string('brokenlinkfound', 'local_linkchecker', format_string($course->fullname));
    $message = get_string('brokenlinkmessage', 'local_linkchecker', (object)[
        'coursename' => format_string($course->fullname),
        'url' => $url,
        'linkcheckerurl' => new moodle_url('/local/linkchecker/index.php')
    ]);

    foreach ($admins as $admin) {
        email_to_user($admin, core_user::get_support_user(), $subject, $message);
    }
}
