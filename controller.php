<?php

    if($_POST){

        if($_POST["q"] == "upload"){

            $allowed_types = array ( 'application/pdf', 'image/jpeg', 'image/png', 'image/gif' );
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_type = finfo_file( $fileInfo, $_FILES["fileToUpload"]["tmp_name"] );

            if ( !in_array($detected_type, $allowed_types) ) {
                die ( 'Please upload a pdf or an image ' );
            }
            finfo_close( $fileInfo );


            
            $filename = strtotime("now");
            switch($detected_type){
                case 'application/pdf': 
                    $extension = 'pdf';
                    break;
                case 'image/jpeg': 
                    $extension ='jpeg';
                    break;
                case 'image/png': 
                    $extension ='png';
                    break;
                case 'image/gif': 
                    $extension = 'gif';
                break;
            }

            $target_file = FILES_DIR  . $filename . "." . $extension;

            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
            
            if($extension == 'pdf'){
                $orifile    = $target_file.'[0]';
            }else{
                $orifile    = $target_file;
            }
            $thumbfile  = THUMBNAILS_DIR . $filename . '.jpeg';



            $a = shell_exec("convert $orifile $thumbfile");


        }elseif($_POST["q"] == "print"){

            $cups_status = shell_exec("lpstat -p -d");

            if (strpos($cups_status, 'Printing page') !== false) {
                die('Printer is busy..');
            }

            $cur_dir = getcwd();
            $digits = preg_replace("/[^0-9]/", "",$_POST["filename"] ).".";
            $files = glob(FILES_DIR.$digits.'*');
            $filepath = $files[0];

            $a = shell_exec("lp -d ". PRINTER ." -o media=A4 $filepath");

        }elseif($_POST["q"] == 'delete'){

            $cur_dir = getcwd();
            $digits = preg_replace("/[^0-9]/", "",$_POST["filename"] ).".";
            $files = glob(FILES_DIR.$digits.'*');
            $filepath = $files[0];

            unlink($filepath);

            $thumbfile= THUMBNAILS_DIR.$digits.'jpeg';
            unlink($thumbfile);
        }

    }
?>
