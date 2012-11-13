<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Forms';
$url = new moodle_url('/admin/tool/elementlibrary/forms.php');

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
echo $OUTPUT->container('Examples of different types of stand-alone form elements (without using formslib).');
echo $OUTPUT->container_start();

echo html_writer::tag('p', '');

echo html_writer::tag('p', 'To render a single checkbox:');

$attr = array(); // other attributes;
echo html_writer::checkbox('name', 'value', true, 'Checkbox label', $attr);

echo html_writer::tag('p', 'To render a single yes/no select:');

echo html_writer::select_yes_no('name2', true, $attr);

echo html_writer::tag('p', 'To render a normal single select:');

$options = array(1 => 'One', 2 => 'Two', 3 => 'Three');
echo html_writer::select($options, 'name3', 1, array('' => 'choosedots'), $attr);

echo html_writer::tag('p', 'Or a single select with option grouping:');

$optoptions = array(array('Group 1' => array(1 => 'Option 1a', 2 => 'Option 1b', 3 => 'Option 1c')), array('Group 2' => array(4 => 'Option 2a', 5 => 'Option 2b', 6 => 'Option 2c')));
echo html_writer::select($optoptions, 'name4');

echo html_writer::tag('p', 'To render a time selector - you can choose the units (years, months, days, hours, minutes):');

echo html_writer::select_time('years', 'name5', time(), 5, $attr);

echo html_writer::tag('p', 'Generate a hidden input field for every parameter in a moodle_url object (output displayed below instead of rendered):');

$url = new moodle_url('index.php', array('a' => 1, 'b' => 2, 'exclude' => 'this'));
echo html_writer::tag('pre', htmlentities(html_writer::input_hidden_params($url, array('exclude'))));

echo html_writer::tag('p', 'Generate a script tag (output displayed below instead of rendered):');

echo html_writer::tag('pre', htmlentities(html_writer::script('alert(\'hi\');')));

echo html_writer::tag('p', 'Generate a form label:');

echo html_writer::label('Label text', 'checkbox1');
echo html_writer::checkbox('name', 'value', false, null, array('id' => 'checkbox1'));

echo html_writer::tag('p', 'A confirm form with continue/cancel options (just providing urls to go to):');

$continueurl = new moodle_url('index.php');
$cancelurl = new moodle_url('index.php');
echo $OUTPUT->confirm('This is the message', $continueurl, $cancelurl);

echo html_writer::tag('p', 'A confirm form with continue/cancel options (with custom buttons):');

$continueurl = new moodle_url('index.php');
$cancelurl = new moodle_url('index.php');
$continuebutton = new single_button($continueurl, 'Custom Button text', 'post');
$cancelbutton = new single_button($cancelurl, 'Something else', 'get');
echo $OUTPUT->confirm('This is another message', $continuebutton, $cancelbutton);

echo html_writer::tag('p', 'A standalone single button. This is still wrapped in a form so you can submit it. There are a couple of ways to generate, via a function call:');

echo $OUTPUT->single_button($continueurl, 'A disabled button', 'post', array('disabled' => true));

echo html_writer::tag('p', 'Or manually create object then render. Note this uses a confirm dialog, try pressing to see popup (needs styling)');
// render directly
$button = new single_button($continueurl, 'Manually rendered button', 'post');
$button->tooltip = 'This is the tooltip';
$button->formid = 'canbeset';
$button->class = 'classonbutton';
$button->add_confirm_action('This message appears to confirm when you push the button');
echo $OUTPUT->render($button);

echo html_writer::tag('p', 'A single select form. Quick way using a function:');

$url = new moodle_url('index.php', array('urlparams' => 'become', 'hidden' => 'fields'));
$options = array(1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four');
echo $OUTPUT->single_select($url, 'youpicked', $options, '', array('' => 'choose'), 'formid');

echo html_writer::tag('p', 'A single select form. Manually rendered:');

$select = new single_select($url, 'youpicked', $options);
$select->set_label('This is a label for the select');
$select->tooltip = 'This is the tooltip';
//this doesn't seem to work - moodle bug?
//$select->add_confirm_action('Confirm you want to do this');
$select->set_help_icon('activities', 'moodle');
echo $OUTPUT->render($select);

echo html_writer::tag('p', 'A url select form. Typically used for navigation.');

$urls = array(
    '/admin/tool/elementlibrary/' => 'Index',
    '/admin/tool/elementlibrary/common.php' => 'Common elements',
    '/admin/tool/elementlibrary/mform.php' => 'Moodle form elements',
    '/admin/tool/elementlibrary/tables.php' => 'Tables',
    '/admin/tool/elementlibrary/tabs.php' => 'Tabs',
);

echo $OUTPUT->url_select($urls, '', array('' => 'choose'), 'formid');

/*
 * This never loads and isn't called this way anywhere else?
echo html_writer::tag('p', 'A stand-alone file picker:');
$options = new stdClass();
echo $OUTPUT->file_picker($options);
 */

// get any cmid to make this work
$cmid = $DB->get_field('course_modules', 'id', array(), IGNORE_MULTIPLE);
$modulename = $DB->get_field('modules', 'name', array(), IGNORE_MULTIPLE);
if ($cmid) {
    echo html_writer::tag('p', 'An "update module" button. The module name is the value from the modules table e.g. "workshop". The button won\'t be shown if the user doesn\'t have the capability to manage that activity.');
    echo $OUTPUT->update_module_button($cmid, $modulename);
}

echo html_writer::tag('p', 'An "editing on/off" button. This automatically sends through the appropriate "edit" param and toggles the text.');
$url = new moodle_url('forms.php', array('params' => 'to', 'send' => 'through'));
echo $OUTPUT->edit_button($url);

echo html_writer::tag('p', 'A "close window" button. To be used in a YUI popup dialog, not normally as part of a page. Automatically includes the close_window action:');

echo $OUTPUT->close_window_button('Close button text');

echo html_writer::tag('p', 'A "continue" button. You can specify the URL to go to when pressed:');

$url = new moodle_url('index.php');
echo $OUTPUT->continue_button($url);

echo html_writer::tag('p', '');

echo $OUTPUT->container_start();
echo html_writer::tag('p', 'By default we remove the full border from the fieldset element and show the legend as a title like this:');
echo html_writer::start_tag('fieldset');
echo html_writer::tag('legend', 'Legend');
echo html_writer::tag('div', 'This is a standard fieldset');
echo html_writer::end_tag('fieldset');

echo html_writer::tag('p', 'If you want an enclosed fieldset (with borders on all sides) use the class "surround" on the fieldset element like this:');
echo html_writer::start_tag('fieldset', array('class' => 'surround'));
echo html_writer::tag('legend', 'Legend');
echo html_writer::tag('div', 'This is a fieldset with the "surround" class applied');
echo html_writer::end_tag('fieldset');
echo $OUTPUT->container_end();

echo $OUTPUT->container_end();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
