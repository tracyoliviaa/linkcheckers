<?php
defined('MOODLE_INTERNAL') || die();

use local_linkchecker\task\check_links;

class local_linkchecker_check_links_test extends advanced_testcase {

    protected function setUp(): void {
        $this->resetAfterTest(true);
    }

    public function test_check_url_valid() {
        $task = new check_links();
        $url = 'http://example.com';
        $status = $task->check_url($url);
        $this->assertEquals(1, $status);
    }

    public function test_check_url_invalid() {
        $task = new check_links();
        $url = 'http://invalidurl.example';
        $status = $task->check_url($url);
        $this->assertEquals(0, $status);
    }

    public function test_check_youtube_video_valid() {
        $task = new check_links();
        $video_id = 'dQw4w9WgXcQ';
        $status = $task->check_youtube_video($video_id);
        $this->assertEquals(1, $status);
    }

    public function test_check_youtube_video_invalid() {
        $task = new check_links();
        $video_id = 'invalidvideoid';
        $status = $task->check_youtube_video($video_id);
        $this->assertEquals(0, $status);
    }

    public function test_execute() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        $url1 = $this->getDataGenerator()->create_module('url', array(
            'course' => $course->id,
            'externalurl' => 'http://example.com'
        ));

        $url2 = $this->getDataGenerator()->create_module('url', array(
            'course' => $course->id,
            'externalurl' => 'http://invalidurl.example'
        ));

        $task = new check_links();
        $task->execute();

        $links = $DB->get_records('local_linkchecker_links');
        $this->assertCount(2, $links);

        foreach ($links as $link) {
            if ($link->url == 'http://example.com') {
                $this->assertEquals(1, $link->status);
            } else {
                $this->assertEquals(0, $link->status);
            }
        }
    }

    public function test_notification_sent_for_broken_links() {
        global $DB, $CFG;

        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        $adminuser = get_admin();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $url = $this->getDataGenerator()->create_module('url', array(
            'course' => $course->id,
            'externalurl' => 'http://invalidurl.example'
        ));

        $sink = $this->redirectEmails();

        $task = new check_links();
        $task->execute();

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $this->assertEquals('Broken link found in course ' . $course->fullname, $messages[0]->subject);

        $sink->close();
    }

    public function test_dummy() {
        $this->assertTrue(true);
    }
}
