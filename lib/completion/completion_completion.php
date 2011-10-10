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
 * Course completion status for a particular user/course
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/completion/data_object.php');
require_once($CFG->libdir.'/completionlib.php');


/**
 * Course completion status constants
 *
 * For translating database recorded integers to strings and back
 */
define('COMPLETION_STATUS_NOTYETSTARTED',   10);
define('COMPLETION_STATUS_INPROGRESS',      25);
define('COMPLETION_STATUS_COMPLETE',        50);

global $COMPLETION_STATUS;
$COMPLETION_STATUS = array(
    COMPLETION_STATUS_NOTYETSTARTED => 'notyetstarted',
    COMPLETION_STATUS_INPROGRESS => 'inprogress',
    COMPLETION_STATUS_COMPLETE => 'complete',
);


/**
 * Course completion status for a particular user/course
 */
class completion_completion extends data_object {

    /**
     * DB Table
     * @var string $table
     */
    public $table = 'course_completions';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'userid', 'course', 'deleted', 'timenotified',
        'timeenrolled', 'timestarted', 'timecompleted', 'reaggregate', 'status');

    /**
     * Array of optional table fields
     * @var array $optional_fields
     */
    public $optional_fields = array('name' => '');

    /**
     * User ID
     * @access  public
     * @var     int
     */
    public $userid;

    /**
     * Course ID
     * @access  public
     * @var     int
     */
    public $course;

    /**
     * Set to 1 if this record has been deleted
     * @access  public
     * @var     int
     */
    public $deleted;

    /**
     * Timestamp the interested parties were notified
     * of this user's completion
     * @access  public
     * @var     int
     */
    public $timenotified;

    /**
     * Time of course enrolment
     * @see     completion_completion::mark_enrolled()
     * @access  public
     * @var     int
     */
    public $timeenrolled;

    /**
     * Time the user started their course completion
     * @see     completion_completion::mark_inprogress()
     * @access  public
     * @var     int
     */
    public $timestarted;

    /**
     * Timestamp of course completion
     * @see     completion_completion::mark_complete()
     * @access  public
     * @var     int
     */
    public $timecompleted;

    /**
     * Flag to trigger cron aggregation (timestamp)
     * @access  public
     * @var     int
     */
    public $reaggregate;

    /**
     * Course name (optional field)
     * @access  public
     * @var     string
     */
    public $name;

    /**
     * Completion status constant
     * @access  public
     * @var     int
     */
    public $status;


    /**
     * Finds and returns a data_object instance based on params.
     * @static static
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['deleted'] = null;
        return self::fetch_helper('course_completions', __CLASS__, $params);
    }


    /**
     * Return user's status
     *
     * Uses the following properties to calculate:
     *  - $timeenrolled
     *  - $timestarted
     *  - $timecompleted
     *  - $rpl
     *
     * @static static
     *
     * @param   object  $completion  Object with at least the described columns
     * @return  str     Completion status lang string key
     */
    public static function get_status($completion) {
        // Check if a completion record was supplied
        if (!is_object($completion)) {
            error('Incorrect data supplied to calculate Completion status');
        }

        // Check we have the required data, if not the user is probably not
        // participating in the course
        if (empty($completion->timeenrolled) &&
            empty($completion->timestarted) &&
            empty($completion->timecompleted))
        {
            return '';
        }

        // Check if complete
        if ($completion->timecompleted) {
            return 'complete';
        }

        // Check if in progress
        elseif ($completion->timestarted) {
            return 'inprogress';
        }

        // Otherwise not yet started
        elseif ($completion->timeenrolled) {
            return 'notyetstarted';
        }

        // Otherwise they are not participating in this course
        else {
            return '';
        }
    }


    /**
     * Return status of this completion
     * @access  public
     * @return  boolean
     */
    public function is_complete() {
        return (bool) $this->timecompleted;
    }

    /**
     * Mark this user as started (or enrolled) in this course
     *
     * If the user is already marked as started, no change will occur
     *
     * @access  public
     * @param   integer $timeenrolled Time enrolled (optional)
     * @return  void
     */
    public function mark_enrolled($timeenrolled = null) {

        if ($this->timeenrolled === null) {

            if ($timeenrolled === null) {
                $timeenrolled = time();
            }

            $this->timeenrolled = $timeenrolled;
        }

        return $this->_save();
    }

    /**
     * Mark this user as inprogress in this course
     *
     * If the user is already marked as inprogress,
     * the time will not be changed
     *
     * @access  public
     * @param   integer $timestarted Time started (optional)
     * @return  void
     */
    public function mark_inprogress($timestarted = null) {

        $timenow = time();

        // Set reaggregate flag
        $this->reaggregate = $timenow;

        if (!$this->timestarted) {

            if (!$timestarted) {
                $timestarted = $timenow;
            }

            $this->timestarted = $timestarted;
        }

        return $this->_save();
    }

    /**
     * Mark this user complete in this course
     *
     * This generally happens when the required completion criteria
     * in the course are complete.
     *
     * @access  public
     * @param   integer $timecomplete Time completed (optional)
     * @return  void
     */
    public function mark_complete($timecomplete = null) {

        // Never change a completion time
        if ($this->timecompleted) {
            return;
        }

        // Use current time if nothing supplied
        if (!$timecomplete) {
            $timecomplete = time();
        }

        // Set time complete
        $this->timecompleted = $timecomplete;

        // Save record
        return $this->_save();
    }

    /**
     * Save course completion status
     *
     * This method creates a course_completions record if none exists
     * @access  private
     * @return  bool
     */
    private function _save() {
        if ($this->timeenrolled === null) {
            $this->timeenrolled = 0;
        }

        // Update status column
        $status = completion_completion::get_status($this);
        if ($status) {
            $status = constant('COMPLETION_STATUS_'.strtoupper($status));
        }

        $this->status = $status;

        // Save record
        if ($this->id) {
            return $this->update();
        } else {
            // We should always be reaggregating when new course_completions
            // records are created as they might have already completed some
            // criteria before enrolling
            if (!$this->reaggregate) {
                $this->reaggregate = time();
            }

            // Make sure timestarted is not null
            if (!$this->timestarted) {
                $this->timestarted = 0;
            }

            return $this->insert();
        }
    }
}


/**
 * Scan a course (or the entire site) for tracked users who
 * do not have completion records in courses with completion
 * enabled and completionstartonenrol set
 *
 * @access  public
 * @param   int     $courseid   (optional)
 * @return  void
 */
function completion_mark_users_started($courseid = null) {
    global $CFG, $DB;

    if (debugging()) {
        mtrace('Marking users as started');
    }

    if (!empty($CFG->gradebookroles)) {
        $roles = ' AND ra.roleid IN ('.$CFG->gradebookroles.')';
    } else {
        // This causes it to default to everyone (if there is no student role)
        $roles = '';
    }

    // Course where clause
    $cwhere = '';
    if ($courseid !== null) {
        $cwhere = 'AND c.id = '.(int)$courseid;
    }

    /**
     * A quick explaination of this horrible looking query
     *
     * It's purpose is to locate all the active participants
     * of a course with course completion enabled.
     *
     * We also only want the users with no course_completions
     * record as this functions job is to create the missing
     * ones :)
     *
     * We want to record the user's enrolment start time for the
     * course. This gets tricky because there can be multiple
     * enrolment plugins active in a course, hence the possibility
     * of multiple records for each couse/user in the results
     */
    $sql = "
        SELECT
            c.id AS course,
            u.id AS userid,
            crc.id AS completionid,
            ue.timestart AS timeenrolled,
            ue.timecreated
        FROM
            {user} u
        INNER JOIN
            {user_enrolments} ue
         ON ue.userid = u.id
        INNER JOIN
            {enrol} e
         ON e.id = ue.enrolid
        INNER JOIN
            {course} c
         ON c.id = e.courseid
        INNER JOIN
            {role_assignments} ra
         ON ra.userid = u.id
        LEFT JOIN
            {course_completions} crc
         ON crc.course = c.id
        AND crc.userid = u.id
        WHERE
            c.enablecompletion = 1
        AND crc.timeenrolled IS NULL
        AND ue.status = 0
        AND e.status = 0
        AND u.deleted = 0
        AND ue.timestart < ?
        AND (ue.timeend > ? OR ue.timeend = 0)
            $cwhere
            $roles
        ORDER BY
            course,
            userid
    ";

    $now = time();
    $rs = $DB->get_recordset_sql($sql, array($now, $now, $now, $now));

    // Check if result is empty
    if (!$rs->valid()) {
        $rs->close(); // Not going to iterate (but exit), close rs
        return;
    }

    /**
     * An explaination of the following loop
     *
     * We are essentially doing a group by in the code here (as I can't find
     * a decent way of doing it in the sql).
     *
     * Since there can be multiple enrolment plugins for each course, we can have
     * multiple rows for each particpant in the query result. This isn't really
     * a problem until you combine it with the fact that the enrolment plugins
     * can save the enrol start time in either timestart or timeenrolled.
     *
     * The purpose of this loop is to find the earliest enrolment start time for
     * each participant in each course.
     */
    $prev = null;
    while ($rs->valid() || $prev) {

        $current = $rs->current();

        if (!isset($current->course)) {
            $current = false;
        }
        else {
            // Not all enrol plugins fill out timestart correctly, so use whichever
            // is non-zero
            $current->timeenrolled = max($current->timecreated, $current->timeenrolled);
        }

        // If we are at the last record,
        // or we aren't at the first and the record is for a diff user/course
        if ($prev &&
            (!$rs->valid() ||
            ($current->course != $prev->course || $current->userid != $prev->userid))) {

            $completion = new completion_completion();
            $completion->userid = $prev->userid;
            $completion->course = $prev->course;
            $completion->timeenrolled = (string) $prev->timeenrolled;
            $completion->timestarted = $completion->timeenrolled;
            $completion->reaggregate = time();

            if ($prev->completionid) {
                $completion->id = $prev->completionid;
            }

            $completion->mark_enrolled();

            if (debugging()) {
                mtrace('Marked started user '.$prev->userid.' in course '.$prev->course);
            }
        }
        // Else, if this record is for the same user/course
        elseif ($prev && $current) {
            // Use oldest timeenrolled
            $current->timeenrolled = min($current->timeenrolled, $prev->timeenrolled);
        }

        // Move current record to previous
        $prev = $current;

        // Move to next record
        $rs->next();
    }

    $rs->close();
}

