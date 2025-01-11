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

    global $CFG;
    require_once(__DIR__ . '/../../../../config.php');
    $systemUrl = $CFG->wwwroot;
    $url = $systemUrl . '/local/ildmeta/get_moochub_courses.php';

    $uuids = get_uuids_by_courseids($courseIds);

    $filteredCourses = getFilteredCoursesData($url, $uuids);

    echo "filteredCourses: ";
    var_dump($filteredCourses);
    if ($filteredCourses) {
        echo ("filtered courses: ");
        //var_dump($filteredCourses); // Gibt die gefilterten Kursdaten aus
    } else {
        echo "Es konnten keine gefilterten Kursdaten abgerufen werden.";
    }


    foreach ($filteredCourses as $result) {
        $cleanstring = preg_replace('/\s*class=["\'][^"\']*["\']/', '', $result["attributes"]["description"]);

        $data = [
            "id" => $result['id'],
            "title" => $result["attributes"]["name"],
            //"description" => $result["attributes"]["description"],
            "description" => $cleanstring,
            "courseUrl" => $result["attributes"]["url"],
            //"logoUrl" => $result["attributes"]["image"]["contentUrl"],
            "logoUrl" => 'localhost',
            "language" => $result["attributes"]["inLanguage"][0],
            "alternativeCourseUrl" => $result["attributes"]["url"],
            "courseCreator" => $result["attributes"]["instructor"][0]["name"],
            //"courseCreatorLogoUrl" => "https://dev-isy.th-luebeck.de/moodle3/pluginfile.php/1/local_ildmeta/provider/1/fhl-intern-logo.png",
            "courseCreatorLogoUrl" => "localhost",
            "cost" => 0.0,
            "courseMode" => $result["attributes"]["courseMode"][0],
            "street" => null,
            "postalCode" => null,
            "city" => null,
            "latitude" => null,
            "longitude" => null,
            "startDate" => null,
            "endDate" => null,

        ];

        $url = $baseurl . '/api/course-v2/' . $sourceslug . '/' . $result['id'];
        //echo "url = " . $url;

        // cURL-Session initialisieren
        $ch = curl_init($url);

        // Optionen für cURL setzen
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Methode: PUT
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Antwort als String zurückgeben
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: */*',
            'Authorization: Bearer ' . $token, // Ersetze "xxx" durch deinen echten Token
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // JSON-Daten senden

        // Anfrage ausführen und Antwort speichern
        $response = curl_exec($ch);

        // Fehler prüfen
        if (curl_errno($ch)) {
            echo 'cURL-Fehler: ' . curl_error($ch);
        }

        // Antwort ausgeben
        echo $response;

        // cURL-Session schließen
        curl_close($ch);
        echo "curl closed";
        // Auf Fehler überprüfen und Antwort verarbeiten
        if ($err) {
            echo "cURL Error #: " . $err;
        } else {
            echo "HTTP Status Code: " . $http_status . "\n";
            echo "Response: " . $response;
        }
    }
}
