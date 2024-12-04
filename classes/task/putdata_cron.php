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
    /*
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
        echo ("filtered courses: ");
        var_dump($filteredCourses); // Gibt die gefilterten Kursdaten aus
    } else {
        echo "Es konnten keine gefilterten Kursdaten abgerufen werden.";
    }
    echo "<br/>convert_moochub_to_amb<br/> ";
    $jsonData = convert_moochub_to_amb($filteredCourses);
    echo "jsonData nach amb: " . $jsonData;
    //$cleanJson = remove_html_from_json($jsonData);
    
    $cleanJson = '{
        "title": "DT2 Dummykurs",
        "description": "halloLernzielehalloKursaufbauhallo",
        "courseUrl": "http:\/\/localhost\/course\/view.php?id=4",
        "language": "de",
        "cost": 0,
        "courseMode": "online",
        "logoUrl": "http:\/\/localhost\/pluginfile.php\/1\/local_ildmeta\/provider\/1\/2024-11-30_09-23-31.png"
    }'; 
    echo "cleanJson: " . $cleanJson;

    foreach ($filteredCourses as $result) {
        //foreach ($result as $object) {

        if (isset($result['id'])) {
            $uuid = $result['id'];
            echo "Die UUID ist: " . $uuid;

            //$jsonData = json_encode($object);
            echo "put cleanJson = " . $$cleanJson;

            $dataArray = json_decode($$cleanJson, true);

            // Zugriff auf die UUID
            //$uuid = $dataArray['uuid'];
            echo "Die UUID ist: " . $uuid;

            $url = $baseurl . '/api/course-v2/' . $sourceslug . '/' . $uuid;
            echo "url: " . $url;
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
            // }
        }
    }
        
*/
    // URL des Endpunkts
    $url = 'https://dam.demo.meinbildungsraum.de/push-connector/api/course-v2/Trainspot2-THL/78ca3023-9e76-4cab-9391-61f82dcf362c';

    // Daten, die gesendet werden sollen
    $data = [
        "title" => "Digital Trainer II - Crossing the Edge",
        "description" => "Im großen Ganzen geht es in der Bildungsstory »Crossing the Edge« um geistige Beweglichkeit und die Bereitschaft, das eigene Digitale Mindset zu erweitern – sich mentales Rüstzeug für die Zukunft anzueignen. Es geht um Offenheit und das gute Gefühl, über sich hinauszuwachsen. Es geht um Kollaboration und gegenseitiges Lehren und Dazulernen. Und nicht zuletzt geht‘s um den Spaß am Neuen und am Lernerfolg: besser werden, Lösungen finden, Möglichkeiten nutzen.",
        "courseUrl" => "https://dev-isy.th-luebeck.de/moodle3/course/view.php?id=35",
        "language" => "de",
        "cost" => 0,
        "courseMode" => "online",
        "logoUrl" => "https://dev-isy.th-luebeck.de/moodle3/pluginfile.php/1/local_ildmeta/provider/1/fhl-intern-logo.png"
    ];

    // cURL-Session initialisieren
    $ch = curl_init($url);

    // Optionen für cURL setzen
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Methode: PUT
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Antwort als String zurückgeben
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: */*',
        'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJzSkx6NXRqRU1LZ0hKbGxvdVFRbThHX2s3MkhCUzZfcHdPSHZGYzRmTTFjIn0.eyJleHAiOjE3MzMyOTc3NDAsImlhdCI6MTczMzI5NzQ0MCwianRpIjoiZDIwZWE5OTMtZWU1Mi00NzJjLTk1NWMtNjUyNDIwYTFkNDE4IiwiaXNzIjoiaHR0cHM6Ly9hYWkuZGVtby5tZWluYmlsZHVuZ3NyYXVtLmRlL3JlYWxtcy9uYnAtYWFpIiwiYXVkIjoiYWNjb3VudCIsInN1YiI6ImU0NmE0OTQwLTE2ZTctNGIwZC1hZDY2LTIwOWJjM2NhMGVkOCIsInR5cCI6IkJlYXJlciIsImF6cCI6IjI4ZjEwNWM5LWEyZjAtNDBkMy1iMDhiLTdhYWMwZTA1ODQ3ZSIsImFjciI6IjEiLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsib2ZmbGluZV9hY2Nlc3MiLCJ1bWFfYXV0aG9yaXphdGlvbiIsImRlZmF1bHQtcm9sZXMtbmJwLWFhaSJdfSwicmVzb3VyY2VfYWNjZXNzIjp7ImFjY291bnQiOnsicm9sZXMiOlsibWFuYWdlLWFjY291bnQiLCJtYW5hZ2UtYWNjb3VudC1saW5rcyIsImRlbGV0ZS1hY2NvdW50Iiwidmlldy1wcm9maWxlIl19LCIyOGYxMDVjOS1hMmYwLTQwZDMtYjA4Yi03YWFjMGUwNTg0N2UiOnsicm9sZXMiOlsiIGRhbS1zb3VyY2Utb3duZXI6VHJhaW5TcG90Mi1USEwiLCJkYW0tc291cmNlLW93bmVyOlRyYWluc3BvdDItVEhMIl19fSwic2NvcGUiOiJob21laWRwIiwiY2xpZW50SG9zdCI6IjEwLjY1LjguNyIsImNsaWVudEFkZHJlc3MiOiIxMC42NS44LjciLCJjbGllbnRfaWQiOiIyOGYxMDVjOS1hMmYwLTQwZDMtYjA4Yi03YWFjMGUwNTg0N2UifQ.dz8OI6Cgz115nLo7LjIp7KPeU8DBfG2MGJcVxd2CeFR4iGwNWht9G6B2F1i19Uh85B3cL-nT9cNfcdQNjXqXeLOdZvJ3AR3-ou2bYfG5PqBzjUkwdsXeFqjScY1E4gvaRZpZ_JqF38rZ6K11NO_BdiekzLVoEPB_LSZJKWr76xNkCPjnWbZD2VoyqoM_jXg3V9p6EcmcSom6aWocERu13aOhMNcJmDRpkV973hWdg2KzeHNgpLG1PxsHrQqmhouBdjO5Dp4GK5ZOpWX-9KpYYYIioTuQujyUksUuy6RZ3CKUHDKksZsZxmMsV-nBA5GxOpOd3lhMhE_Refv6bee6jw', // Ersetze "xxx" durch deinen echten Token
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
}
