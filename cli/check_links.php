<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params(array(
    'help' => false,
), array(
    'h' => 'help',
));

if ($options['help']) {
    $help = "Execute the link checker manually.

Options:
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php local/linkchecker/cli/check_links.php
";
    echo $help;
    die;
}

$task = new \local_linkchecker\task\check_links();
$task->execute();

cli_writeln("Link checker executed successfully.");
