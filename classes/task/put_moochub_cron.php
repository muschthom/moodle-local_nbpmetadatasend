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


class put_moochub_cron extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('put_moochub_cron', 'local_nbpmetadatasend');
    }



    public function execute() {
        start_put_moochub_data_process();
    }
}


function start_put_moochub_data_process() {
    require_once(__DIR__ . '/../../../../config.php');
    global $CFG, $DB;

    $baseurl = get_baseurl();

    $sourceslug = get_source_slug();

    $clientid = get_clientid();

    $clientsecret = get_clientsecret();

    $courseIds = get_courseIds();

    $token = return_token($clientid, $clientsecret);

    $systemUrl = $CFG->wwwroot;
    //$url = $systemUrl . '/local/ildmeta/get_moochub_courses.php';
    $url = $systemUrl . '/local/nbpmetadatasend/get_trainspot_courses.php';


    $uuids = get_uuids_by_courseids($courseIds);

    $filteredCourses = getFilteredCoursesData($url, $uuids);

    if (!$filteredCourses) {
        echo "No courses found with the given courseIds.";
    }

    foreach ($filteredCourses as $result) {
       
        $json_data = json_encode($result);

        echo $json_data; 
        $url = $baseurl . '/api/moochub/' . $sourceslug;

        $ch = curl_init($url);

        // Optionen für cURL setzen
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Methode: PUT
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Antwort als String zurückgeben
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: */*',
            'Authorization: Bearer ' . $token, // Ersetze "xxx" durch deinen echten Token
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data); // JSON-Daten senden

        // Anfrage ausführen und Antwort speichern
        $response = curl_exec($ch);

        // Fehler prüfen
        if (curl_errno($ch)) {
            echo 'cURL-Fehler: ' . curl_error($ch);
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //echo "HTTP Status Code: " . $http_status . "\n";

        if ($http_status == 204) {
            echo "Metadata successfully saved/updated for course " . $result["attributes"]["name"] .
                " with uuid = " . $result['id'] .  "\n";
        }

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
