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
 * Task to delete users if marked as disabled in external DB and the
 * deletion timespan indicated in settings.php is reached.
 *
 * @package    local_nbpmetadatasend
 * @copyright   2023 ILD TH Lübeck <dev.ild@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_nbpmetadatasend\task;

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/local/nbpmetadatasend/locallib.php');

use stdClass;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}



/**
 * Represents a scheduled task for deleting Possehl users.
 * Extends the core\task\scheduled_task class.
 */
class putdata_cron extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('put_data_cron', 'local_nbpmetadatasend');
    }



    public function execute() {
        start_putdata_process();
    }
}


function start_putdata_process() {
    echo "start_putdata_process";
    $baseurl = get_baseurl();
    //echo "baseurl = " . $baseurl;

    $sourceslug = get_source_slug();
    //echo "sourceslug = " . $sourceslug;

    $clientid = get_clientid();
    //echo "clientid = " . $clientid;

    $clientsecret = get_clientsecret();
    //echo "clientsecret = " . $clientsecret;

    $courseIds = get_courseIds();
    //echo "courseIds = " . $courseIds;

    $tokenfile = "token_info.json";

    $token = get_nbp_token($tokenfile, $clientid, $clientsecret);

    // Beispielverwendung
    //echo "<br/>token = " . $token . "<br/>";

    //$results = get_course_metadata($courseIds);
    // test to echo all required data to http://localhost/local/nbpmetadatasend/get_course_data.php
    global $CFG;
    require_once(__DIR__ . '/../../../../config.php');
    $systemUrl = $CFG->wwwroot;
    $url = $systemUrl . '/local/ildmeta/get_moochub_courses.php';
    // Beispielnutzung der Funktion
    //require_once($CFG->dirroot . '/local/nbpmetadatasend/locallib.php');
    $courseIds = get_courseIds();
    $uuids = get_uuids_by_courseids($courseIds);

    $filteredCourses = getFilteredCoursesData($url, $uuids);
    if ($filteredCourses) {
        print_r($filteredCourses); // Gibt die gefilterten Kursdaten aus
    } else {
        echo "Es konnten keine gefilterten Kursdaten abgerufen werden.";
    }
    //echo "<br/>convert_moochub_to_amb<br/> ";
    foreach ($filteredCourses as $result) {
        foreach ($result as $object) {

            if (isset($object->uuid)) {
                $uuid = $object->uuid;
                $jsonData = json_encode($object);
                echo "put jsonData = " . $jsonData;

                $dataArray = json_decode($jsonData, true);

                // Zugriff auf die UUID
                //$uuid = $dataArray['uuid'];
                echo "Die UUID ist: " . $uuid;

                $url = $baseurl . '/api/course-v2/' . $sourceslug . '/' . $uuid;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        "accept: text/plain",
                        "Authorization: Bearer " . $token,
                        "Content-Type: application/json",
                    )
                );

                $response = curl_exec($ch);
                $err = curl_error($ch);

                // Status-Code ermitteln
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // cURL-Session schließen
                curl_close($ch);

                // Auf Fehler überprüfen und Antwort verarbeiten
                if ($err) {
                    echo "cURL Error #: " . $err;
                } else {
                    echo "HTTP Status Code: " . $http_status . "\n";
                    echo "Response: " . $response;
                }
            }
        }
    }
}
