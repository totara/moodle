<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    course
 * @author     Russell England <russell.england@catalyst-eu.net>
 */

/**
 * Deletes course completion records and archives activities for a course
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');

$id = required_param('id', PARAM_INT); // course id
$archive = optional_param('archive', '', PARAM_ALPHANUM); // archive confirmation hash

$PAGE->set_url('/course/archivecompletions.php', array('id' => $id));
$PAGE->set_context(context_system::instance());

require_login();

$site = get_site();

if (($site->id == $id) || (!$course = $DB->get_record('course', array('id' => $id)))) {
    print_error('invalidcourseid');
}

$coursecontext = context_course::instance($course->id);

// If the user can't delete then they can't archive
if (!can_delete_course($id)) {
    print_error('cannotarchivecompletions', 'completion');
}

$sql = "SELECT DISTINCT cc.userid
        FROM {course_completions} cc
        WHERE cc.course = :courseid
        AND cc.timecompleted IS NOT NULL";
$users = $DB->get_records_sql($sql, array('courseid' => $course->id));

$category = $DB->get_record('course_categories', array('id' => $course->category));
$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
$categoryname = format_string($category->name, true, array('context' => context_coursecat::instance($category->id)));

$PAGE->navbar->add(get_string('administration'), new moodle_url('/admin/index.php/'));
$PAGE->navbar->add(get_string('categories'), new moodle_url('/course/index.php'));
$PAGE->navbar->add($categoryname, new moodle_url('/course/index.php', array('categoryid' => $course->category)));
$PAGE->navbar->add($courseshortname, new moodle_url('/course/view.php', array('id' => $course->id)));


// first time round - get confirmation
if (!$archive) {
    $strarchivecheck = get_string('archivecheck', 'completion', $courseshortname);
    $strarchivecompletionscheck = get_string('archivecompletionscheck', 'completion');

    $PAGE->navbar->add($strarchivecheck);
    $PAGE->set_title($site->shortname .': '. $strarchivecheck);
    $PAGE->set_heading($site->fullname);
    echo $OUTPUT->header();

    if (empty($users)) {
        echo $OUTPUT->box(get_string('nouserstoarchive', 'completion'));
    } else {
        $message = $strarchivecompletionscheck;
        $message .= html_writer::empty_tag('br');
        $message .= html_writer::empty_tag('br');
        $message .= format_string($course->fullname, true, array('context' => $coursecontext));
        $message .= ' (' . $courseshortname . ')';
        $message .= html_writer::empty_tag('br');
        $message .= html_writer::empty_tag('br');
        $message .= get_string('archiveusersaffected', 'completion', count($users));

        $archiveurl = new moodle_url('/course/archivecompletions.php',
                array('id' => $course->id, 'archive' => md5($course->timemodified)));
        $viewurl = new moodle_url('/course/view.php', array('id' => $course->id));
        echo $OUTPUT->confirm($message, $archiveurl, $viewurl);
    }
} else {
    // user confirmed archive
    if ($archive != md5($course->timemodified)) {
        print_error('invalidmd5');
    }

    require_sesskey();

    $strarchivingcourse = get_string('archivingcompletions', 'completion', $courseshortname);

    $PAGE->navbar->add($strarchivingcourse);
    $PAGE->set_title($site->shortname .': '. $strarchivingcourse);
    $PAGE->set_heading($site->fullname);

    foreach ($users as $user) {
        // Archive the course completion record before the activities to get the grade
        archive_course_completion($user->userid, $course->id);
        archive_course_activities($user->userid, $course->id);
    }

    add_to_log(SITEID, "course", "archive", "archivecompletions.php?id=$course->id", "$course->fullname (ID $course->id)");

    // The above archive_course_activities() calls set_module_viewed() which needs to be called before $OUTPUT->header()
    echo $OUTPUT->header();

    echo $OUTPUT->heading(get_string('archivedcompletions', 'completion', $courseshortname));
    echo html_writer::tag('p', get_string('usersarchived', 'completion', count($users)));
    $viewurl = new moodle_url('/course/view.php', array('id' => $course->id));
    echo $OUTPUT->continue_button($viewurl);
}
echo $OUTPUT->footer();
