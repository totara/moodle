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
 * This file contains the overall badge award criteria type
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Overall badge award criteria
 *
 */
class award_criteria_overall extends award_criteria {

    /* @var int Criteria ['overall'] */
    public $criteriatype = 'overall';
    /* @var array Supported badge types */
    public static $supportedtypes = array(BADGE_TYPE_COURSE, BADGE_TYPE_SITE);

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param badge $badge Badge being edited
     */
    public function config_form_criteria(badge $badge) {
        global $OUTPUT;
        $prefix = 'criteria-' . $this->id;
        if ($badge->has_criteria() && !$badge->has_one_criterion()) {
            echo $OUTPUT->box_start();
            echo $OUTPUT->heading($this->get_title(), 2);

            $agg = $badge->get_aggregation_methods();
            if (!$badge->is_locked() && !$badge->is_active()) {
                $url = new moodle_url('criteria.php', array('id' => $badge->id, 'sesskey' => sesskey()));
                $table = new html_table();
                $table->attributes = array('class' => 'clearfix');
                $table->colclasses = array('', 'activatebadge');
                $table->data[] = array(
                        $OUTPUT->single_select($url, 'update', $agg, $badge->get_aggregation_method($this->criteriatype), null),
                        get_string('overallcrit', 'badges')
                        );
                echo html_writer::table($table);
            } else {
                echo $OUTPUT->box(get_string('description', 'badgecriteria_' . $this->criteriatype,
                        strtoupper($agg[$badge->get_aggregation_method()])), 'clearfix');
            }
            echo $OUTPUT->box_end();
        }
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_options(&$mform, $param) {
    }

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = false) {
    }

    /**
     * Review this criteria and decide if it has been completed
     * Overall criteria review should be called only from other criteria handlers.
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    public function review($userid) {
        global $DB;

        $sql = "SELECT bc.*, bcm.critid, bcm.userid, bcm.datemet
                FROM {badge_criteria} bc
                LEFT JOIN {badge_criteria_met} bcm
                    ON bc.id = bcm.critid AND bcm.userid = :userid
                WHERE bc.badgeid = :badgeid
                    AND bc.criteriatype != :criteriatype ";

        $params = array(
                    'userid' => $userid,
                    'badgeid' => $this->badgeid,
                    'criteriatype' => 'overall'
                );

        $criteria = $DB->get_records_sql($sql, $params);
        $overall = false;
        foreach ($criteria as $crit) {
            // Check the award criteria exists.
            $critobj = award_criteria::build((array)$crit);
            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (!$critobj || $crit->datemet === null) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if (!$critobj || $crit->datemet === null) {
                    $overall = false;
                    continue;
                } else {
                    return true;
                }
            }
        }

        return $overall;
    }

    /**
     * Add appropriate criteria elements to the form
     *
     */
    public function get_options(&$mform) {
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid) {
    }
}
