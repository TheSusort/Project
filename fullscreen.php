<!DOCTYPE html>
<html>
<head>
	<title>fullscreen</title>
	<link rel="stylesheet" href="style.css">
</head>
<!-------------------------------------------------------------------------------------->
<body>
    
   <div id="containermain">
       <div id="title">
				<h1>Fullscreenview</h1>
               </div>
       
       <div id="fullscreenpic">
           <?php
                //print_r($_GET);
                echo("<img src=".$_GET['bilde']." height=500px >");
            ?>
        
       </div>
       <div id="buttons">
       <p>
           Buttons here</p>
       </div>
       </div>

    
</body>
</html>