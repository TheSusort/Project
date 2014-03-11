<!DOCTYPE html>
    <html>
    <head>
        <title>fullscreen</title>
        <meta http-equiv="content-type" content="=text/html; charset=utf-8 without BOM"/>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="img/favicon.ico">
        
        <script type="text/javascript">
            function exchange(el){
                var ie=document.all&&!document.getElementById? document.all : 0;
                var toObjId=/b$/.test(el.id)? el.id.replace(/b$/,'') : el.id+'b';
                var toObj=ie? ie[toObjId] : document.getElementById(toObjId);
                if(/b$/.test(el.id))
                    toObj.innerHTML=el.value;
                else{
                    toObj.style.width=el.offsetWidth+7+'px';
                    toObj.value=el.innerHTML;
                }
                el.style.display='none';
                toObj.style.display='inline';
            }
			
        </script>
        
    </head>
    <!-------------------------------------------------------------------------------------->
    <body>

    
    <?php
    include_once("mysql.php");
    include_once("funksjoner.php");
	include_once("fullscreen.php");
    db_connnect();
	
	// alert_message("POST allert: ".print_r($_POST));
	
	$currentImage = substr($_GET['bilde'],7);
	
	$query1 = "SELECT fileid FROM file_liste WHERE filename='$currentImage'";
					$result1 = $db->query($query1);
         	
					if (!empty($result1)){
						foreach($result1 as $rr)
						{
							$result3 = (array_to_string($rr));
						}	
					}
    ?>
        
       <div id="containermain">
           
           <div id="leftcontainer">
                      
			   <div id="details">
					<h3>Picture details</h3>
					<b>Current file: </b>
					<?php print_r($currentImage);?><br>
					<b>Tags:</b>
					
					<?php 	
					
					$query2 = "SELECT tags FROM tag WHERE fileid=$result3";
					$result2 = $db->query($query2);
					if (!empty($result2)){
						foreach($result2 as $rr)
						{
							print "#";
							print_r(array_to_string($rr));
							print " ";
						}	
					}
					
					?><br>
					<b>Comment:</b>
					
					<?php 	
					
					$query2 = "SELECT commentary FROM file_liste WHERE fileid=$result3";
					$result2 = $db->query($query2);
					if (!empty($result2)){
						foreach($result2 as $rr)
						{
							print_r(array_to_string($rr));
							print " ";
						}	
					}
					
					?>
					 
					
					<br>
					<b>Rating:</b>
					<span id='ratingStr'>
					<?php 	
						$query2 = "SELECT rating FROM file_liste WHERE fileid=$result3";
						$result2 = $db->query($query2);
						if (!empty($result2)){
							foreach($result2 as $rr)
							{
								$currentRating = array_to_string($rr);
								print_r(array_to_string($rr));
								print " ";
							}	
						}
					?>
					</span>
					<?php
					//	$rating = 'Not rated';
					//	if(isset($_POST['ratinginput'])){
					//		if(!empty($_POST['ratinginput'])){
					//			$rating = $_POST['ratinginput'];
					//		}
					//	}
					//	print_r($rating);
					?>			
					<br>
			   </div>
               
                <!containerwithcomments,rating>
               <div id="buttoncontainer">
                   
                   <!ratingknapper>
               
				<?php
				
                $rating1=NULL;
                $rating2=NULL;
                $rating3=NULL;
                $rating4=NULL;
                $rating5=NULL;
                if (strcmp($currentRating,"1") == 0) $rating1="checked";
                if (strcmp($currentRating,"2") == 0) $rating2="checked";
                if (strcmp($currentRating,"3") == 0) $rating3="checked";
                if (strcmp($currentRating,"4") == 0) $rating4="checked";
                if (strcmp($currentRating,"5") == 0) $rating5="checked";
				echo '<form id="ratingForm" method="post">	
					<span class="rating"> 
						<input type="radio" value="5" class="rating-input"
							id="rating-input-1-5" name="ratinginput" '.$rating5.' onChange="rateFunction(5)">
						<label for="rating-input-1-5" class="rating-star"></label>
						
						<input type="radio" value="4" class="rating-input"
							id="rating-input-1-4" name="ratinginput" '.$rating4.' onChange="rateFunction(4)">
						<label for="rating-input-1-4" class="rating-star"></label>
						
						<input type="radio" value="3" class="rating-input"
							id="rating-input-1-3" name="ratinginput" '.$rating3.' onChange="rateFunction(3)">
						<label for="rating-input-1-3" class="rating-star"></label>
						
						<input type="radio" value="2" class="rating-input"
							id="rating-input-1-2" name="ratinginput" '.$rating2.' onChange="rateFunction(2)">
						<label for="rating-input-1-2" class="rating-star"></label>
						
						<input type="radio" value="1" class="rating-input"
							id="rating-input-1-1" name="ratinginput" '.$rating1.' onChange="rateFunction(1)">
						<label for="rating-input-1-1" class="rating-star"></label>
					</span>';
				?>
				
				</form>
				<script>
					function getXmlHttp(){
						var xmlhttp;
						try {
							xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
						} catch (e) {
							try {
								xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
							} catch (E) {
								xmlhttp = false;
							}
						}
						if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
							xmlhttp = new XMLHttpRequest();
						}
						return xmlhttp;
					}
					
					function parseGetParams() { 
					   var $_GET = {}; 
					   var __GET = window.location.search.substring(1).split("&"); 
					   for(var i=0; i<__GET.length; i++) { 
						  var getVar = __GET[i].split("="); 
						  $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
					   } 
					   return $_GET; 
					} 
					
					function rateFunction(rate){
						var xmlhttp = getXmlHttp();
						var getArr = parseGetParams();
						var fileName = getArr['bilde'];
						
						xmlhttp.open('POST', 'rating.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("rate=" + encodeURIComponent(rate) + "&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText==""){
								var span = document.getElementById('ratingStr');
								span.innerHTML = rate;
							};
						}
					}
							
				</script>
				<?php
					if(!empty($_POST['ratinginput'])){
						$svaret = $_POST['ratinginput'];
						$query = "UPDATE file_liste SET rating='$svaret' WHERE fileid=$result3";
						$result = $db->query($query);
						echo'<meta http-equiv="refresh" content="0" />';
					}
					
				?>				
				
				
                   
                   <!kommentarfelt>
        
                <form action="" method="post">
                    <span id="itm1" onclick="exchange(this);"><?php 	
					$query2 = "SELECT commentary FROM file_liste WHERE fileid=$result3";
					$result2 = $db->query($query2);
                	foreach($result2 as $rr)
						
					if (!empty($result2)){
						foreach($result2 as $rr) {
							print_r(array_to_string($rr));
							if(null === (array_to_string($rr))) echo("click to comment");
						}
                    }
?>
</span>
                    <input ondblclick="exchange(this);" id="itm1b" class="replace" type="text" value=""  name="comment">
                    </form><?php
					if(!empty($_POST['comment'])){
						$svaret = $_POST['comment'];
						$query = "UPDATE file_liste SET commentary='$svaret' WHERE fileid=$result3";
						$result = $db->query($query);
						echo'<meta http-equiv="refresh" content="0" />';
					}?>
					
					
               
           
                 
                   
                <!tagfelt>
                   
                   <?php
                   
                   echo '<form action="" method="post">
                       <input type="text" name="tag" />
                       <input type="submit" value="Tag!" />
                    </form>'
                    ;
  
				
					
			
					if(!empty($_POST['tag'])){
						$svaret = $_POST['tag'];
                        $stringarray = explode(" ", $svaret);
                        foreach ($stringarray as $str)
                        {
                            $query = "INSERT INTO tag(fileid, tags) VALUES ($result3 , '$str')";
                            $result = $db->query($query);
                            echo'<meta http-equiv="refresh" content="0" />';
                        }
					}
			
					?>  
                        
                    <p>
                   
				 <!nullstill>

				 <?php
				 
					echo '<form action="" method="post">
                       <input type="submit" name="reset" value="Reset!" />
                    </form>'
                    ;
			
					if ( isset( $_POST['reset'] ) ) { 
							$query = "UPDATE file_liste SET commentary = NULL WHERE fileid=$result3;";
							$result = $db->query($query);
							
							$query = "UPDATE file_liste SET rating = NULL WHERE fileid=$result3;";
							$result = $db->query($query);
							
							$query = "DELETE FROM tag WHERE fileid=$result3;";
							$result = $db->query($query);
							
							echo'<meta http-equiv="refresh" content="0" />';
					}

					
					?>
				  
               <p>

            </div>
           
           </div>

    <FORM action="CloseFullscreen">
                   <input type="image" src="closex.png" onClick="window.close('fs')" align="right" width="40" height="40">
           </FORM>           
           
           <div id="fullscreenpic">
          <?php
                    if(($_GET['tag'] === "null")) {
                        $files = get_img_list($big);
                    }else {
                        $files = get_img_by_tag($_GET['tag']);
                    }
                    
                    
                    $number = count($files);
                    $key = array_search(basename($_GET['bilde']), $files);

                    
                    if (isset($_GET['previous'])){
                        if ($key == 0) $key = $number;
                        $showFile = $files[$key - 1];
                    }
                    else if (isset($_GET['next'])){
                        if($key == $number-1) $key = -1;
                        $showFile = $files[$key + 1];
                    }
                    else { $showFile = substr($_GET['bilde'], 7);
                    echo '<img src='.$big.$showFile.' height=85% ><br/>'; 
                         }
                    if ((isset($_GET['previous'])) or (isset($_GET['next']))) {
                        echo '<img src='.$_GET['bilde'].' height=85% ><br/>'; 

                    }
                    echo '<a href="?previous=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    <img src= "Lbutton.png"width="40" height="40"></a>';
                    echo '<a href="?next=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    <img src= "Rbutton.png"width="40" height="40"></a>';
               
                ?>
 
    
               
               <script type="text/javascript">
                    window.onload=function() {
                        document.onkeyup = key_event;
                    }
   
                    function key_event(e) {
                        if (e.keyCode == 27) doStuff();
                    }
   
                    function doStuff() {
                        window.close('fs'); 
                    }
               </script>
</div>
        
    </body>
    </html>
