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
class getdata_cron extends \core\task\scheduled_task
{

    public function get_name()
    {
        return get_string('get_data_cron', 'local_nbpmetadatasend');
    }



    public function execute()
    {
        start_getdata_process();
    }
}


function start_getdata_process()
{
    echo "start_getdata_process";
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

    $metadata = get_course_metadata($courseIds);
    //var_dump($metadata);

    $tokenfile = "token_info.json";

    $token = get_nbp_token($tokenfile, $clientid, $clientsecret);
    $results = get_course_metadata($courseIds);
    foreach ($results as $result) {
        $courseid = isset($result->uuid);
        $url = $baseurl . '/api/course/' . $sourceslug . '/' . $courseid;
        $curl = curl_init($url);

        // cURL-Optionen festlegen
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Antwort als String zurückgeben
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Redirects folgen
        curl_setopt($curl, CURLOPT_HTTPGET, true); // HTTP GET verwenden

        // Header festlegen, einschließlich des Authorization-Headers
        $headers = [
            'accept: application/json',
            'Authorization: Bearer ' . $token,
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Die Anfrage ausführen
        $response = curl_exec($curl);
        $err = curl_error($curl);

        // Status-Code ermitteln
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // cURL-Session schließen
        curl_close($curl);

        // Auf Fehler überprüfen und Antwort verarbeiten
        if ($err) {
            echo "cURL Error #: " . $err;
        } else {
            echo "HTTP Status Code: " . $http_status . "\n";
            echo "Response: " . $response;
        }
    }
}
