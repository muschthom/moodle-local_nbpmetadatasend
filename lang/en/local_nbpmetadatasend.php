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
$string['pluginname'] = 'NBP Metadata Management';
$string['managepage'] = 'Manage NBP Metadata';
$string['managepageheading'] = 'NBP Metadata Management';
$string['inputlabel'] = '<ul> <li> Enter the IDs of the Moodle courses that are to be sent to the data room in the input field</li>
                        <li> The course ID can be found in the URL, e.g., course/view.php?id=<b>1</b> </li>
                                    <li> Enter the IDs separated by commas, e.g., 1,2,3</li> </ul>';
$string['inputlabel_deletedata'] = '<ul> <li>Enter the course IDs of the Moodle courses that are to be deleted from the data room in the input field.</li>
                                    <li> The course ID can be found in the URL, e.g., course/view.php?id=<b>1</b> </li>
                                    <li> Enter the IDs separated by commas, e.g., 1,2,3</li> </ul>';
$string['submitbuttongetdata'] = 'Start data retrieval process';
$string['submitbuttonputdata'] = 'Start data entry process';
$string['submitbuttondeletedata'] = 'Start data deletion process';
$string['datasaved'] = 'Process successfully executed';
$string['inputrequired'] = 'Please enter course IDs';
$string['getdataprocessdesc'] = 'Clicking on the button retrieves all courses that are entered in the data room for the slug (see nbpmetadata settings field).';
$string['getdataprocessheading'] = 'Metadata data retrieval from the Datenraum';
$string['putdataprocessheading'] = 'Metadata data entry in the  Datenraum';
$string['putdataprocessdesc'] = 'Sending metadata for specific courses to the data room.';
$string['deletedataprocessheading'] = 'Metadata deletion from the data room';
$string['deletedataprocessdesc'] = 'Deleting metadata for specific courses from the data room.';
$string['settingspagetitle'] = 'NBP Meta Data Send';

$string['baseurl'] = 'Base URL';
$string['baseurldesc'] = 'Base address for all requests to the Datenraum';
$string['sourceslug'] = 'Source Slug';
$string['sourceslugdesc'] = 'Human-readable identifier of the data source for which learning opportunities are to be processed';
$string['clientid'] = 'Client ID';
$string['clientiddesc'] = 'Client ID for connecting to the Datenraum';
$string['clientsecret'] = 'Client password';
$string['clientsecretdesc'] = 'Password for connecting to the Datenraum';

