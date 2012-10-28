<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library';
$url = new moodle_url('/admin/tool/elementlibrary/index.php');

// Start setting up the page
$params = array();
$PAGE->set_context(get_system_context());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('toolelementlibrary');

echo $OUTPUT->header();

echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();
echo $OUTPUT->container('This page contains a set of sample elements used on this site. It can be used to ensure that everything has been correctly themed (remember to check in a right-to-left language too), and for developers to see examples of how to implement particular elements. Developers: if you need an element that is not represented here, add it here first - the idea is to build up a library of all the elements used across the site.');

echo $OUTPUT->container_start();
echo $OUTPUT->heading('Moodle elements', 3);
echo html_writer::start_tag('ul');
echo html_writer::tag('li', html_writer::link(new moodle_url('headings.php'), 'Headings'));
echo html_writer::tag('li', html_writer::link(new moodle_url('common.php'), 'Common tags'));
echo html_writer::tag('li', html_writer::link(new moodle_url('lists.php'), 'Lists'));
echo html_writer::tag('li', html_writer::link(new moodle_url('tables.php'), 'Tables'));
echo html_writer::tag('li', html_writer::link(new moodle_url('forms.php'), 'Form elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('mform.php'), 'Moodle form elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('tabs.php'), 'Moodle tab bar elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('images.php'), 'Images'));
echo html_writer::tag('li', html_writer::link(new moodle_url('notifications.php'), 'Notifications'));
echo html_writer::tag('li', html_writer::link(new moodle_url('pagelayouts.php'), 'Page Layouts'));
echo html_writer::end_tag('ul');
echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
