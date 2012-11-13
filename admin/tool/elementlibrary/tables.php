<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$strheading = 'Element Library: Tables';
$url = new moodle_url('/admin/tool/elementlibrary/tables.php');

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
echo $OUTPUT->container('Examples of different types of tables.');

echo $OUTPUT->container_start();

echo $OUTPUT->heading('Standard moodle tables (class "generaltable")', 3);

echo $OUTPUT->heading('Simplest way to make a table', 4);

$table = new html_table();
$table->head = array('Name', 'Grade');
$table->data = array(
    array('Harry Potter', '76%'),
    array('Hermione Granger', '100%'),
);
echo html_writer::table($table);

echo $OUTPUT->heading('Column headings spanning multiple columns', 4);

// with column heading spanning multiple columns
$table = new html_table();
$table->head = array('Name', 'Location');
$table->headspan = array(1,2);
$table->data = array(
    array('Bob', '75S', '24E'),
    array('Joe', '10N', '145W'),
);
echo html_writer::table($table);

echo $OUTPUT->heading('With fine-grained control over cells', 4);

$table = new html_table();
$cell1 = new html_table_cell();
$cell1->text = 'This cell has a colspan applied';
$cell1->colspan = 2;
$row1 = new html_table_row();
$row1->cells[] = $cell1;
$cell2 = new html_table_cell();
$cell2->text = 'Normal cell';
$cell3 = new html_table_cell();
$cell3->text = 'Normal cell';
$row2 = new html_table_row();
$row2->cells = array($cell2, $cell3);
$table->data = array($row1, $row2);
echo html_writer::table($table);

echo $OUTPUT->heading('Applying classes and ids to every element in a table', 4);

// Don't use align, size or wrap properties - assign all styles via stylesheets using classes instead
$table = new html_table();
$table->head = array('Name', 'Grade');
$table->rowclasses = array('first-row-class', 'second-row-class');
$table->colclasses = array('first-col-class', 'second-col-class');
$table->data = array(
    array('Joe Bloggs', '76%'),
    array('Jane Doe', '100%'),
);
echo html_writer::table($table);

echo $OUTPUT->heading('Applying more specific ids/classes to any element', 4);

// applying more specific ids/classes to any element
$table = new html_table();
$head1 = new html_table_cell();
$head1->text = 'Left Head';
$head1->header = true;
$head1->id = 'left-head-id';
$head1->attributes['class'] = 'left-head-class';
$head2 = new html_table_cell();
$head2->text = 'Right Head';
$head2->header = true;
$head2->id = 'right-head-id';
$head2->attributes['class'] = 'right-head-class';
$head = new html_table_row();
$head->cells = array($head1, $head2);
$head->id = 'header-id';
$head->attributes['class'] = 'header-class';
$cell1 = new html_table_cell();
$cell1->id = 'top-left-cell-id';
$cell1->attributes['class'] = 'top-left-cell-class';
$cell1->text = 'Top Left';
$cell2 = new html_table_cell();
$cell2->id = 'top-right-cell-id';
$cell2->attributes['class'] = 'top-right-cell-class';
$cell2->text = 'Top Right';
$row1 = new html_table_row();
$row1->cells = array($cell1, $cell2);
$row1->id = 'row1-id';
$row1->attributes['class'] = 'row1-class';
$cell3 = new html_table_cell();
$cell3->id = 'bottom-left-cell-id';
$cell3->attributes['class'] = 'bottom-left-cell-class';
$cell3->text = 'Bottom Left';
$cell4 = new html_table_cell();
$cell4->id = 'bottom-right-cell-id';
$cell4->attributes['class'] = 'bottom-right-cell-class';
$cell4->text = 'Bottom Right';
$row2 = new html_table_row();
$row2->cells = array($cell3, $cell4);
$row2->id = 'row2-id';
$row2->attributes['class'] = 'row2-class';
$table->data = array($head, $row1, $row2);
echo html_writer::table($table);


echo $OUTPUT->heading('Example of a table with headings in row instead of column', 4);
// note the use of 'scope' for accessibility. See:
// http://www.w3.org/TR/WCAG20-TECHS/H63
$table = new html_table();
$head1 = new html_table_cell();
$head1->text = 'First Heading';
$head1->scope = 'row';
$head1->header = true;
$cell1 = new html_table_cell();
$cell1->text = 'Data associated with First Heading';
$cell2 = new html_table_cell();
$cell2->text = 'More data associated with First Heading';
$row1 = new html_table_row();
$row1->cells = array($head1, $cell1, $cell2);
$head2 = new html_table_cell();
$head2->text = 'Second Heading';
$head2->scope = 'row';
$head2->header = true;
$cell3 = new html_table_cell();
$cell3->text = 'Data associated with Second Heading';
$cell4 = new html_table_cell();
$cell4->text = 'More data associated with Second Heading';
$row2 = new html_table_row();
$row2->cells = array($head2, $cell3, $cell4);
$table->data = array($row1, $row2);
echo html_writer::table($table);

echo $OUTPUT->heading('Turning off the default "generaltable" classes is not very easy, you have to override the class attribute on the table element. Note that setting any class, will by default remove the generaltable class', 4);

$table = new html_table();
$table->head = array('Name', 'Grade');
$table->attributes = array('class' => ""); // need some other class stop the "generaltable" class
// but no easy way to do the same on the cells
$table->head = array('Name', 'Grade');
$table->data = array(
    array('Joe Bloggs', '76%'),
    array('Jane Doe', '100%'),
);
echo html_writer::table($table);

echo $OUTPUT->heading('If you need a plain table, you can build it yourself using html_writer:', 4);

echo html_writer::start_tag('table');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', 'Left Heading');
echo html_writer::tag('th', 'Right Heading');
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('td', 'Top Left');
echo html_writer::tag('td', 'Top Right');
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('td', 'Bottom Left');
echo html_writer::tag('td', 'Bottom Right');
echo html_writer::end_tag('tr');
echo html_writer::end_tag('table');

echo $OUTPUT->heading('A "flexible table" moodle table (class "flexible"). Useful if you want pagination, sorting etc.', 3);

require_once($CFG->libdir . '/tablelib.php');
$table = new flexible_table('test-flexible-table');
$table->define_baseurl($url);
$table->define_columns(array('col1', 'col2'));
$table->define_headers(array('Column 1', 'Column 2'));
$table->sortable(true, 'col1', SORT_ASC);
$table->setup();
$table->add_data(array('Top Left', 'Top Right'));
$table->add_data(array('Bottom Left', 'Bottom Right'));
$table->finish_html();

echo $OUTPUT->heading('By default tables are left aligned. Use the boxaligncenter class if you want the table to be centred instead', 4);

$table = new html_table();
$table->attributes['class'] = 'boxaligncenter';
$table->head = array('Name', 'Grade');
$table->data = array(
    array('Harry Potter', '76%'),
    array('Hermione Granger', '100%'),
);
echo html_writer::table($table);

echo $OUTPUT->heading('You can specify \'hr\' as a row to get a horizontal rule that spans all columns. Note this only works if you have the table \'head\' defined!', 4);

$table = new html_table();
$table->head = array('Name', 'Grade');
$table->data = array(
    array('Harry Potter', '76%'),
    array('Hermione Granger', '100%'),
    'hr',
    array('Harry Potter', '76%'),
    array('Hermione Granger', '100%'),
);
echo html_writer::table($table);


echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
