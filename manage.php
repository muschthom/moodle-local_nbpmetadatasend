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
 * Management page for NBP Metadata Send
 *
 * @package    local_nbpmetadatasend
 * @copyright  2025 ILD TH Lübeck <dev.ild@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__ . '/locallib.php');

admin_externalpage_setup('local_nbpmetadatasend_manage');

$PAGE->set_url(new moodle_url('/local/nbpmetadatasend/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('managepage', 'local_nbpmetadatasend'));
$PAGE->set_heading(get_string('managepage', 'local_nbpmetadatasend'));

// Handle form submission
$submitted_getdata = optional_param('submitbuttongetdata', '', PARAM_TEXT);
$submitted_putdata = optional_param('submitbuttonputdata', '', PARAM_TEXT);
$submitted_deletedata = optional_param('submitbuttondeletedata', '', PARAM_TEXT);
$inputvalue_putdata = optional_param('inputfield_putdata', '', PARAM_TEXT);
$inputvalue_deletedata = optional_param('inputfielddeletedata', '', PARAM_TEXT);


echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('managepageheading', 'local_nbpmetadatasend'));





// Create the form to get data from Datenraum slug defined in settings page
echo html_writer::start_tag('form', array(
    'method' => 'post',
    'action' => new moodle_url('/local/nbpmetadatasend/manage.php')
));

// Add sesskey for security
echo html_writer::empty_tag('input', array(
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey()
));

// Add sub headline
echo html_writer::tag('h3', get_string('getdataprocessheading', 'local_nbpmetadatasend'));


if ($submitted_getdata && confirm_sesskey()) {
    // Execute the getdata process from slug
    $output = get_metadata_from_slug();
    
    \core\notification::success(get_string('datasaved', 'local_nbpmetadatasend'));
    
    if (!empty($output)) {
        echo html_writer::div(
            '<pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto; overflow-x: auto; font-size: 12px; line-height: 1.4; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">' . 
            htmlspecialchars($output) . 
            '</pre>', 
            'alert alert-info mt-3'
        );
    }
}

// Add description
echo html_writer::div(get_string('getdataprocessdesc', 'local_nbpmetadatasend'), 'mb-3');

// Add submit button
echo html_writer::start_div('form-group mt-3');
echo html_writer::empty_tag('input', array(
    'type' => 'submit',
    'name' => 'submitbuttongetdata',
    'value' => get_string('submitbuttongetdata', 'local_nbpmetadatasend'),
    'class' => 'btn btn-primary'
));
echo html_writer::end_div();

echo html_writer::end_tag('form');














// Create the form to put data to Datenraum slug defined in settings page
echo html_writer::start_tag('form', array(
    'method' => 'post',
    'action' => new moodle_url('/local/nbpmetadatasend/manage.php')
));

// Add sesskey for security
echo html_writer::empty_tag('input', array(
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey()
));

// Add sub headline
echo html_writer::tag('h3', get_string('putdataprocessheading', 'local_nbpmetadatasend'));


if ($submitted_putdata && confirm_sesskey()) {
    // Execute the putdata process from slug
    $output = put_metadata_to_slug($inputvalue_putdata);
    
    \core\notification::success(get_string('datasaved', 'local_nbpmetadatasend'));
    
    if (!empty($output)) {
        echo html_writer::div(
            '<pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto; overflow-x: auto; font-size: 12px; line-height: 1.4; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">' . 
            htmlspecialchars($output) . 
            '</pre>', 
            'alert alert-success mt-3'
        );
    }
}

// Add description
echo html_writer::div(get_string('putdataprocessdesc', 'local_nbpmetadatasend'), 'mb-3');


// Add input field
echo html_writer::start_div('form-group');
echo html_writer::label(get_string('inputlabel', 'local_nbpmetadatasend'), 'inputfield', true, array('class' => 'form-label'));
echo html_writer::empty_tag('input', array(
    'type' => 'text',
    'name' => 'inputfield_putdata',
    'id' => 'inputfield_putdata',
    'class' => 'form-control',
    'value' => $inputvalue_putdata
));
echo html_writer::end_div();

// Add submit button
echo html_writer::start_div('form-group mt-3');
echo html_writer::empty_tag('input', array(
    'type' => 'submit',
    'name' => 'submitbuttonputdata',
    'value' => get_string('submitbuttonputdata', 'local_nbpmetadatasend'),
    'class' => 'btn btn-primary'
));
echo html_writer::end_div();

echo html_writer::end_tag('form');




// Create the form to delete data from Datenraum slug defined in settings page
echo html_writer::start_tag('form', array(
    'method' => 'post',
    'action' => new moodle_url('/local/nbpmetadatasend/manage.php')
));

// Add sesskey for security
echo html_writer::empty_tag('input', array(
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey()
));

// Add sub headline
echo html_writer::tag('h3', get_string('deletedataprocessheading', 'local_nbpmetadatasend'));


if ($submitted_deletedata && confirm_sesskey()) {
    // Execute the putdata process from slug
    $output = delete_metadata_to_slug($inputvalue_deletedata);
    
    \core\notification::success(get_string('datasaved', 'local_nbpmetadatasend'));
    
    if (!empty($output)) {
        echo html_writer::div(
            '<pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto; overflow-x: auto; font-size: 12px; line-height: 1.4; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">' . 
            htmlspecialchars($output) . 
            '</pre>', 
            'alert alert-success mt-3'
        );
    }
}

// Add description
echo html_writer::div(get_string('deletedataprocessdesc', 'local_nbpmetadatasend'), 'mb-3');


// Add input field
echo html_writer::start_div('form-group');
echo html_writer::label(get_string('inputlabel_deletedata', 'local_nbpmetadatasend'), 'inputfield', true, array('class' => 'form-label'));
echo html_writer::empty_tag('input', array(
    'type' => 'text',
    'name' => 'inputfielddeletedata',
    'id' => 'inputfielddeletedata',
    'class' => 'form-control',
    'value' => $submitted_deletedata
));
echo html_writer::end_div();

// Add submit button
echo html_writer::start_div('form-group mt-3');
echo html_writer::empty_tag('input', array(
    'type' => 'submit',
    'name' => 'submitbuttondeletedata',
    'value' => get_string('submitbuttondeletedata', 'local_nbpmetadatasend'),
    'class' => 'btn btn-primary'
));
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo $OUTPUT->footer();
