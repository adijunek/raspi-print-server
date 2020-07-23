<!DOCTYPE html>
<html lang="en">
<head>
  <title>Print Server</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
  <link rel="icon" href="icon/favicon-16x16.png" sizes="16x16">
  <link rel="icon" href="icon/favicon-32x32.png" sizes="32x32">
  <link rel="shortcut icon" href="icon/android-chrome-192x192.png" sizes="192x192">
  <link rel="shortcut icon" href="icon/android-chrome-512x512.png" sizes="512x512">
  <link rel="apple-touch-icon" href="icon/apple-touch-icon.png">

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>
<?php 
    if($_POST){

        if($_POST["mode"] == "upload"){
            $target_dir = "files/";

            $temp = explode(".", $_FILES["fileToUpload"]["name"]); 	
            $extension = end($temp);

            $filename = strtotime("now");

            $target_file = $target_dir . $filename . "." . $extension;

            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

        }elseif($_POST["mode"] == "operate"){

            $filename = $_POST["filename"];
            

            if($_POST["q"] === 'print-a4'){
                $filepath = '/var/www/html/files/'.$filename;
               	$a = shell_exec("lp -d EPSON_L120_Series -o media=A4 $filepath");
               	echo $a;

            }elseif($_POST["q"] === 'print-postcard'){
		$filepath = '/var/www/html/files/'.$filename;
               	$a = shell_exec("lp -d EPSON_L120_Series -o media=Postcard $filepath");
		echo $a;
            }elseif($_POST["q"] === 'delete'){
                unlink("files/".$filename);
    
            }
     
        }

    }

?>

<div class="container-fluid">

    <div class="row ">
        <div class="col-sm-12 text-center">
            <h1>Print Server</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <form action="index.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="upload">
                <div class="form-group"  >
                    <label>Upload File</label>
                    <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">  
                </div>
                <div class="form-group">
                    
                    <button type="submit" class="btn btn-success">Upload</button> 

                </div>
                </form>
           
        </div>

    </div>
<hr>

<?php
    $dir = 'files/';
    $files = array_diff(scandir($dir,SCANDIR_SORT_DESCENDING), array('..', '.'));
    
    
    foreach($files as $file){
        ?>
            <div class="row">
                    <div class="col-sm-12 text-center">
                        <img src="files/<?=$file?>" style="width:50%;">                   
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-center">
                        <form action="index.php" method="post">
                            <input type="hidden" name="mode" value="operate">
                            <input type="hidden" name="filename" value="<?=$file?>">
                            <button type="submit" name="q" value="print-a4" class="btn btn-primary">Print A4</button> 
                            <button type="submit" name="q" value="print-postcard" class="btn btn-primary">Print PostCard</button> 
                            <button type="submit" name="q" value="delete" class="btn btn-danger">Delete</button> 
                        </form>
                    </div>

                </div>
                <br>
        <?php
    }
?>
 
<br>
<br>

</body>
</html>



