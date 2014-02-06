<!DOCTYPE html>
<html>
<head>
	<title>Gruppe F's fantastiske løsning til å laste opp filer</title>
	<link rel="stylesheet" href="style.css">
</head>
<!-------------------------------------------------------------------------------------->
<body>
	<?php
		include_once("Metadata.php");
		include_once("Bildeviser.php");
	?>
    
    <div id="containermain">
        
        <div id="title">
				<h1>Gruppe F's fantastiske løsning til å laste opp filer</h1>
        </div>
        
        <div id="containerleft">
        
        <div id="upload">
				<form action="Opplasting.php" method="post" enctype="multipart/form-data">
					<input name="bildefil[]" id="bildefil" type="file" multiple=""><br>
					<input type="submit" name="submit" value="Submit">
				</form>
        </div>
        
        <div id="directory">
				<p>This is the directory script</p>
        </div>
            
        <div id="exif">
				<p>This is the EXIF script</p>
        </div>
            
        </div>
            
        <div id="gallery">
				<?php					
					LastInnMetadata();
					VisBilder();
				?> 
        </div>
		
        <div id="terriblemusic">
	               <embed height="50" width="100" src="Kalimba.mp3" style= visibilty: hidden>
        </div>
        
    </div>
</body>
</html>