<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Paging';
$url = new moodle_url('/admin/tool/elementlibrary/paging.php');

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

echo "<p>Pages 1-7 of 7</p>";
$pages = 7;
$perpage = 100;
$totalcount = $perpage * $pages;
$baseurl = '?';
$pagevar = 'page';
for ($i = 0; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}

echo "<p>Pages 1-20 of 20</p>";
$pages = 20;
$totalcount = $perpage * $pages;
for ($i = 0; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}


echo "<p>Pages 50-60 of 60</p>";
$pages = 60;
$totalcount = $perpage * $pages;
for ($i = 49; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}
echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
