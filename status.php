<?php
    if(isset($_GET)){

        $cups_status = shell_exec("lpstat -p -d");

        if (strpos($cups_status, 'Printing page') !== false) {
            $printer_status = 'printing';
            preg_match('/, (?P<digit>\d+)/', $cups_status, $matches);
            $progress = $matches['digit'];   
        }elseif(strpos($cups_status, 'idle')) {
            $printer_status = 'idle';
            $progress = 0;            
        }else{
            $printer_status = 'wait';
            $progress = 0;              
        }

        $data = [ 
                    'printer_status'    => $printer_status, 
                    'printing_progress' => $progress
                ];

            header("Pragma", "no-cache, no-store");
            header("Cache-Control", "no-cache");
            header("Expires", 0);
            header('Content-type: application/json');
            echo json_encode( $data );
    }
    
    
?>