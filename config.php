<?php
/*
* Use the lpstat command to see a list of available printers:
* `lpstat -p -d`
*/

define("PRINTER", 'EPSON_L120_Series');
define("FILES_DIR", getcwd().'/files/');
define("THUMBNAILS_DIR", getcwd().'/thumbnails/');

$allowed_types = [
    'application/pdf', 
    'image/jpeg', 
    'image/png', 
    'image/gif',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/msword',
    'application/doc',
    'application/ms-doc',    
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/x-excel',
    'application/x-msexcel',    
    'application/vnd.ms-excel',
    'application/excel',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
    'application/x-mspowerpoint',    
    'application/vnd.ms-powerpoint',
    'application/mspowerpoint',
    'application/powerpoint'
];

?>
