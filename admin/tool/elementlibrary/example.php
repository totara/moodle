<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');

$strheading = 'Element Library: Example page';
$url = new moodle_url('/admin/tool/elementlibrary/example.php');


// Start setting up the page
$params = array();
$PAGE->set_context(get_system_context());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

$PAGE->requires->css('/admin/tool/elementlibrary/google-code-prettify/prettify.css');
$PAGE->requires->js('/admin/tool/elementlibrary/google-code-prettify/prettify.js');
$PAGE->requires->js_init_call('M.tool_elementlibrary.prettyprint');

admin_externalpage_setup('toolelementlibrary');
echo $OUTPUT->header();

echo html_writer::link(new moodle_url('index.php'), '&laquo; Back to index');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box('This is an example of how we could use a single <a href="example.txt">source file</a> to display the code for the developer, an example of the output, and the html source.');

echo $OUTPUT->container('<p>This is the code you should use to create a top level heading:</p>');

echo '<pre class="prettyprint linenums lang-php">';
echo get_html_output('example.txt');
echo '</pre>';

echo $OUTPUT->container('<p>Which will look like this:</p>');

echo '<div class="docs-example">';
include('example.txt');
echo '</div>';

echo $OUTPUT->container('<p>and here is the HTML output that is generated:</p>');

echo '<pre class="prettyprint linenums lang-html">';
echo get_source_output('example.txt');
echo '</pre>';


echo $OUTPUT->footer();
