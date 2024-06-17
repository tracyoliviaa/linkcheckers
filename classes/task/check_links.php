<?php
namespace local_linkchecker\task;

use core\task\scheduled_task;

defined('MOODLE_INTERNAL') || die();

class check_links extends scheduled_task {

    public function get_name() {
        return get_string('checklinks', 'local_linkchecker');
    }

    public function execute() {
        global $DB;

        $courses = $DB->get_records('course');
        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course);
            foreach ($modinfo->cms as $cm) {
                $content = $cm->get_content();
                preg_match_all('/href="([^"]+)"/', $content, $matches);
                $urls = array_unique($matches[1]);

                foreach ($urls as $url) {
                    $status = $this->check_url($url);
                    $record = new \stdClass();
                    $record->courseid = $course->id;
                    $record->url = $url;
                    $record->status = $status;
                    $record->timechecked = time();

                    $existing = $DB->get_record('local_linkchecker_links', array('courseid' => $course->id, 'url' => $url));
                    if ($existing) {
                        $record->id = $existing->id;
                        $DB->update_record('local_linkchecker_links', $record);
                    } else {
                        $DB->insert_record('local_linkchecker_links', $record);
                    }

                    if ($status == 0) {
                        local_linkchecker_send_notification($course->id, $url);
                    }
                }
            }
        }
    }

    public function check_url($url) {
        if (preg_match('/youtu\.be\/([^\?\/]+)/', $url, $matches) || preg_match('/youtube\.com\/watch\?v=([^\&]+)/', $url, $matches)) {
            return $this->check_youtube_video($matches[1]);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpcode >= 200 && $httpcode < 400) ? 1 : 0;
    }

    private function check_youtube_video($video_id) {
        $api_key = get_config('local_linkchecker', 'youtubeapikey');
        $url = "https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$api_key&part=status";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        return isset($data['items']) && !empty($data['items']) ? 1 : 0;
    }
}
