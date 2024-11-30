<?php

require_once(__DIR__ . '/../../config.php');

global $CFG; 
// test to echo all required data to http://localhost/local/nbpmetadatasend/get_course_data.php
$systemUrl = $CFG->wwwroot;
$url = $systemUrl . '/local/ildmeta/get_moochub_courses.php';
// Beispielnutzung der Funktion
require_once($CFG->dirroot . '/local/nbpmetadatasend/locallib.php');
$courseIds = get_courseIds();
$uuids = get_uuids_by_courseids($courseIds);

$filteredCourses = getFilteredCoursesData($url, $uuids);
if ($filteredCourses) {
    print_r($filteredCourses); // Gibt die gefilterten Kursdaten aus
} else {
    echo "Es konnten keine gefilterten Kursdaten abgerufen werden.";
}
