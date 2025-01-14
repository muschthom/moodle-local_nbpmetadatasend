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
    $url = $systemUrl . '/local/ildmeta/get_moochub_courses.php';

    $uuids = get_uuids_by_courseids($courseIds);

    $filteredCourses = getFilteredCoursesData($url, $uuids);

    if (!$filteredCourses) {
        echo "No courses found with the given courseIds.";
    }

    foreach ($filteredCourses as $result) {
        //var_dump($result);

        $teasertext = $DB->get_field('ildmeta', 'teasertext', ['uuid' => $result['id']]);
        $plainText = strip_tags($teasertext);
        $cleanstring = preg_replace('/\s*class=["\'][^"\']*["\']/', '', $plainText);
        $result["attributes"]["description"] = $cleanstring;

        global $DB; // Moodle-Datenbank-Objekt

        // Kurs-ID festlegen
        $courseid = get_courseid_by_uuid($result['id']);

        // SQL-Abfrage definieren
        $sql = "
            SELECT 
                c.shortname,
                c.description,
                c.idnumber
            FROM 
                {competency_coursecomp} cc
            JOIN 
                {competency} c
            ON 
                cc.competencyid = c.id
            WHERE 
                cc.courseid = :courseid
        ";

        // Parameter für die Abfrage
        $params = ['courseid' => $courseid];

        // Abfrage ausführen
        $competencies = $DB->get_records_sql($sql, $params);

        $result["attributes"]["teaches"] = []; // Initialisiere das teaches-Array

        // Ergebnisse verarbeiten
        if ($competencies) {
            foreach ($competencies as $competency) {
                echo "Shortname: " . $competency->shortname . "\n";
                echo "Description: " . $competency->description . "\n";
                echo "ID Number: " . $competency->idnumber . "\n";
                echo "-------------------------\n";

                $result["attributes"]["teaches"][] = [
                    "name" => [
                        [
                            "inLanguage" => "de",
                            "name" => $competency->shortname
                        ]
                    ],
                    "description" => $competency->description,
                    "educationalFramework" => "GRETA",
                    "educationalFrameworkVersion" => "2",
                    "url" => "www.example.com",
                    "targetUrl" => "www.example.com",
                    "educationalLevel" => [
                        "educationalFramework" => "GRETA"
                    ]
                ];
            }
        } else {
            echo "Keine Kompetenzen für den Kurs mit der ID $courseid gefunden.\n";
        }

        /*
        $result["attributes"]["teaches"]["name"]["inLanguage"] = "de"; 
        $result["attributes"]["teaches"]["name"]["inLanguage"] = $competency->shortname; 
        $result["attributes"]["teaches"]["description"] = $competency->description;
        $result["attributes"]["teaches"]["educationalFramework"] = "GRETA";
        $result["attributes"]["teaches"]["educationalFrameworkVersion"] = "2";
        $result["attributes"]["teaches"]["url"] = "www.example.com";
        $result["attributes"]["teaches"]["targetUrl"] = "www.example.com";
        $result["attributes"]["teaches"]["educationalLevel"]["educationalFramework"] = "GRETA";


        /*
        $data = [
            "id" => $result['id'],
            "title" => $result["attributes"]["name"],
            //"description" => $result["attributes"]["description"],
            "description" => $cleanstring,
            "courseUrl" => $result["attributes"]["url"],
            //"logoUrl" => $result["attributes"]["image"]["contentUrl"],
            "logoUrl" => 'localhost123',
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

        $json_data = json_encode($data);
        /*
        //example code from https://dam.demo.meinbildungsraum.de/datenraum/swagger/index.html
        $json_data = '{
            "title": "Englisch lernen",
            "description": "Aus dieser Volltext-Beschreibung sollte Umfang und Form meines Kursinhalts hervor gehen.",
            "courseUrl": "https://example.com/kurse/englisch_lernen/index",
            "logoUrl": "https://example.com/logo.png",
            "language": "de",
            "alternativeCourseUrl": "https://example.com/kurse/englisch_lernen/index",
            "courseCreator": "Lernimus",
            "courseCreatorLogoUrl": "https://example.com/lernimus-logo.png",
            "cost": 1234.56,
            "courseMode": "online",
            "street": "Kapelle-Ufer 2",
            "postalCode": "D-10117",
            "city": "Berlin",
            "latitude": "52.52278002340085",
            "longitude": "13.375684430248915",
            "startDate": "2023-11-23T13:33:55.123456Z",
            "endDate": "2023-11-23T13:33:55.123456Z"
          }';
          */

        $json_data = json_encode($result);
        //echo $json_data; 
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
