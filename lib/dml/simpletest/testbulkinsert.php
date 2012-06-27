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
 * @package    core
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class bulkinsert_test extends UnitTestCase {
    private $tables = array();
    /** @var moodle_database */
    private $tdb;
    private $data;
    public  static $includecoverage = array('lib/dml');
    public  static $excludecoverage = array('lib/dml/simpletest');

    protected $olddebug;
    protected $olddisplay;

    function setUp() {
        global $DB, $UNITTEST;

        if (isset($UNITTEST->func_test_db)) {
            $this->tdb = $UNITTEST->func_test_db;
        } else {
            $this->tdb = $DB;
        }

        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_TEXT, 'large', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
    }

    function tearDown() {
        $dbman = $this->tdb->get_manager();

        foreach ($this->tables as $tablename) {
            if ($dbman->table_exists($tablename)) {
                $table = new xmldb_table($tablename);
                $dbman->drop_table($table);
            }
        }
        $this->tables = array();
    }

    /**
     * Get a xmldb_table object for testing, deleting any existing table
     * of the same name, for example if one was left over from a previous test
     * run that crashed.
     *
     * @param database_manager $dbman the database_manager to use.
     * @param string $suffix table name suffix, use if you need more test tables
     * @return xmldb_table the table object.
     */
    private function get_test_table($suffix = '') {
        $dbman = $this->tdb->get_manager();

        $tablename = "unit_table";
        if ($suffix !== '') {
            $tablename .= $suffix;
        }

        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table->setComment("This is a test'n drop table. You can drop it safely");
        $this->tables[$tablename] = $tablename;
        return new xmldb_table($tablename);
    }

    protected function enable_debugging() {
        global $CFG;

        $this->olddebug   = $CFG->debug;       // Save current debug settings
        $this->olddisplay = $CFG->debugdisplay;
        $CFG->debug = DEBUG_DEVELOPER;
        $CFG->debugdisplay = true;
        ob_start(); // hide debug warning

    }

    protected function get_debugging() {
        global $CFG;

        $debuginfo = ob_get_contents();
        ob_end_clean();
        $CFG->debug = $this->olddebug;         // Restore original debug settings
        $CFG->debugdisplay = $this->olddisplay;

        return $debuginfo;
    }

    private function make_dummy_data($numrows) {
        $data = array();
        for ($i = 1; $i <= $numrows; $i++) {
            $var = "row{$i}";
            $$var = new stdClass();
            $$var->course = "{$i}";
            $$var->name = "Course {$i}";
            $data[$i] = $$var;
        }
        return $data;
    }

    function test_get_sql_length_for_params_valid() {
        global $DB;
        $params = array('a', null, 'c', 'd', 'e', 'f', 'g', 'h', 'i', null, 'k', 'l');
        $sql = "('a',NULL,'c','d','e','f','g','h','i',NULL,'k','l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 12), strlen($sql));
        $sql = "('a',NULL,'c','d','e','f'),('g','h','i',NULL,'k','l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 6), strlen($sql));
        $sql = "('a',NULL,'c','d'),('e','f','g','h'),('i',NULL,'k','l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 4), strlen($sql));
        $sql = "('a',NULL,'c'),('d','e','f'),('g','h','i'),(NULL,'k','l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 3), strlen($sql));

        $sql = "('a',NULL),('c','d'),('e','f'),('g','h'),('i',NULL),('k','l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 2), strlen($sql));

        $sql = "('a'),(NULL),('c'),('d'),('e'),('f'),('g'),('h'),('i'),(NULL),('k'),('l')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 1), strlen($sql));

        // some unusual cases
        // TODO handle numbers like 01234 and quoted strings 'sdlfk\'lskfj'
        $params = array('string', 1234, 3.4, 'strings with spaces', "abc", 'null');
        $sql = "('string','1234','3.4'),('strings with spaces','abc','null')";
        $this->assertEqual($DB->get_sql_length_for_params($params, 3), strlen($sql));
    }

    function test_get_sql_length_for_params_invalid() {
        global $DB;
        $params = array('a', 'b', 'c', 'd', 'e');

        // it should throw an error if the number of fields doesn't divide
        // evenly into the parameters
        $this->expectException('dml_exception');
        $DB->get_sql_length_for_params($params, 4);
    }

    function test_bad_iterable() {
        $DB = $this->tdb;

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch('unit_table', 'bad-iterable');
    }

    function test_bad_iterable_item() {
        $DB = $this->tdb;
        $iterator = array('not an object');

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch('unit_table', $iterator);
    }

    function test_bad_iterable_second_item() {
        $DB = $this->tdb;
        $valid_object = new stdClass();
        $valid_object->property = 'value';
        $iterator = array($valid_object, 'not an object');

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch('unit_table', $iterator);
    }

    // equivalent to a recordset with no results
    function test_empty_iterable() {
        $DB = $this->tdb;
        $tablename = 'unit_table';
        $iterator = array();

        $writes_before = $DB->perf_get_writes();
        $DB->insert_records_via_batch($tablename, $iterator);
        $writes_after = $DB->perf_get_writes();

        // nothing to write so nothing done
        $this->assertEqual($writes_after, $writes_before);

        $result = $DB->get_records($tablename);
        $this->assertEqual(count($result), 0);
    }

    function test_bad_object_no_properties() {
        $DB = $this->tdb;

        $empty_object = new stdClass();
        $iterator = array($empty_object);

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch('unit_table', $iterator);
    }

    function test_bad_object_id_only() {
        $DB = $this->tdb;

        $bad_object = new stdClass();
        $bad_object->id = 1;
        $iterator = array($bad_object);

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch('unit_table', $iterator);
    }

    function test_bad_table_name() {
        $DB = $this->tdb;

        $valid_object = new stdClass();
        $valid_object->course = 1;
        $iterator = array($valid_object);

        $this->expectException('dml_write_exception');
        $DB->insert_records_via_batch('badtablename', $iterator);
    }

    function test_allowed_null_field() {
        $DB = $this->tdb;
        $DB = $this->tdb;
        $tablename = 'unit_table';

        // should insert as long as field can be null
        $valid_object = new stdClass();
        $valid_object->course = 1;
        $valid_object->name = null;
        $iterator = array($valid_object);

        $DB->insert_records_via_batch($tablename, $iterator);

    }

    function test_bulk_insert_single_batch() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $numrows = 3;
        $data = array();
        for ($i = 1; $i <= $numrows; $i++) {
            $var = "row{$i}";
            $$var = new stdClass();
            $$var->course = "{$i}";
            $$var->name = "Course {$i}";
            $data[$i] = $$var;
        }

        $writes_before = $DB->perf_get_writes();
        $DB->insert_records_via_batch($tablename, $data);
        $writes_after = $DB->perf_get_writes();

        // should be done as a single query
        $this->assertEqual($writes_after - $writes_before, 1);

        // should insert every row into the table
        $result = $DB->get_records($tablename);
        $this->assertEqual(count($data), count($result));

        // should insert the data unchanged
        foreach ($result as $record) {
            $id = $record->id;
            unset($record->id);
            $this->assertIdentical($record, $data[$id]);
        }
    }

    function test_bulk_insert_object_order_different() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $row1 = new stdClass();
        $row1->course = '1';
        $row1->name = "Course 1";
        // second item has order of properties swapped
        $row2 = new stdClass();
        $row2->name = "Course 2";
        $row2->course = '2';
        $data = array(1 => $row1, 2 => $row2);

        $writes_before = $DB->perf_get_writes();
        $DB->insert_records_via_batch($tablename, $data);
        $writes_after = $DB->perf_get_writes();

        // should be done as a single query
        $this->assertEqual($writes_after - $writes_before, 1);

        // should insert every row into the table
        $result = $DB->get_records($tablename);
        $this->assertEqual(count($data), count($result));

        // should insert the data unchanged
        foreach ($result as $record) {
            $id = $record->id;
            unset($record->id);
            $this->assertEqual($record, $data[$id]);
        }
    }

    function test_bulk_insert_two_batches() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $data = $this->make_dummy_data(1500);

        $writes_before = $DB->perf_get_writes();
        $DB->insert_records_via_batch($tablename, $data);
        $writes_after = $DB->perf_get_writes();

        // should be done as a two queries
        $this->assertEqual($writes_after - $writes_before, 2);

        // should insert every row into the table
        $result = $DB->get_records($tablename);
        $this->assertEqual(count($data), count($result));

        // should insert the data unchanged
        foreach ($result as $record) {
            // only check 1 in 100
            if ($record->id % 100 == 0) {
                $id = $record->id;
                unset($record->id);
                $this->assertIdentical($record, $data[$id]);
            }
        }
    }

    function test_bulk_insert_with_bad_record() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $data = $this->make_dummy_data(900);
        // course is a not null field so this should stop entire query
        unset($data[100]->course);

        $writes_before = $DB->perf_get_writes();
        $exceptioncaught = false;
        try {
            $DB->insert_records_via_batch($tablename, $data);
        } catch (dml_exception $e) {
            $exceptioncaught = true;
        }
        $writes_after = $DB->perf_get_writes();
        $this->assertTrue($exceptioncaught);

        /* TODO this doesn't work because transactions_supported is protected
        $result = $DB->get_records($tablename);
        if ($DB->transactions_supported()) {
            // no successful writes expected
            $this->assertEqual($writes_after - $writes_before, 0);
            // table should remain empty
            $this->assertEqual(count($result), 0);
        } else {
            // 1 write will occur
            $this->assertEqual($writes_after - $writes_before, 1);
            // first 99 records will have been written before query fails
            $this->assertEqual(count($result), 0);
        }
         */


    }

    function test_bulk_insert_at_batch_boundary() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        // number of rows inserted => number of queries required
        // test just below, at, just above a boundary, then the
        // same for the next boundary
        $tests = array(
            (BATCH_INSERT_MAX_ROW_COUNT   - 1) => 1,
             BATCH_INSERT_MAX_ROW_COUNT        => 1,
            (BATCH_INSERT_MAX_ROW_COUNT   + 1) => 2,
            (BATCH_INSERT_MAX_ROW_COUNT*2 - 1) => 2,
             BATCH_INSERT_MAX_ROW_COUNT*2      => 2,
            (BATCH_INSERT_MAX_ROW_COUNT*2 + 1) => 3,
        );
        foreach ($tests as $numrows => $write_count) {
            $data = $this->make_dummy_data($numrows);

            $writes_before = $DB->perf_get_writes();
            $DB->insert_records_via_batch($tablename, $data);
            $writes_after = $DB->perf_get_writes();

            // should be done as the correct number of queries
            $this->assertEqual($writes_after - $writes_before, $write_count);

            // should insert every row into the table
            $result = $DB->get_records($tablename);
            $this->assertEqual(count($data), count($result));

            // empty out the table again
            $DB->delete_records($tablename);
        }
    }

    function test_bulk_insert_at_query_boundary() {
        global $CFG;
        $DB = $this->tdb;
        $tablename = 'unit_table';

        // this is the whole query, except it's missing the data for the
        // 'name' field
        $query = "INSERT INTO {$CFG->prefix}unit_table (course,name) VALUES ('1','')";
        $query_length = strlen($query);
        $second_item_query = ",('2','a')";
        $second_item_query_length = strlen($second_item_query);
        // this is how big the name of the first item has to be to put the
        // second item right on the boundary
        $first_boundary_data_size = BATCH_INSERT_MAX_QUERY_SIZE - $query_length - $second_item_query_length;

        // data size in bytes => number of queries required
        // test just below, at, just above a boundary, then the
        // same for the next boundary
        $tests = array(
            ($first_boundary_data_size  - 1) => 1,
             $first_boundary_data_size       => 1,
            ($first_boundary_data_size  + 1) => 2,
        );
        foreach ($tests as $datasize => $write_count) {
            // first item is really big
            $item1 = new stdClass();
            $item1->course = 1;
            $item1->name = str_repeat('a', $datasize);
            // next one is tiny
            $item2 = new stdClass();
            $item2->course = 2;
            $item2->name = 'b';
            $data = array($item1, $item2);

            $writes_before = $DB->perf_get_writes();
            $DB->insert_records_via_batch($tablename, $data);
            $writes_after = $DB->perf_get_writes();

            // should be done as the correct number of queries
            $this->assertEqual($writes_after - $writes_before, $write_count);

            // should insert every row into the table
            $result = $DB->get_records($tablename);
            $this->assertEqual(count($data), count($result));

            // empty out the table again
            $DB->delete_records($tablename);
        }
    }

    function test_bulk_insert_first_item_exceeds_max_query_size() {
        global $CFG;
        $DB = $this->tdb;
        $tablename = 'unit_table';

        // first item is too big
        $item1 = new stdClass();
        $item1->course = 1;
        $item1->name = str_repeat('a', BATCH_INSERT_MAX_QUERY_SIZE);
        // next one is normal
        $item2 = new stdClass();
        $item2->course = 2;
        $item2->name = 'b';
        $data = array($item1, $item2);

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch($tablename, $data);
    }

    function test_bulk_insert_second_item_exceeds_max_query_size() {
        global $CFG;
        $DB = $this->tdb;
        $tablename = 'unit_table';

        // first item is normal
        $item1 = new stdClass();
        $item1->course = 1;
        $item1->name = 'a';
        // next one is too big
        $item2 = new stdClass();
        $item2->course = 2;
        $item2->name = str_repeat('b', BATCH_INSERT_MAX_QUERY_SIZE);
        $data = array($item1, $item2);

        $this->expectException('dml_exception');
        $DB->insert_records_via_batch($tablename, $data);
    }

    function test_bulk_insert_single_processor() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $numrows = 3;
        $data = array();
        $doubleddata = array();
        for ($i = 1; $i <= $numrows; $i++) {
            $var = "row{$i}";
            $$var = new stdClass();
            $$var->course = "{$i}";
            $$var->name = "Course {$i}";
            $data[$i] = $$var;
            $var2 = "row{$i}";
            $$var2 = new stdClass();
            $$var2->course = str_repeat("{$i}", 2);
            $$var2->name = str_repeat("Course {$i}", 2);
            $doubleddata[$i] = $$var2;
        }

        $writes_before = $DB->perf_get_writes();
        $DB->insert_records_via_batch($tablename, $data,
            'double_item_properties');
        $writes_after = $DB->perf_get_writes();

        // should be done as a single query
        $this->assertEqual($writes_after - $writes_before, 1);

        // should insert every row into the table
        $result = $DB->get_records($tablename);
        $this->assertEqual(count($doubleddata), count($result));

        // should insert the data unchanged
        foreach ($result as $record) {
            $id = $record->id;
            unset($record->id);
            $this->assertIdentical($record, $doubleddata[$id]);
        }
    }

    function test_bulk_insert_validator() {
        $DB = $this->tdb;
        $tablename = 'unit_table';

        $baditem1 = new stdClass();
        $baditem1->course = 'string';
        $baditem1->name = 'Name';
        $data = array($baditem1);

        $exceptioncaught = false;
        $exceptionmessage = '';
        $writes_before = $DB->perf_get_writes();
        try {
            $DB->insert_records_via_batch($tablename, $data, null,
                array($this, 'item_validator'));
        } catch (dml_exception $e) {
            $exceptioncaught = true;
            $exceptionmessage = $e->getMessage();
        }
        $writes_after = $DB->perf_get_writes();

        // should throw an exception
        $this->assertTrue($exceptioncaught);

        // exception should be due to failing the validation
        $this->assertEqual($exceptionmessage,
            get_string('batchinsertitemfailedvalidation', 'error',
            'item_validator'));

        // query should not be executed
        $this->assertEqual($writes_after - $writes_before, 0);

        // nothing should be inserted
        $result = $DB->get_records($tablename);
        $this->assertEqual(count($result), 0);

    }

    /**
     * Test method for use as a validator in bulk insert tests
     *
     * @param object $item An item
     * @returns boolean true if the item is valid
     */
    function item_validator($item) {
        // check required properties are set
        if (!property_exists($item, 'course') || !property_exists($item, 'name')) {
            return false;
        }
        // check the course is not null
        if (is_null($item->course)) {
            return false;
        }
        // make sure course is a positive integer
        if (!is_integer($item->course) || $item->course <= 0) {
            return false;
        }
        // made up one for the test:
        // make sure name isn't equal to 'badvalue'
        if ($item->name == 'badvalue') {
            return false;
        }
        // all okay
        return true;
    }
}

/**
 * Test function for use as a processor in bulk insert tests
 *
 * @param object $item An item
 * @returns object $item but with the values doubled
 */
function double_item_properties($item) {
    $newitem = new stdClass();
    foreach ($item as $property => $value) {
        $newitem->$property = str_repeat($value, 2);
    }
    return $newitem;
}

