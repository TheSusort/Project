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
    db_connnect();
    ?>
        
       <div id="containermain">
           
           <!blått felt>
           <div id="title">
                    <h1>Fullscreenview</h1>
                   </div>
               
                     
            <FORM action="CloseFullscreen">>
                   <input type="image" src="closex.png" onClick="window.close('fs')" align="right" width="40" height="40">
</FORM>
           
         <!rødt felt>
           <div id="fullscreenpic">
               <?php

                    if(($_GET['tag'] === "null") or ($_GET['tag'] === null)) {
                        $files = get_img_list($big);
                        echo 'bilder fra mappe';
                    }else {
                        $files = get_img_by_tag($_GET['tag']);
                        echo 'bilder fra tag';
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
                    else $showFile = substr($_GET['bilde'], 7);
                  
                    echo '<img src='.$big.$showFile.' height=400px ><br/>'; 
                    

                    echo '<a href="?previous=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    <img src= "Lbutton.png"width="40" height="40"></a>';
                    echo '<a href="?next=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    <img src= "Rbutton.png"width="40" height="40"></a>';
               
                ?>
              
               
               
               
           
       
           <!grønt felt>
          
           
           
           <div id="buttons">
               
               <!gult felt>
               
               
               <div id="toprow">
                   
                   <!ratingknapper>
                   <span class="rating">
                       
        <input type="radio" class="rating-input"
            id="rating-input-1-5" name="rating-input-1">
        <label for="rating-input-1-5" class="rating-star"; ></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-4" name="rating-input-1">
        <label for="rating-input-1-4" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-3" name="rating-input-1">
        <label for="rating-input-1-3" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-2" name="rating-input-1">
        <label for="rating-input-1-2" class="rating-star"></label>
        <input type="radio" class="rating-input"
            id="rating-input-1-1" name="rating-input-1">
        <label for="rating-input-1-1" class="rating-star"></label>
                       
    </span>
                   
                   <!kommentarfelt>
                   <textarea rows="1,5" cols="100" placeholder="Comment"></textarea>
                   <!toolsbutton>
                   <input type="button" style="background-color:lightgrey; width:80px; position: absolute;" value="Tools">
                   
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
            
            $currentImage = substr($_GET['bilde'],7);
            print_r($currentImage);

            $query1 = "SELECT fileid FROM file_liste WHERE filename='$currentImage'";
            $result1 = $db->query($query1);
         	
			
			if (!empty($result1)){
			foreach($result1 as $rr)
			{
				$result3 = (array_to_string($rr));
			}
		}
			
			if(!empty($_POST['tag'])){
				$svaret = $_POST['tag'];
				$query = "INSERT INTO tag(fileid, tags) VALUES ($result3 , '$svaret')";
				$result = $db->query($query);
			}
			
			?>  
                   
               </div>
               
            
           </div>
           </div>
    
        
    </body>
    </html>