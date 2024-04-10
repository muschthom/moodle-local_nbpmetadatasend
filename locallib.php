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
 * Internal library of functions for module nbpmetadatasend
 *
 * @package    local
 * @subpackage nbpmetadatasend
 * @copyright   2023 ILD TH Lübeck <dev.ild@th-luebeck.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../config.php');


/**
 * Retrieves data from the database matching certain criteria (see sql-statement).
 *
 * @return mysqli_result|bool The result of the database query or false if there is no connection.
 */
function get_course_metadata($courseids)
{
    // Umwandeln des Strings in ein Array
    $courseidsArray = explode(",", $courseids);

    // Stelle sicher, dass diese Datei Teil von Moodle ist
    global $DB;

    foreach ($courseidsArray as $courseid) {

        // SQL-Abfrage vorbereiten
        $sql = "SELECT * FROM {ildmeta} WHERE courseid = :courseid";

        // Die Abfrage durchführen
        $results = $DB->get_records_sql($sql, array('courseid' => $courseid));

        // Ergebnisse verarbeiten
        foreach ($results as $result) {
            // Mach etwas mit jedem Ergebnis
            echo "coursetitle = " . $result->coursetitle . '<br>'; // Oder jede andere Spalte in deiner Tabelle
            echo "lecturer = " . $result->lecturer . '<br>'; // Oder jede andere Spalte in deiner Tabelle
            echo "teasertext = " . $result->teasertext . '<br>'; // Oder jede andere Spalte in deiner Tabelle
        }
    }
    return $results;
}


function convert_moochub_to_amb($results)
{
    $courseAttributes = $results['data'][0]['attributes'];

    foreach ($courseAttributes as $result) {
        $convertedResults[] = [
            'title' => $result->coursetitle, // oder der entsprechende Spaltenname
            'description' => $result->coursedescription, // oder der entsprechende Spaltenname
            'uri' => $result->courseurl, // oder der entsprechende Spaltenname
            'cost' => isset($result->coursecost) ? (float)$result->coursecost : 0, // Annahme: coursecost ist der Feldname für die Kosten
        ];
    }

    foreach ($convertedResults as $courseData) {
        echo "Titel: " . $courseData['title'] . "<br>";
        echo "Beschreibung: " . $courseData['description'] . "<br>";
        echo "URI: " . $courseData['uri'] . "<br>";
        echo "Kosten: " . $courseData['cost'] . "<br><br>";
    }
}

function put_data_to_nbp($data, $baseUrl, $sourceSlug, $courseId, $tokenFile, $clientId, $clientSecret)
{
    $url = $baseUrl . '/api/course/' . $sourceSlug . '/' . $courseId;
    require_once "get-bearer-token.php";
    $token = get_token($tokenFile, $clientId, $clientSecret);
}


function get_course_ids()
{
    global $DB;
    if ($DB->get_records('config')) {
        $tableobj = $DB->get_record('config', ['name' => 'local_importpossehl_tablename']);
        $tablename = $tableobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $tablename;
}

function get_baseurl()
{
    global $DB;
    if ($DB->get_records('config')) {
        $baseurlobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_baseurl']);
        $baseurl = $baseurlobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $baseurl;
}

function get_source_slug()
{
    global $DB;
    if ($DB->get_records('config')) {
        $sourceslugobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_sourceSlug']);
        $sourceslug = $sourceslugobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $sourceslug;
}

function get_clientid()
{
    global $DB;
    if ($DB->get_records('config')) {
        $clientidobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_clientId']);
        $clientid = $clientidobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $clientid;
}

function get_clientsecret()
{
    global $DB;
    if ($DB->get_records('config')) {
        $clientsecretobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_clientSecret']);
        $clientsecret = $clientsecretobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $clientsecret;
}

function get_courseIds()
{
    global $DB;
    if ($DB->get_records('config')) {
        $courseIdsobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_courseIds']);
        $courseIds = $courseIdsobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    return $courseIds;
}


function get_new_token($tokenFile, $clientId, $clientSecret)
{

    //TODO: URL anpassen, passt derzeit nicht mit URL aus DB zusammen
    $url = "https://aai.demo.meinbildungsraum.de/realms/nbp-aai/protocol/openid-connect/token";
    $credentials = base64_encode("$clientId:$clientSecret");

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $credentials",
        "Content-Type: application/x-www-form-urlencoded"
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($statusCode == 200) {
        $data = json_decode($response, true);
        $expiresIn = $data["expires_in"];
        $accessToken = $data["access_token"];

        // Speichere den Token und den Ablaufzeitpunkt
        $tokenInfo = [
            "token" => $accessToken,
            "expires" => time() + $expiresIn - 30 // 30 Sekunden Sicherheitspuffer
        ];
        file_put_contents($tokenFile, json_encode($tokenInfo));
    } else {
        // Fehlerbehandlung
        echo "Fehler beim Abrufen des Tokens: HTTP-Status $statusCode\n";
    }

    curl_close($curl);
}

function get_token($tokenFile, $clientId, $clientSecret)
{

    if (!file_exists($tokenFile)) {
        get_new_token($tokenFile, $clientId, $clientSecret);
    }

    $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    if (time() >= $tokenInfo["expires"]) {
        get_new_token($tokenFile, $clientId, $clientSecret);
        $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    }

    return $tokenInfo["token"];
}
