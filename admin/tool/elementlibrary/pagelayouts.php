<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$layout = optional_param('layout', null, PARAM_ALPHANUM);

// put details of all defined layouts in here
$layouts = array(
    'base' => array(
        'name' => 'Base',
        'description' => 'This is the base layout. This is the default layout used by any page which doesn\'t specify a layout via $PAGE->set_pagelayout().',
    ),
    'standard' => array(
        'name' => 'Standard',
        'description' => 'This is the standard layout. This layout is used if a page defines an invalid pagelayout option. Some core moodle pages specify this layout.',
    ),
    'course' => array(
        'name' => 'Course',
        'description' => 'This layout is used on the main course page.',
    ),
    'coursecategory' => array(
        'name' => 'Course category',
        'description' => 'This layout is used on course category pages.',
    ),
    'incourse' => array(
        'name' => 'In course',
        'description' => 'This layout is used on pages inside a course, e.g. inside a forum or other module. This is the default page layout if $cm is specified in require_login().',
    ),
    'frontpage' => array(
        'name' => 'Front page',
        'description' => 'This layout is used on the front page of the site.',
    ),
    'admin' => array(
        'name' => 'Admin',
        'description' => 'This layout is used on admin pages.',
    ),
    'mydashboard' => array(
        'name' => 'My dashboard',
        'description' => 'This layout is used on a user\'s "my" page (their own customisable dashboard area), and some profile pages.',
    ),
    'mypublic' => array(
        'name' => 'My public',
        'description' => 'Presumably this is a layout for publically accessible content, although it doesn\'t appear to be used anywhere.',
    ),
    'login' => array(
        'name' => 'Login',
        'description' => 'This layout is used on the login page.',
    ),
    'noblocks' => array(
        'name' => 'No Blocks',
        'description' => 'This is a custom Totara layout used on pages where the full page width is needed. No other block regions are displayed.',
    ),
    'popup' => array(
        'name' => 'Popup',
        'description' => 'This layout is used within pages displayed as a popup window. Avoid navigation, blocks or header to a minimum to leave space for the content of the window.',
    ),
    'frametop' => array(
        'name' => 'Frame top',
        'description' => 'This layout has no blocks and minimal footer - used for legacy frame layouts only',
    ),
    'embedded' => array(
        'name' => 'Embedded',
        'description' => 'This layout is used for embedded pages, like iframe embedded in a moodleform (e.g. chat)',
    ),
    'maintenance' => array(
        'name' => 'Maintenance',
        'description' => 'This is the maintenance layout. It is used during installs and upgrades and for the "This site is undergoing maintenance" message, so it shouldn\'t display blocks, navigation, or any other external links that the user could click during an upgrade.',
    ),
    'print' => array(
        'name' => 'Print',
        'description' => 'This is the print layout. It should display the content and basic headers only.',
    ),
    'redirect' => array(
        'name' => 'Redirect',
        'description' => 'This is the layout used when a redirection is occuring.',
    ),
    'report' => array(
        'name' => 'Report',
        'description' => 'This is the layout used when displaying moodle reports.',
    ),
);

if (!array_key_exists($layout, $layouts)) {
    $layout = null;
}

$strheading = 'Element Library: Page Layouts';
if ($layout) {
    $strheading .= ': ' . ucfirst($layout);
}

// Start setting up the page
$PAGE->set_context(get_system_context());
$url = new moodle_url('/admin/tool/elementlibrary/pagelayouts.php', array('layout' => $layout));
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);
if ($layout) {
    $PAGE->set_pagelayout($layout);
}

admin_externalpage_setup('toolelementlibrary');

echo $OUTPUT->header();

if ($layout) {
    echo html_writer::link(new moodle_url('/admin/tool/elementlibrary/pagelayouts.php'), '&laquo; Back to layouts');
} else {
    echo html_writer::link(new moodle_url('/admin/tool/elementlibrary/'), '&laquo; Back to index');
}
echo $OUTPUT->heading($strheading);

if ($layout) {
    // display each layout
    echo $OUTPUT->container($layouts[$layout]['description']);

    echo str_repeat(html_writer::tag('p', 'Paragraph text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu accumsan nulla. Cras elementum tincidunt dictum. Phasellus varius, est non ornare mattis, leo velit congue libero, vitae suscipit ipsum urna sed orci. Pellentesque venenatis pulvinar lobortis. Vestibulum iaculis commodo eros quis volutpat. Morbi vitae dapibus ante. Nullam convallis interdum ipsum, venenatis consequat eros faucibus sed. Pellentesque non tellus vel eros ullamcorper sollicitudin ut in lectus. Sed aliquet gravida porta.'), 5);
} else {
    // display index of layouts
    echo $OUTPUT->container('The links below take you to pages using each of the page layouts that can be defined in the theme.');
    $list = array();
    foreach ($layouts as $name => $info) {
        $url = new moodle_url('/admin/tool/elementlibrary/pagelayouts.php', array('layout' => $name));
        $text = $info['name'];
        if ($name != 'popup') {
            $list[] = html_writer::link($url, $text);
        } else {
            $list[] = $OUTPUT->action_link($url, $text, new popup_action('click', $url));
        }
    }

    echo html_writer::alist($list);

    echo $OUTPUT->heading('Developer info', 3);
    echo $OUTPUT->container('Each layout defines:');
    echo html_writer::alist(array('A file name for the layout template (stored in <code>theme/[themename]/layout/[filename]</code>). If no file exists in the theme, will look for layout files in each parent theme in turn.',
        'A set of regions which are displayed by that file',
        'A default region (used when adding blocks)',
        'A set of options. You can create any options you want in your theme\'s config.php then reference them in the theme layout files via <code>$PAGE->layout_options[\'settingname\']</code>. Typical options include:' .
        html_writer::alist(array(
        '<strong>langmenu</strong>: whether to show or hide the language menu (if enabled via settings and site has at least two languages installed)',
        '<strong>nofooter</strong>: don\'t include the page footer code',
        '<strong>nocustommenu</strong>: don\'t include the custommenu.',
        '<strong>noblocks</strong>: don\'t display any block regions on the page.',
        '<strong>nonavbar</strong>: don\'t display the navigation bar (row containing breadcrumbs trail and "edit button") on the page',
        '<strong>nologininfo</strong>: don\'t display the "you are logged in as..." text or login/logout button on the page'))
    ));
    echo $OUTPUT->container('Each of the files in the theme layout/ folder should contain the logic to correctly handles the options above. See the layouts section of base/config.php and base/layouts/ for full details.');

}

echo $OUTPUT->footer();
