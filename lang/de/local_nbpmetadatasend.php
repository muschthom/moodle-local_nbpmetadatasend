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
 * Strings for component 'local_importpossehluser', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    local
 * @subpackage nbpmetadatasend
 * @copyright   2023 ILD TH Lübeck <dev.ild@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'NBP Metadaten Management';
$string['managepage'] = 'Manage NBP Metadata';
$string['managepageheading'] = 'NBP Metadata Management';
$string['inputlabel'] = '<ul> <li>Kurs IDs der Moodle Kurse in das Eingabefeld eintragen, die an den Datenraum geschickt werden sollen</li>
                        <li> Kurs-ID ist der URL zu entnehmen, bspw. course/view.php?id=<b>1</b> </li>
                        <li> IDs durch Kommas getrennt eintragen, z.B. 1,2,3</li> </ul>';
$string['inputlabel_deletedata'] = '<ul> <li>Kurs IDs der Moodle Kurse in das Eingabefeld eintragen, die aus dem Datenraum gelöscht werden sollen</li>
                                    <li> Kurs-ID ist der URL zu entnehmen, bspw. course/view.php?id=<b>1</b> </li>
                                    <li> IDs durch Kommas getrennt eintragen, z.B. 1,2,3</li> </ul>';
$string['submitbuttongetdata'] = 'Prozess Datenabruf starten';
$string['submitbuttonputdata'] = 'Prozess Dateneingabe starten';
$string['submitbuttondeletedata'] = 'Prozess Datenlöschung starten';
$string['datasaved'] = 'Prozess erfolgreich ausgeführt';
$string['inputrequired'] = 'Bitte Kurs-IDs eingeben';
$string['getdataprocessdesc'] = 'Bei Klick auf den Button werden alle Kurse abgerufen, die im Datenraum für die slug (siehe nbpmetadata settings Feld) eingetragen sind.';
$string['getdataprocessheading'] = 'Metadaten-Datenabruf vom Datenraum';
$string['putdataprocessheading'] = 'Metadaten-Dateneingabe in den Datenraum';
$string['putdataprocessdesc'] = 'Senden von Metadaten für spezifische Kurse in den Datenraum.';
$string['deletedataprocessheading'] = 'Metadaten-Datenlöschung aus dem Datenraum';
$string['deletedataprocessdesc'] = 'Löschen von Metadaten für spezifische Kurse aus dem Datenraum.';

$string['settingspagetitle'] = 'NBP Meta Data Send';
$string['baseurl'] = 'Basis-URL';
$string['baseurldesc'] = 'Basisadresse für alle Anfragen an den Datenraum';
$string['sourceslug'] = 'Source Slug';
$string['sourceslugdesc'] = 'Menschenlesbarer Identifikator der Datenquelle, für welche Lernangebote bearbeitet werden sollen';
$string['clientid'] = 'Client ID';
$string['clientiddesc'] = 'Client Id für die Verbindung zum Datenraum';
$string['clientsecret'] = 'Client Passwort';
$string['clientsecretdesc'] = 'Passwort für die Verbindung zum Datenraum';

