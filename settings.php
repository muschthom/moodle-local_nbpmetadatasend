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
 * Link to CSV user upload
 *
 * @package    local
 * @subpackage nbpmetadatasend
 * @copyright   2023 ILD TH Lübeck <dev.ild@th-luebeck.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Ensure the configurations for this site are set
if ($hassiteconfig) {

	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$settings = new admin_settingpage('local_nbpmetadatasend', get_string('settingspagetitle', 'local_nbpmetadatasend'));

	// Create 
	$ADMIN->add('localplugins', $settings);

	// Add external management page
	$ADMIN->add('localplugins', new admin_externalpage(
		'local_nbpmetadatasend_manage',
		get_string('managepage', 'local_nbpmetadatasend'),
		new moodle_url('/local/nbpmetadatasend/manage.php')
	));

	// Add a setting field to the settings for this page
	$settings->add(new admin_setting_configtext(

		// This is the reference you will use to your configuration
		'local_nbpmetadatasend_baseurl',

		// This is the friendly title for the config, which will be displayed
		get_string('baseurl', 'local_nbpmetadatasend'),

		// This is helper text for this config field
		get_string('baseurldesc', 'local_nbpmetadatasend'),

		// This is the default value
		'',

		// This is the type of Parameter this config is
		PARAM_TEXT

	));

	// Add a setting field to the settings for this page
	$settings->add(new admin_setting_configtext(

		// This is the reference you will use to your configuration
		'local_nbpmetadatasend_sourceSlug',

		// This is the friendly title for the config, which will be displayed
		get_string('sourceslug', 'local_nbpmetadatasend'),

		// This is helper text for this config field
		get_string('sourceslugdesc', 'local_nbpmetadatasend'),

		// This is the default value
		'',

		// This is the type of Parameter this config is
		PARAM_TEXT

	));

	// Add a setting field to the settings for this page
	$settings->add(new admin_setting_configtext(

		// This is the reference you will use to your configuration
		'local_nbpmetadatasend_clientId',

		// This is the friendly title for the config, which will be displayed
		get_string('clientid', 'local_nbpmetadatasend'),

		// This is helper text for this config field
		get_string('clientiddesc', 'local_nbpmetadatasend'),

		// This is the default value
		'',

		// This is the type of Parameter this config is
		PARAM_TEXT

	));

	// Add a setting field to the settings for this page
	$settings->add(new admin_setting_configpasswordunmask(

		// This is the reference you will use to your configuration
		'local_nbpmetadatasend_clientSecret',

		// This is the friendly title for the config, which will be displayed
		get_string('clientsecret', 'local_nbpmetadatasend'),

		// This is helper text for this config field
		get_string('clientsecretdesc', 'local_nbpmetadatasend'),

		// This is the default value
		'',

		// This is the type of Parameter this config is
		PARAM_TEXT

	));

	// Add a link to the custom page
	$url = new moodle_url('/local/nbpmetadatasend/manage.php');
	$link = html_writer::link($url, get_string('managepage', 'local_nbpmetadatasend'));
	$settings->add(new admin_setting_heading(
		'local_nbpmetadatasend_managepage',
		'',
		$link
	));
}
