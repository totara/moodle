<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * minimalistic edit form
 *
 * @package   block_private_files
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class user_files_form extends moodleform {
    function definition() {
        global $USER, $CFG;
        $mform = $this->_form;

        $data           = $this->_customdata['data'];
        $options        = $this->_customdata['options'];

        $context = context_user::instance($USER->id);
        $fs = get_file_storage();
        $usedspace = 0;
        $privatefiles = $fs->get_area_files($context->id, 'user', 'private', false, 'id', false);
        foreach ($privatefiles as $file) {
            $usedspace += $file->get_filesize();
        }
        $megabyte = 1024 * 1024;
        $a = new stdClass();
        $a->usedspace = round(($usedspace/$megabyte), 1);
        $a->quota = round(($CFG->userquota/$megabyte), 1);
        $a->percent = round((($a->usedspace/$a->quota)*100), 1);

        $mform->addElement('static', 'quotautilisation', '', get_string('userquotautilisation', null, $a));
        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
        $mform->addElement('hidden', 'returnurl', $data->returnurl);

        $this->add_action_buttons(true, get_string('savechanges'));

        $this->set_data($data);
    }
    function validation($data, $files) {
        global $CFG, $USER;
        $usercontext = context_user::instance($USER->id);
        $errors = array();
        $options = $this->_customdata['options'];
        $draftitemid = $data['files_filemanager'];
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);
        $totalsize = 0;
        foreach ($draftfiles as $file) {
            $filesize = $file->get_filesize();
            if ($filesize > $options['maxbytes']) {
                $errors['files_filemanager'] = get_string('maxbytes', 'error');
            }
            $totalsize += $filesize;
        }

        if ($totalsize >= $CFG->userquota) {
            $errors['files_filemanager'] = get_string('userquotaexceeded', 'error');
        }
        return $errors;
    }
}
