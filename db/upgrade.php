<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_linkchecker_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024061601) {
        $table = new xmldb_table('local_linkchecker_links');

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024061601, 'local', 'linkchecker');
    }

    return true;
}
