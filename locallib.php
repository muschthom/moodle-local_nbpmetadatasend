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
function get_course_isymetadata($courseids) {
    global $DB;
    $results = [];


    foreach ($courseids as $courseid) {

        // Trim leere Zeichen und prüfe, ob der courseid eine gültige Ganzzahl ist
        $courseid = trim($courseid);

        // Prüfen, ob courseid nur aus Ziffern besteht und positiv ist
        if (!ctype_digit($courseid) || (int)$courseid <= 0) {
            // Wenn ungültig, überspringe diesen Wert
            continue;
        }


        // SQL-Abfrage vorbereiten
        $sql = "SELECT * FROM {ildmeta} WHERE courseid = :courseid";

        // Die Abfrage durchführen
        $results[] = $DB->get_record_sql($sql, array('courseid' => $courseid));
    }
    return $results;
}


function get_competency_description($compID, $jsonFilePath) {
    // Prüfen, ob compID "GRETA" enthält
    if (strpos($compID, 'GRETA') !== false) {
        // JSON-Datei laden
        $jsonData = file_get_contents($jsonFilePath);
        if ($jsonData === false) {
            die("Error loading JSON file.");
        }

        // JSON-Daten in ein assoziatives Array konvertieren
        $competencyModel = json_decode($jsonData, true);

        if ($competencyModel === null) {
            die("Error decoding JSON.");
        }

        // compID zerlegen in GRETA, Aspect, Area, Facet, Requirement
        $parts = explode('-', $compID);

        // Prüfen, ob die compID die richtige Struktur hat (GRETA-Aspect-Area-Facet-Requirement)
        if (count($parts) === 5) {
            $aspectIndex = (int)$parts[1] - 1;    // Kompetenzaspekt (1-basiert)
            $areaIndex = (int)$parts[2] - 1;      // Kompetenzbereich (1-basiert)
            $facetIndex = (int)$parts[3] - 1;     // Kompetenzfacette (1-basiert)
            $requirementIndex = (int)$parts[4] - 1; // Kompetenzanforderung (1-basiert)

            // Zugriff auf die gewünschte Beschreibung
            if (isset($competencyModel['Kompetenzmodell']['Kompetenzaspekte'][$aspectIndex]['Kompetenzbereiche'][$areaIndex]['Kompetenzfacetten'][$facetIndex]['Kompetenzanforderungen'][$requirementIndex])) {
                $description = $competencyModel['Kompetenzmodell']['Kompetenzaspekte'][$aspectIndex]['Kompetenzbereiche'][$areaIndex]['Kompetenzfacetten'][$facetIndex]['Kompetenzanforderungen'][$requirementIndex];
                return $description;
            } else {
                return "Beschreibung nicht gefunden.";
            }
        } else {
            return "Ungültige compID-Struktur.";
        }
    }

    return "compID enthält nicht 'GRETA'.";
}


function get_course_competencydata($courseids) {
    global $DB;
    $results = [];


    foreach ($courseids as $courseid) {
        // SQL-Abfrage vorbereiten
        $sql = "SELECT competency.idnumber
                FROM {competency_coursecomp} coursecomp
                LEFT JOIN {competency} competency ON coursecomp.competencyid = competency.id
                LEFT JOIN {competency_framework} framework ON competency.competencyframeworkid = framework.id
                WHERE coursecomp.courseid = :courseid AND framework.shortname IN ('ESCO', 'GRETA', 'DigComp')";


        // Die Abfrage durchführen
        $recordset = $DB->get_recordset_sql($sql, array('courseid' => $courseid));

        // Initialisiere ein Array, um die Competency-IDs für diesen Kurs zu speichern
        $competency_ids = [];

        foreach ($recordset as $record) {
            $competency_ids[] = $record->idnumber; // Füge die idnumber der Kompetenz hinzu
        }

        // Schließe den Recordset nach dem Durchlaufen
        $recordset->close();

        // Füge das Ergebnis für diesen Kurs dem results-Array hinzu
        $results[$courseid] = $competency_ids;
    }
    return $results;
}


function convert_moochub_to_amb($results) {
    //require_once(__DIR__ . '/../../config.php');

    global $CFG;
    //$courseAttributes = $results['data'][0]['attributes'];
    $convertedResults = [];
    foreach ($results as $result) {
        $convertedResults[] = [
            'title' => $result->coursetitle, // oder der entsprechende Spaltenname
            'description' => $result->teasertext, // oder der entsprechende Spaltenname
            //'uri' => $result->courseurl, // oder der entsprechende Spaltenname


            'uri' => $CFG->wwwroot . "/course/view.php?id=" . (isset($result->courseid) ? $result->courseid : ''),
            'cost' => isset($result->coursecost) ? (float)$result->coursecost : 0, // Annahme: coursecost ist der Feldname für die Kosten
            'uuid' => $result->uuid,
        ];
        //$jsonData .= $jsonData . json_encode($convertedResults);
    }
    $jsonData = json_encode($convertedResults);
    $jsonDataFinal = str_replace(['[', ']'], '', $jsonData);
    //echo "jsondata = " . $jsonDataFinal;

    return $jsonDataFinal;
}



function get_course_ids() {
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

function get_baseurl() {
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

function get_source_slug() {
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

function get_clientid() {
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

function get_clientsecret() {
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

function get_courseIds() {
    global $DB;
    if ($DB->get_records('config')) {
        $courseIdsobj = $DB->get_record('config', ['name' => 'local_nbpmetadatasend_courseIds']);
        $courseIds = $courseIdsobj->value;
    } else {
        echo ("No connection to database. ");
        die();
    }
    $courseidsArray = [];
    // Umwandeln des Strings in ein Array
    if (strpos($courseIds, ',') !== false) {
        $courseidsArray = explode(",", $courseIds);
    } else {
        $courseidsArray = [$courseIds];
    }

    $final_courseidsArray = [];
    foreach ($courseidsArray as $courseid) {

        // Trim leere Zeichen und prüfe, ob der courseid eine gültige Ganzzahl ist
        $courseid = trim($courseid);

        // Prüfen, ob courseid nur aus Ziffern besteht und positiv ist
        if (!ctype_digit($courseid) || (int)$courseid <= 0) {
            // Wenn ungültig, überspringe diesen Wert
            continue;
        }
        $final_courseidsArray[] = $courseid;
    }
    var_dump($final_courseidsArray); 
    return $final_courseidsArray;
}

/**
 * Funktion, um UUIDs basierend auf einer Liste von Course-IDs aus der Tabelle mdl_ildmeta abzurufen.
 *
 * @param array $courseids Array der Course-IDs, für die die UUIDs abgerufen werden sollen.
 * @return array Array mit den gefundenen UUIDs, wobei die Schlüssel die Course-IDs sind.
 * @throws dml_exception Wenn ein Datenbankfehler auftritt.
 */
function get_uuids_by_courseids(array $courseids): array {
    global $DB; // Zugriff auf die Moodle-Datenbank

    // Prüfen, ob die Eingabe leer ist
    if (empty($courseids)) {
        return [];
    }

    // SQL-Abfrage: UUIDs für die angegebenen Course-IDs abrufen
    list($in_sql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
    $sql = "SELECT courseid, uuid 
            FROM {ildmeta} 
            WHERE courseid $in_sql";

    // Abfrage ausführen
    $results = $DB->get_records_sql($sql, $params);

    // Ergebnisse in ein einfaches Array umwandeln
    $uuids = [];
    foreach ($results as $record) {
        $uuids[$record->courseid] = $record->uuid;
    }
    var_dump( $uuids); 

    return $uuids;
}

/**
 * Funktion, um Kursdaten von einer URL abzurufen und nur die Daten zu filtern,
 * die die gewünschten UUIDs enthalten.
 *
 * @param string $url Die URL, von der die JSON-Daten abgerufen werden.
 * @param array $uuids Die Liste der UUIDs, die berücksichtigt werden sollen.
 * @return array|null Gefilterte Kursdaten oder null bei einem Fehler.
 */
function getFilteredCoursesData(string $url, array $uuids): ?array {
    // cURL-Session initialisieren
    $ch = curl_init();

    // Optionen setzen
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Ausgabe als String zurückgeben

    // Anfrage ausführen und Antwort speichern
    $jsonData = curl_exec($ch);

    // Prüfen, ob ein Fehler aufgetreten ist
    if (curl_errno($ch)) {
        echo "cURL-Fehler: " . curl_error($ch);
        curl_close($ch);
        return null;
    }

    // cURL-Session schließen
    curl_close($ch);

    // JSON in ein PHP-Array umwandeln
    $dataArray = json_decode($jsonData, true);

    // Überprüfen, ob das JSON-Parsing erfolgreich war und die Struktur gültig ist
    if (!is_array($dataArray) || !isset($dataArray['data'])) {
        echo "Fehler beim Dekodieren der JSON-Daten oder ungültige Datenstruktur.";
        return null;
    }

    // Auf die Daten im Schlüssel "data" zugreifen
    $courseData = $dataArray['data'];
    echo "courseData <br/>"; 
    var_dump($courseData); 
    // Gefilterte Daten basierend auf den UUIDs (entsprechen den "id"-Werten)
    $filteredData = array_filter($courseData, function ($item) use ($uuids) {
        return isset($item['id']) && in_array($item['id'], $uuids, true);
    });

    return array_values($filteredData); // Indexe neu sortieren
}


function get_new_token($tokenFile, $clientId, $clientSecret) {

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

function get_nbp_token($tokenFile, $clientId, $clientSecret) {

    if (!file_exists($tokenFile)) {
        echo "no token file";
        get_new_token($tokenFile, $clientId, $clientSecret);
    }

    $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    if (time() >= $tokenInfo["expires"]) {
        get_new_token($tokenFile, $clientId, $clientSecret);
        $tokenInfo = json_decode(file_get_contents($tokenFile), true);
    }

    return $tokenInfo["token"];
}
