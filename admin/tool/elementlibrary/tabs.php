<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Tab bars';
$url = new moodle_url('/admin/tool/elementlibrary/tabs.php');

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

$tabs = array();
$row = array();
$inactive = array();
$activated = array();

echo $OUTPUT->box('Standard, single row tab bar');

$url = new moodle_url('index.php');
$row[] = new tabobject('tab1',
    $url->out(),
    'Selected Tab',
    'This is the hover text for tab1'
);

$row[] = new tabobject('tab2',
    $url->out(),
    'Another tab',
    'This is the hover text for tab2'
);

$row[] = new tabobject('tab3',
    $url->out(),
    'Yet Another tab',
    'This is the hover text for tab3'
);

$row[] = new tabobject('tab4',
    $url->out(),
    'More tabs',
    'This is the hover text for tab4'
);

$row[] = new tabobject('tab5',
    $url->out(),
    'Even more tabs',
    'This is the hover text for tab5'
);

$current_tab = 'tab1';
$tabs[] = $row;
print_tabs($tabs, $current_tab, $inactive, $activated);
echo html_writer::tag('div', 'This is a div tag directly under the tab bar. Make sure the padding is correct. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu accumsan nulla. Cras elementum tincidunt dictum. Phasellus varius, est non ornare mattis, leo velit congue libero, vitae suscipit ipsum urna sed orci. Pellentesque venenatis pulvinar lobortis. Vestibulum iaculis commodo eros quis volutpat. Morbi vitae dapibus ante. Nullam convallis interdum ipsum, venenatis consequat eros faucibus sed. Pellentesque non tellus vel eros ullamcorper sollicitudin ut in lectus. Sed aliquet gravida porta.');

echo $OUTPUT->box('Same as above, but with a couple of disabled tabs');

print_tabs($tabs, $current_tab, array('tab4', 'tab5'), $activated);

echo $OUTPUT->box('Same as above, but with a couple of extra activated tabs');

print_tabs($tabs, $current_tab, $inactive, array('tab4', 'tab5'));

echo $OUTPUT->box('You can set a flag on the tab object to keep the active tab as a link, though this doesn\'t affect additional selected tabs, just the current tab.');

$row = array();
$url = new moodle_url('index.php');
$row[] = new tabobject('tab1',
    $url->out(),
    'Selected Tab',
    'This is the hover text for tab1',
    true
);

$row[] = new tabobject('tab2',
    $url->out(),
    'Another tab',
    'This is the hover text for tab2',
    true
);

$row[] = new tabobject('tab3',
    $url->out(),
    'Yet Another tab',
    'This is the hover text for tab3',
    true
);

$row[] = new tabobject('tab4',
    $url->out(),
    'Activated tab',
    'This is the hover text for tab4',
    true
);

$row[] = new tabobject('tab5',
    $url->out(),
    'Another activated tab',
    'This is the hover text for tab5',
    true
);

$tabs2 = array($row);
print_tabs($tabs2, $current_tab, $inactive, array('tab4', 'tab5'));

echo $OUTPUT->box('Extra rows can be used to hold further subcategories.');

$row1 = array(
    new tabobject('row1a', $url->out(), 'Activated Row 1 Tab A'),
    new tabobject('row1b', $url->out(), 'Row 1 Tab B'),
    new tabobject('row1c', $url->out(), 'Disabled Row 1 Tab C'),
);
$row2 = array(
    new tabobject('row2a', $url->out(), 'Row 2 Tab A'),
    new tabobject('row2b', $url->out(), 'Selected Row 2 Tab B'),
    new tabobject('row2c', $url->out(), 'Row 2 Tab C'),
);
$tabs = array();
$tabs[] = $row1;
$tabs[] = $row2;
$current_tab = 'row2b';
$inactive = array('row1c');
$activated = array('row1a');
print_tabs($tabs, $current_tab, $inactive, $activated);


echo $OUTPUT->box('Extra rows can be used to hold further subcategories. You can nest as many levels of tabs as you like but for Totara please stick to a maximum of two levels, any more will be confusing anyway. Note that while each row is specified independently in the code, the HTML that is rendered nests the list items hierarchically, so the activated tabs on each row contains the list items for the level below.');

$row1 = array(
    new tabobject('row1a', $url->out(), 'Activated Row 1 Tab A'),
    new tabobject('row1b', $url->out(), 'Row 1 Tab B'),
    new tabobject('row1c', $url->out(), 'Disabled Row 1 Tab C'),
);
$row2 = array(
    new tabobject('row2a', $url->out(), 'Row 2 Tab A'),
    new tabobject('row2b', $url->out(), 'Selected Row 2 Tab B'),
    new tabobject('row2c', $url->out(), 'Row 2 Tab C'),
);
$tabs = array();
$tabs[] = $row1;
$tabs[] = $row2;
$current_tab = 'row2b';
$inactive = array('row1c');
$activated = array('row1a');
print_tabs($tabs, $current_tab, $inactive, $activated);
echo html_writer::tag('div', 'This is a div tag directly under a two row tab bar. Make sure the padding is correct. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu accumsan nulla. Cras elementum tincidunt dictum. Phasellus varius, est non ornare mattis, leo velit congue libero, vitae suscipit ipsum urna sed orci. Pellentesque venenatis pulvinar lobortis. Vestibulum iaculis commodo eros quis volutpat. Morbi vitae dapibus ante. Nullam convallis interdum ipsum, venenatis consequat eros faucibus sed. Pellentesque non tellus vel eros ullamcorper sollicitudin ut in lectus. Sed aliquet gravida porta.');


echo $OUTPUT->box('Sometimes the tab bars can get very full, especially when translated into other languages. We need to ensure they are at least readable when the tabs are too wide for the page.');

$row1 = array(
    new tabobject('row1a', $url->out(), 'Row 1 Tab A. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row1b', $url->out(), 'Row 1 Tab B. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row1c', $url->out(), 'Row 1 Tab C. This is a really long title. This is a really long title. This is a really long title.'),
);
$row2 = array(
    new tabobject('row2a', $url->out(), 'Row 2 Tab A. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row2b', $url->out(), 'Row 2 Tab B. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row2c', $url->out(), 'Row 2 Tab C. This is a really long title. This is a really long title. This is a really long title.'),
);
$tabs = array();
$tabs[] = $row1;
$tabs[] = $row2;
$current_tab = 'row2b';
$inactive = array('row1c');
$activated = array('row1a');
print_tabs($tabs, $current_tab, $inactive, $activated);
echo html_writer::tag('div', 'This is a div tag directly under an overflowing two row tab bar. Make sure the padding is correct. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu accumsan nulla. Cras elementum tincidunt dictum. Phasellus varius, est non ornare mattis, leo velit congue libero, vitae suscipit ipsum urna sed orci. Pellentesque venenatis pulvinar lobortis. Vestibulum iaculis commodo eros quis volutpat. Morbi vitae dapibus ante. Nullam convallis interdum ipsum, venenatis consequat eros faucibus sed. Pellentesque non tellus vel eros ullamcorper sollicitudin ut in lectus. Sed aliquet gravida porta.');

echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
