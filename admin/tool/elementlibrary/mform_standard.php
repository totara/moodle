<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/lib/formslib.php');

$strheading = 'Element Library: Moodle Forms: Standard elements';
$url = new moodle_url('/admin/tool/elementlibrary/mform_standard.php');

// Start setting up the page
$params = array();
$PAGE->set_context(get_system_context());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('toolelementlibrary');
echo $OUTPUT->header();

echo html_writer::link(new moodle_url('mform.php'), '&laquo; Back to moodle forms');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();
echo $OUTPUT->container('Examples of different types of elements. Submit the form to see server side validation message for each item when validation fails.');
echo $OUTPUT->box_end();

class standard_form_elements extends moodleform {

    // Define the form
    function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', null, 'Controls');

        $mform->addElement('checkbox', 'disableelements', 'Disable all elements below', 'Use to test disabled state styles and functionality.');

        $mform->addElement('header', null, 'Header element');

        $mform->addElement('button', 'buttonfield', 'Button text');
        $mform->disabledIf('buttonfield', 'disableelements', 'checked');

        $mform->addElement('checkbox', 'checkboxfield', 'A checkbox', 'Label next to checkbox');
        $mform->disabledIf('checkboxfield', 'disableelements', 'checked');

        $this->add_checkbox_controller(1);
        $mform->addElement('advcheckbox', 'advcheckboxfield1', 'Advanced checkbox 1', 'Label next to advanced checkbox 1', array('group' => 1));
        $mform->addElement('advcheckbox', 'advcheckboxfield2', 'Advanced checkbox 2', 'Label next to advanced checkbox 2', array('group' => 1));
        $mform->addElement('advcheckbox', 'advcheckboxfield3', 'Advanced checkbox 3', 'Label next to advanced checkbox 3', array('group' => 1));
        $mform->disabledIf('advcheckboxfield1', 'disableelements', 'checked');
        $mform->disabledIf('advcheckboxfield2', 'disableelements', 'checked');
        $mform->disabledIf('advcheckboxfield3', 'disableelements', 'checked');


        $mform->addElement('static', 'datedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('date_selector', 'datefield', 'Date selector');
        $mform->disabledIf('datefield', 'disableelements', 'checked');

        $mform->addElement('static', 'datetimedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('date_time_selector', 'datetimefield', 'Date and time selector');
        $mform->disabledIf('datetimefield', 'disableelements', 'checked');

        $mform->addElement('duration', 'durationfield', 'Duration');
        $mform->disabledIf('durationfield', 'disableelements', 'checked');

        $mform->addElement('editor', 'editorfield', 'HTML Editor');
        $mform->disabledIf('editorfield', 'disableelements', 'checked');

        $mform->addElement('static', 'filepickerdes', '', 'Use the file picker to let the user choose or upload one or more files to be processed immediately (such as a CSV import).');
        $mform->addElement('filepicker', 'filepickerfield', 'Filepicker');
        $mform->disabledIf('filepickerfield', 'disableelements', 'checked');

        $mform->addElement('static', 'filemanagerdes', '', 'Use the file manager when the user needs to upload one or more files to the server. Remember to disabled subfolders if not required.');
        $mform->addElement('filemanager', 'filemanagerfield', 'Filemanager');
        $mform->disabledIf('filemanagerfield', 'disableelements', 'checked');

        $mform->addElement('modgrade', 'modgradefield', 'Mod grade');
        $mform->disabledIf('modgradefield', 'disableelements', 'checked');

        $mform->addElement('modvisible', 'modvisiblefield', 'Mod visible');
        $mform->disabledIf('modvisiblefield', 'disableelements', 'checked');

        $mform->addElement('password', 'passwordfield','Password field');
        $mform->disabledIf('passwordfield', 'disableelements', 'checked');

        $mform->addElement('passwordunmask', 'passwordunmaskfield','Password field with unmask option');
        $mform->disabledIf('passwordunmaskfield', 'disableelements', 'checked');

        $mform->addElement('static', 'radiodes', '', 'Usually radio buttons are grouped, see grouped page for details');
        $mform->addElement('radio', 'radiofield', 'Radio', 'Text to right of radio', 1);
        $mform->addElement('radio', 'radiofield', 'Radio 2', 'Text to right of radio 2', 2);
        $mform->disabledIf('radiofield', 'disableelements', 'checked');

        $mform->addElement('select', 'selectfield', 'Single select', array(0 => 'Item 1', 1 => 'Item 2', 3 => 'Item 3'));
        $mform->disabledIf('selectfield', 'disableelements', 'checked');

        $mform->addElement('static', 'multiselectdesc', '', 'Use checkboxes instead of multiselects where possible. If you must use, include a label indicating ctrl can be used to select/unselect multiple items');
        $select = &$mform->addElement('select', 'multiselectfield', 'Multi select', array(0 => 'Item 1', 1 => 'Item 2', 3 => 'Item 3'));
        $select->setMultiple(true);
        $mform->disabledIf('multiselectfield', 'disableelements', 'checked');

        // to disable individual options, build the select manually:
        $select2 = HTML_QuickForm::createElement('select', 'selectwithdisabledoptionsfield', 'Select with disabled options');
        $select2->addOption( 'An active option', '');
        $select2->addOption( 'A disabled option', '', array('disabled' => 'disabled'));
        $select2->addOption( 'Another active option', '');
        $select2->addOption( 'Another disabled option', '', array('disabled' => 'disabled'));
        $mform->addElement($select2);
        $mform->disabledIf('selectwithdisabledoptionsfield', 'disableelements', 'checked');

        $groupselectoptions = array(
            'group one' => array(1 => 'one', 2 => 'two', 3 => 'three'),
            'group two' => array(1 => 'one', 2 => 'two', 3 => 'three'),
            'group three' => array(1 => 'one', 2 => 'two', 3 => 'three'),
        );
        $mform->addElement('selectgroups',"groupedselectfield", 'Grouped select', $groupselectoptions);
        $mform->disabledIf('groupedselectfield', 'disableelements', 'checked');

        $mform->addElement('selectyesno', 'selectyesnofield', 'Yes/No select');
        $mform->disabledIf('selectyesnofield', 'disableelements', 'checked');

        $mform->addElement('selectwithlink', 'selectwithlinkfield', 'Select with link', array(1 => 'One', 2 => 'Two', 3 => 'Three'), null, array('link' => $CFG->wwwroot.'/admin/tool/elementlibrary/', 'label' => 'A label'));
        $mform->disabledIf('selectwithlinkfield', 'disableelements', 'checked');

        $mform->addElement('searchableselector', 'searchableselectorfield', 'Searchable selector', get_string_manager()->get_list_of_countries(true));
        $mform->disabledIf('searchableselectorfield', 'disableelements', 'checked');

        $mform->addElement('submit', 'submitfield', 'Submit text');
        $mform->disabledIf('submitfield', 'disableelements', 'checked');
        $mform->addElement('reset', 'resetfield', 'Reset text');
        $mform->disabledIf('resetfield', 'disableelements', 'checked');
        $mform->addElement('cancel', 'cancelfield', 'Cancel text');
        $mform->disabledIf('cancelfield', 'disableelements', 'checked');


        // no 'size' attribute, use CSS definitions
        $mform->addElement('text', 'textfield', 'Text field');
        $mform->setDefault('textfield', 'Default text');
        $mform->disabledIf('textfield', 'disableelements', 'checked');

        $mform->addElement('textarea', 'textareafield', 'Textarea field', 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('textareafield', 'Default text');
        $mform->disabledIf('textareafield', 'disableelements', 'checked');

        $mform->addElement('static', 'tagsdesc', '', 'The tags field has options to show just official tags, just user tags or both. Here is both:');
        $mform->addElement('tags', 'tagsfield', 'Tags');
        $mform->disabledIf('tagsfield', 'disableelements', 'checked');

        $mform->addElement('static', 'datedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('url', 'urlfield', 'URL');
        $mform->disabledIf('urlfield', 'disableelements', 'checked');

        $mform->addElement('header', null, 'Another Header element');

        $mform->addElement('static', 'staticfield', 'A static element', 'A static field\'s value');

        $this->add_action_buttons(true, 'Submit button');
    }

    function validation($formelements) {
        $err = array();
        foreach ($formelements as $name => $value) {
            $err[$name] = 'Custom validation message for ' . $name;
        }
        // some elements need to be manually failed as they aren't included
        // in the formelements array
        $error = 'Custom validation message';
        $err['buttonfield'] = $error;
        $err['submitfield'] = $error;
        $err['resetfield'] = $error;
        $err['cancelfield'] = $error;
        $err['radiofield'] = $error;
        $err['searchableselectorfield'] = $error;
        $err['checkboxfield'] = $error;
        $err['multiselectfield'] = $error;
        $err['selectwithdisabledoptionsfield'] = $error;
        return $err;
    }
}
$form = new standard_form_elements();
$data = $form->get_data(); // enables server validation
$form->display();

echo $OUTPUT->footer();

