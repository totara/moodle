<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Images';
$url = new moodle_url('/admin/tool/elementlibrary/images.php');

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

echo html_writer::tag('p', 'You can build images manually using pix_url() to get the image path, but you have to specify all the info manually (including width, height and alt attributes):');

$attr = array(
    'src' => $OUTPUT->pix_url('moodlelogo'),
    'alt' => 'Moodle logo',
    'width' => 100,
    'height' => 30,
);
echo html_writer::empty_tag('img', $attr);

echo html_writer::tag('p', 'If you are creating icons which use the standard (16x16px) or small (11x11px) icon sizes, then just set the CSS class instead to "icon" or "iconsmall".');

$attr = array(
    'src' => $OUTPUT->pix_url('i/risk_xss'),
    'alt' => 'XSS Risk',
    'class' => 'icon',
);
echo '16x16 icon: ' . html_writer::empty_tag('img', $attr);

$attr = array(
    'src' => $OUTPUT->pix_url('t/delete'),
    'alt' => 'Delete',
    'class' => 'iconsmall',
);
echo '11x11 icon: ' . html_writer::empty_tag('img', $attr);

echo html_writer::tag('p', 'Generally the component should be "moodle" for existing moodle images, "totara_core" for core totara images, "totara_modname" for module specfic images. It is possible to specify a theme image directly, but this should be avoided unless the image is only used by the theme - normally the theme would just override the core or module image if required');

echo html_writer::tag('p', 'Instead of building the image tag manually, you can render a pix_icon object');

$attr = array('width' => 11, 'height' => 11); // any extra attributes you want
$icon = new pix_icon('t/edit', 'Edit', 'moodle', $attr);
echo $OUTPUT->render($icon);

echo html_writer::tag('p', 'Or you can call the helper method to just render the pix_icon directly');
echo $OUTPUT->pix_icon('t/edit', 'Edit', 'moodle', $attr);

echo html_writer::tag('p', 'This will set the title to be the same as the alt text, and by default assign the "smallicon" class unless you set any other class via the attributes.');

echo html_writer::tag('p', 'If you want to create a linked icon use $OUTPUT->action_icon(). Note that action_icon requires a pix_icon object, not the rendered string so make sure the second argument is "new pix_icon()" not $OUTPUT->pix_icon().');

$url = new moodle_url('index.php');
$icon = new pix_icon('t/add', 'Add');
echo $OUTPUT->action_icon($url, $icon);

echo html_writer::tag('p', 'action_icon() takes an option linktext boolean argument for putting the alt text next to the icon like this. You can also trigger javascript actions using the component_action argument.');
$url = new moodle_url('index.php');
$icon = new pix_icon('t/add', 'Add');
echo $OUTPUT->action_icon($url, $icon, null, null, true);

echo html_writer::tag('p', 'If you want a spacer (for when an icon is not being shown) use $OUTPUT->spacer(). Remember to specify the width and height or it will only be 1x1px. You can\'t set a class as it\'s overridden in the method.');

echo $OUTPUT->pix_icon('t/removeright', '>') . $OUTPUT->spacer(array('width' => 11, 'height' => 11)) . $OUTPUT->pix_icon('t/moveleft', '<');

echo html_writer::tag('p', 'There is a special class for rendering emoticons. All it does dfiferently is set "emoticon" as the default class.');

$emoticon = new pix_emoticon('s/smiley', 'This is a smiley', 'core');
echo $OUTPUT->render($emoticon);

echo html_writer::tag('p', 'To render a user picture use the user_picture() function or render a user_picture object. There are various options around linking, popups, size, etc. See user_picture docs:');

$user = $DB->get_record('user', array('id' => 2));
echo $OUTPUT->user_picture($user, array('popup' => true));

echo html_writer::tag('p', 'TODO: Add example of displaying a custom icon, where the icon name is stored in the database');


echo html_writer::tag('p', 'One final comment regarding images - you can reference moodle images in style sheets, just use this syntax and it will be substituted with the correct path when the stylesheet is compiled.');
echo html_writer::start_tag('pre');
echo '.someclass {
    background-image: url([[pix:t/edit]]);
}
.someclass2 {
    background-image: url([[pix:mod_name|logo]]);
}
.someclass3 {
    background-image: url([[pix:theme|t/edit]]);
}';
echo html_writer::end_tag('pre');

echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
