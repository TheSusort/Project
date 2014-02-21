<!DOCTYPE html>
    <html>
    <head>
        <title>fullscreen</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <!-------------------------------------------------------------------------------------->
    <body>

    
    <?php
    include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
	include_once("fullscreen.php");
    db_connnect();
	
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
           
           <!blått felt>
           <div id="title">
                    <h1>Fullscreenview</h1>
                   </div>
           
           
           <!rødt felt>
           <div id="fullscreenpic">
               <?php
                    //print_r($_GET);
                    echo("<img src=".$_GET['bilde']." height=500px >");
               
                ?>
            
           
           </div>

          
           <form action="NextPrevButton">
				<input type="image" src="Lbutton.png" alt="Submit" width="40" height="40">
				<input type="image" src="Rbutton.png" alt="Submit" width="40" height="40">
			</form>               
			   
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
			   

       
           <!grønt felt>
          
           
           
           <div id="buttons">
               
               <!gult felt>
               
               
               <div id="toprow">
                   
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
				echo '<form action="" method="post">	
					<span class="rating"> 
					<input type="radio" value="5" class="rating-input"
						id="rating-input-1-5" name="ratinginput" '.$rating5.' >
					<label for="rating-input-1-5" class="rating-star"></label>
					
					<input type="radio" value="4" class="rating-input"
						id="rating-input-1-4" name="ratinginput" '.$rating4.' >
					<label for="rating-input-1-4" class="rating-star"></label>
					
					<input type="radio" value="3" class="rating-input"
						id="rating-input-1-3" name="ratinginput" '.$rating3.' >
					<label for="rating-input-1-3" class="rating-star"></label>
					
					<input type="radio" value="2" class="rating-input"
						id="rating-input-1-2" name="ratinginput" '.$rating2.' >
					<label for="rating-input-1-2" class="rating-star"></label>
					
					<input type="radio" value="1" class="rating-input"
						id="rating-input-1-1" name="ratinginput" '.$rating1.' >
					<label for="rating-input-1-1" class="rating-star"></label>
				</span>';
				?>
					
				</span>
					<input type='hidden' name='ratingid' id='rateingid' value='<?php echo $_GET["id"]; ?>' />
					<input type="submit" name="submit" value="Rate!"></p>
					
				</form>
				
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
                       <input type="text" name="comment" size="50"/>
                       <input type="submit" value="Comment!" />
                    </form>
			
					<?php
					if(!empty($_POST['comment'])){
						$svaret = $_POST['comment'];
						$query = "UPDATE file_liste SET commentary='$svaret' WHERE fileid=$result3";
						$result = $db->query($query);
						echo'<meta http-equiv="refresh" content="0" />';
					
					}
					?>
					
					
               </div>
                      
           
           
           
           
           
               <!brunt felt>
               <div id="bottomrow">    
                <!tagfelt>
                   
                   <?php
                   
                   echo '<form action="" method="post">
                       <input type="text" name="tag" />
                       <input type="submit" value="Tag!" />
                    </form>'
                    ;
  
				
					
			
					if(!empty($_POST['tag'])){
						$svaret = $_POST['tag'];
						$query = "INSERT INTO tag(fileid, tags) VALUES ($result3 , '$svaret')";
						$result = $db->query($query);
						echo'<meta http-equiv="refresh" content="0" />';
					}
			
					?>  
                   
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
				  
               </div>
               
            
           </div>
           </div>
    
        
    </body>
    </html>