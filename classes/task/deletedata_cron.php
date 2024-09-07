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
class deletedata_cron extends \core\task\scheduled_task
{

    public function get_name()
    {
        return get_string('delete_data_cron', 'local_nbpmetadatasend');
    }



    public function execute()
    {
        start_delete_process();
    }
}


function start_delete_process()
{
    echo "start_deletedata_process";
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
    //$courseIds = ["a5d2ce26-8783-4d6d-880c-ee291d5c24c3"];



    $tokenfile = "token_info.json";

    $token = get_nbp_token($tokenfile, $clientid, $clientsecret);
    $results = get_course_metadata($courseIds);
    foreach ($results as $result) {
        $courseid = isset($result->uuid);
        $url = $baseurl . '/api/course/' . $sourceslug . '/' . $courseid;

        $ch = curl_init();

        // Für den DELETE-Request entfernen wir die Zeilen, die den Body betreffen, da dieser typischerweise nicht benötigt wird
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Ändere den Request-Typ zu DELETE
        // Entferne CURLOPT_POSTFIELDS, da wir keinen Body für DELETE senden

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "accept: text/plain",
                "Authorization: Bearer " . $token, // Nutze das Bearer-Token für die Autorisierung
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
