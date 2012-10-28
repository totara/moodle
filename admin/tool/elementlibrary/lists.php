<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Lists';
$url = new moodle_url('/admin/tool/elementlibrary/lists.php');

// Start setting up the page
$params = array();
$PAGE->set_context(get_system_context());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('toolelementlibrary');

echo $OUTPUT->header();

echo html_writer::link(new moodle_url('index.php'), '&laquo; Back to index');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();

echo $OUTPUT->container_start();
echo $OUTPUT->heading('Unordered List', 3);
$items = array('item one', 'item two', 'item three', 'item four', 'item five');
echo html_writer::alist($items);

echo $OUTPUT->heading('Ordered List', 3);
echo html_writer::alist($items, null, 'ol');

echo $OUTPUT->heading('Nested unordered List', 3);
$subitems = array('item two point one', 'item two point two');
$sublist = html_writer::alist($subitems);
$items = array('item one', "item two\n$sublist", 'item three', 'item four', 'item five');
echo html_writer::alist($items);


echo $OUTPUT->heading('Nested ordered List', 3);
$sublist = html_writer::alist($subitems, null, 'ol');
$items = array('item one', "item two\n$sublist", 'item three', 'item four', 'item five');
echo html_writer::alist($items, null, 'ol');

echo $OUTPUT->heading('Deeply nested unordered List', 3);

echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 1');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 2');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 3');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 4');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 5');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 6');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 7');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 8');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 9');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'level 10');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');
echo html_writer::end_tag('ul');

echo $OUTPUT->heading('Deeply nested ordered List', 3);

echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 1');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 2');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 3');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 4');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 5');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 6');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 7');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 8');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 9');
echo html_writer::start_tag('ol');
echo html_writer::tag('li', 'level 10');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');
echo html_writer::end_tag('ol');

echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
