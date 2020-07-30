<!DOCTYPE html>
<html lang="en">
<head>
  <title>Print Server</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="apple-touch-icon" sizes="57x57" href="icons/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="icons/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="icons/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="icons/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="icons/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="icons/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="icons/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="icons/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="icons/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="icons/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="icons/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png">


  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <style>
  
  #loading {
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  position: fixed;
  display: block;
  opacity: 0.7;
  background-color: #fff;
  z-index: 99;
  text-align: center;
  display: none;
}

#loading-image {
  position: fixed;
  top: 50%;
  left: 50%;
  margin-top: -50px;
  margin-left: -100px;
  z-index: 100;
  display: none;
}
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color: white;
   color: black;
   text-align: left;
}
  
  </style>
</head>

<body>

<?php 
    require 'config.php';
    require 'controller.php';
?>

<div id="loading">
    <img id="loading-image" src="icons/spinner.svg"/>
</div>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
  <!-- Brand/logo -->
  
        <form class="form-inline my-2 my-lg-0" action="index.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="q" value="upload">
            <input class="form-control mr-sm-2" type="file" name="fileToUpload" id="fileToUpload" class="form-control">  
            <button type="submit" class="btn btn-success my-2 my-sm-0">Upload</button> 
        </form>
    
</nav>

<div class="container-fluid" style="margin-top: 130px;">



<?php
    
    $files = array_diff(scandir(FILES_DIR,SCANDIR_SORT_DESCENDING), array('..', '.'));
    
    $i=1;
    foreach($files as $file){
	$exploded = explode(".", $file);
	$thumb_file = $exploded[0].'.jpeg';
	//$thumb_file = $exploded[0].'.'.$exploded[1];
        ?>
            <div class="row">
                    <div class="col-sm-12 text-center">
                             <img src="thumbnails/<?=$thumb_file?>" style="max-width:300px;" >                   
                     </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-right">
                            <form class="print-form" id="print_form_<?=$i?>" action="index.php" method="post">
                                <input type="hidden" name="filename" value="<?=$file?>">
				<input type="hidden" name="q" value="print">
                                <button id="print-button" data-id="<?=$i?>" type="button" class="btn btn-primary">Print</button> 
                            </form>                             
                        </td>
                        <td class="text-left">
                            <form action="index.php" method="post">
                                <input type="hidden" name="filename" value="<?=$file?>">
                                <button type="submit" name="q" value="delete" class="btn btn-danger">Del</button>             
                            </form>
                        </td>
                    </tr>                
                </table>
           

                </div>
            </div>
            <br>
        <?php
	$i++;
    }
?>
 
<br>
<br>
</div>

<div class="footer">
    <div class="row">
        <div class="col-sm-12 text-center bg-warning">
         <?=PRINTER;?> </b> <i> <span id="printer-status"> </span> </i>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        <div class="progress">
                <div class="progress-bar progress-bar-info" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width:0%">
                <span id="printing-progress"></span>
    </div>
        </div>
    </div>

</div>

</body>
<script>
    $('document').ready(function(){
        $("#loading").show();
        $("#loading-image").show();

        $("form.print-form").on('click', "#print-button", function(e){
            e.preventDefault();
	   var i=$(this).data('id');
            $("#loading").show();
            $("#loading-image").show();
            $("#print-button").prop('disabled', true);
            $("form#print_form_"+i).submit();

        });

        var refresh = setInterval(function(){

            var url = window.location.protocol +'//' +window.location.hostname + '/status.php';
            
            var ajaxRequest;
             
             ajaxRequest= $.ajax({
                 url: url,
                 type: "get"
             });
             
             ajaxRequest.done(function (response, textStatus, jqXHR){
                 var status = response.printer_status;
                 var progress = response.printing_progress;

                 if(status == 'idle'){
                    $("#printer-status").text('(idle)');
                    $(".progress-bar").css('width', '0%');
                    $("#printing-progress").text('')
                    $("#loading").hide();
                    $("#loading-image").hide();     
                    $("#print-button").prop('disabled', false);              
                 }else if(status == 'printing'){
                    $("#printer-status").html('');
                    $(".progress-bar").css('width', progress+'%');
                    $("#printing-progress").text('Printing '+progress+'%')
                    $("#loading").hide();
                    $("#loading-image").hide();
                    $("#print-button").prop('disabled', true);
                 }else{
                    $("#printer-status").text('(wait)');
                    $(".progress-bar").css('width', '0%');
                    $("#printing-progress").text('')
                    $("#loading").show();
                    $("#loading-image").show();
                    $("#print-button").prop('disabled', true);
                 }
 
             });
        },500);

  
    });

</script>

</html>



