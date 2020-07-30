<?php

    if($_POST){

        if($_POST["q"] == "upload"){

        $file_type = $_FILES['fileToUpload']['type'];

        if (!in_array($file_type, $allowed_types)) {
            die('Unsupported file type! Allowed: image, pdf, doc, docx, xls, xlsx, ppt, pptx');
        }
	
       $filename = strtotime("now");

        
        $extension = strtolower(preg_replace('/\W/', '', pathinfo($_FILES["fileToUpload"]["name"])['extension']));
        $target_file = FILES_DIR  . $filename . "." . $extension;

        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif' ) {
            $orifile    = $target_file;

        } else {
            $orifile    = $target_file.'[0]';
        }

        $thumbfile  = THUMBNAILS_DIR . $filename . '.jpeg';
        shell_exec("convert $orifile $thumbfile");

        }elseif($_POST["q"] == "print"){

            $cups_status = shell_exec("lpstat -p -d");

            if (strpos($cups_status, 'Printing page') !== false) {
                die('Printer is busy..');
            }

            $cur_dir = getcwd();
            $digits = preg_replace("/[^0-9]/", "",$_POST["filename"] ).".";
            $files = glob(FILES_DIR.$digits.'*');
            $filepath = $files[0];
	    $filename = end(explode('/',$filepath));
	    $ext = end(explode('.', $filename));

            if($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'pdf' ){
                shell_exec("lp -d ". PRINTER ." -o media=A4 $filepath");
            }else{
                shell_exec("libreoffice --headless --pt " .PRINTER. " $filepath");
            }

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
