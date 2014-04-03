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
 * Unit tests for recordset_transform class.
 *
 * @package    core
 * @category   phpunit
 * @author     Simon Coggins <simon.coggins@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

class core_recordset_transform_testcase extends advanced_testcase {

    public function test_constructor() {
        global $DB;

        // Should create a recordset if a valid recordset is passed.
        $testrecordset = $DB->get_recordset('user');
        $trs = new recordset_transform($testrecordset);
        $this->assertInstanceOf('Traversable', $trs);
        $trs->close();

        // Should work even if the recordset passed doesn't contain any results.
        $testrecordset = $DB->get_recordset('user', array('username' => 'nonexistant'));
        $trs = new recordset_transform($testrecordset);
        $this->assertInstanceOf('Traversable', $trs);
        $trs->close();
    }

    public function test_validator() {
        global $DB;

        // Test when a validator always returns true (should have no effect).

        $unmodifiedrecordset = $DB->get_recordset('user');
        $oldresults = array();
        foreach ($unmodifiedrecordset as $item) {
            $oldresults[] = $item;
        }
        $unmodifiedrecordset->close();

        $transformedrecordset = $DB->get_recordset('user');
        $trs = new recordset_transform($transformedrecordset);
        $trs->set_validator(function($item) {
            return true;
        });
        $newresults = array();
        foreach ($trs as $item) {
            $newresults[] = $item;
        }
        $trs->close();

        // Recordset should return all results unchanged.
        $this->assertEquals($oldresults, $newresults);

        // Test when a validator always returns false (expect no results).

        $testrecordset = $DB->get_recordset('user');
        $trs = new recordset_transform($testrecordset);
        $trs->set_validator(function($item) {
            return false;
        });
        $items = array();
        foreach ($trs as $item) {
            $items[] = $item;
        }
        $trs->close();
        // Recordset should return zero results, but otherwise work
        // as normal.
        $this->assertCount(0, $items);

        // Test a validator which validates some records but excludes others.

        // The user recordset will contain 2 users (admin and guest).
        $testrecordset = $DB->get_recordset('user');
        $trs = new recordset_transform($testrecordset);

        // Validator that checks if user is admin.
        $trs->set_validator(function($item) {
            return $item->username == 'admin';
        });

        $items = array();
        foreach ($trs as $item) {
            $items[] = $item;
        }
        $trs->close();

        // Recordset should only return the admin user, not guest
        // or any other users.
        $this->assertCount(1, $items);

        // The record should be unchanged.
        $admin = $DB->get_record('user', array('username' => 'admin'));
        $this->assertEquals($admin, current($items));

        // Test validator arguments.
        $testrecordset = $DB->get_recordset('user');
        $trs = new recordset_transform($testrecordset);

        // Validator that checks for any username.
        $checkusername = function($item, $username) {
            return $item->username == $username;
        };
        // Pass the username we want to check as an argument.
        $trs->set_validator($checkusername, array('guest'));

        $items = array();
        foreach ($trs as $item) {
            $items[] = $item;
        }
        $trs->close();

        // Recordset should only return the guest user, not admin
        // or any other users.
        $this->assertCount(1, $items);

        // The record should be unchanged.
        $admin = $DB->get_record('user', array('username' => 'guest'));
        $this->assertEquals($admin, current($items));
    }

    public function test_processor() {
        global $DB;

        // Test a simple processor.
        $testrecordset = $DB->get_recordset('user', null, '', 'id,username');
        $trs = new recordset_transform($testrecordset);

        // Processor that adds a new property to each object.
        $trs->set_processor(function($item) {
            $item->newproperty = true;
            return $item;
        });

        // Each item should have the newproperty added.
        foreach ($trs as $item) {
            $this->assertObjectHasAttribute('newproperty', $item);
        }
        $trs->close();

        // Test processor arguments.
        $testrecordset = $DB->get_recordset('user', null, '', 'id,username');
        $trs = new recordset_transform($testrecordset);

        // Processor that adds a property to each object with a specified value.
        $processor = function($item, $value) {
            $item->newproperty = $value;
            return $item;
        };
        $trs->set_processor($processor, array('data'));

        // Each item should have the newproperty added with the correct value.
        foreach ($trs as $item) {
            $this->assertEquals('data', $item->newproperty);
        }
        $trs->close();

        // Test processing based on original recordset contents.
        $testrecordset = $DB->get_recordset('user', null, '', 'id,username');
        $trs = new recordset_transform($testrecordset);

        // Processor that stores the first initial from the username in a
        // new property.
        $processor = function($item) {
            $item->firstinitial = substr($item->username, 0, 1);
            return $item;
        };
        $trs->set_processor($processor);

        // Each item should have the newproperty added with the correct value.
        foreach ($trs as $item) {
            if ($item->username == 'admin') {
                $this->assertEquals('a', $item->firstinitial);
            }
            if ($item->username == 'guest') {
                $this->assertEquals('g', $item->firstinitial);
            }
        }
        $trs->close();
    }
}

