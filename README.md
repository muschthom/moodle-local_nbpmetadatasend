# moodle-local_nbpmetadatasend
 Local plugin to send selected course meta data defined by Project Trainspot to Mein Bildungsraum Datenraum /Trainspot Datenraum. 
 


 In the plugin settings /admin/settings.php?section=local_nbpmetadatasend you find input fields for: 
 * baseurl (Datenraum)
 * Source Slug (your institution)
 * Client Id (provided by Datenraum)
 * Client Secret (provided by Datenraum)


# Installation

The plugin needs to be installed inside the "local" folder and needs to be named ildmeta.

```bash
git clone https://github.com/muschthom/moodle-local_nbpmetadatasend.git nbpmetadatasend
```


# Manage Metadata
The link to this page is /local/nbpmetadatasend/manage.php 

or use the link at Plugins/Local Plugins/Manage NBP Metadata. You will find the following option: 
* Metadata data retrieval from the Datenraum = get all metadata that is available at the source slug defined in the settings
* Metadata data entry in the Datenraum = add new metadata for another course or update existing metadata by entering the course id and press the button "Start data entry process"
* Metadata deletion from the Datenraum = delete metadata from the Datenraum by entering the course id of the course you want to delete metadata from and press the button "Start data deletion process"



 ## Information about the plugin
 * in folder /ressources/GRETA you find 
   * a description of the GRETA competency framework (json) 
   * a competency framework file (.csv) to be imported into Moodle at /admin/tool/lpimportcsv/index.php
   * a json file (greta-shortcodes.json) that is required to extract GRETA-related data to be inserted into the metadata
 * in folder /ressources/trainspot you find 
   * a description of Trainspot-required meta data to be send to the Datenraum (.yaml) 
   * an example dataset (2025-09-06-digitaltrainer1.json)
 * at  /local/nbpmetadatasend/get_trainspot_courses.php you can see the complete meta data set to be send to Datenraum


 ## About Trainspot 
 see https://wb-web.de/trainspot.html 
