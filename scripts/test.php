<?php


echo 'testing';

/*$file = portal_generate_resized_image('/tmp/castle.jpg', 150,150);

rename($file, '/tmp/castle-thumb.jpg');

echo $file;

$file = portal_generate_resized_image('/tmp/school_image_file_1.jpg', 150,150);

rename($file, '/tmp/school_image_file_1-thumb.jpg');

echo $file;

echo '<hr>';
*/

$diy_id = portal_get_diy_member_id('Paul W','Burney','paul+itsitest2@burney.ws','paul+itsitest2@burney.ws','test');

mystery_print_r($diy_id);


/*
echo portal_get_unique_username('Paul', 'Burney');

portal_debug_query();

*/

?>