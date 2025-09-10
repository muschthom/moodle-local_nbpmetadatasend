# moodle-local_nbpmetadatasend
 Local plugin to send selected course meta data defined by Project Trainspot to Mein Bildungsraum Datenraum /Trainspot Datenraum. 
 


 In the plugin settings /admin/settings.php?section=local_nbpmetadatasend you find input fields for: 
 * baseurl (Datenraum)
 * Source Slug (your institution)
 * Client Id (provided by Datenraum)
 * Client Secret (provided by Datenraum)
 * Course Ids (Ids of the courses that metadata will be send to Datenraum)



 Data will be send by cron job, so configure at /admin/tool/task/scheduledtasks.php and search for \local_nbpmetadatasend\task\put_moochub_cron


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