<!DOCTYPE html>
    <html>
    <head>
        <title>fullscreen</title>
        <meta http-equiv="content-type" content="=text/html; charset=utf-8 without BOM"/>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="img/favicon.ico">
        
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
        <link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"   type="text/javascript" charset="utf-8"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js"    type="text/javascript" charset="utf-8"></script>
        <script src="js/tag-it.js" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript" src="exif-js-master/binaryajax.js"></script>
		<script type="text/javascript" src="exif-js-master/exif.js"></script>

        <!javascript kommentarboks>
        <script type="text/javascript">
            function exchange(el)
			{
                var ie=document.all&&!document.getElementById? document.all : 0;
                var toObjId=/b$/.test(el.id)? el.id.replace(/b$/,'') : el.id+'b';
                var toObj=ie? ie[toObjId] : document.getElementById(toObjId);
                if(/b$/.test(el.id))
                    toObj.innerHTML=el.value;
                else{
                    toObj.style.width=200+'px';
                    toObj.value=el.innerHTML;
                }
                el.style.display='none';
                toObj.style.display='inline';
            }
			
             // IN PROGRESS
            // function leftArrowPressed() {
                // var list = imgList();
                // var imgLoc = getURLParameter('bilde');
                // var imgName = imgLoc.split("/");
                // var place = list.indexOf(imgName[1]);
                // if (place == 0)place = list.length;
                // var previous = list[place - 1];
                
               // window.location.assign("fullscreen.php?tag=null&bilde=Bilder%2F" + previous);
                    
            // }
            
            // function rightArrowPressed() {
                // var list = imgList();
                // var imgLoc = getURLParameter('bilde');
                // var imgName = imgLoc.split("/");
                // var place = list.indexOf(imgName[1]);
                // var next = list[place + 1];
                // if (place == list.length)place = 0;

               // window.location.assign("fullscreen.php?tag=null&bilde=Bilder%2F" + next);
            // }
            
            function imgList() {
                <?php
                    include_once("funksjoner.php");
                    $files = get_img_list($big);
                    
                    $number = count($files);
                    $key = array_search(basename($_GET['bilde']), $files);
                ?>
                var imgList = <?php 
                                echo json_encode($files);
                                ?>;
                return imgList;
                    
            }
			
			// var commentField = document.getElementById('itm1b');
            function onKeyDown(element){
				switch (event.keyCode) {
                    case 13: //Enter
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						xmlhttp.open('POST', 'rotation.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("comment=" + encodeURIComponent(element.value) + "&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText!==""){
								alert('Comment Error!!! '+ xmlhttp.responseText);
							}else{
								showComment()
							};
						}
                        break;
                    case 27: //Escape
                       alert('AAAAAAAAAaaaaaaaaaaaaaaaa');
                        break;
                }
			}
			
            document.onkeydown = function(evt) {
                evt = evt || window.event;
                switch (evt.keyCode) {
                    case 37:
                        // leftArrowPressed();
						prevImg();
                        break;
                    case 39:
                        // rightArrowPressed();
						nextImg();
                        break;
                }
            };
            
            function getURLParameter(name) {
                return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
		}
// PROGRESS
        </script>
		
		<script type="text/javascript">
									
			function musOverPilVenstre(musover)
			{
			musover.src = "PilVenstre.png";
			}
			function musOverPilHoyre(musover)
			{
			musover.src = "PilHoyre.png";
			}
			function musIkkeOver(musover)
			{
			musover.src = "Tom.png";
			}
			
			function musOverKnapp(musover)
			{
			musover.src = "Rotasjonsmeny.png";
			}
			function musIkkeOverKnapp(musover)
			{
			musover.src = "TomRotasjonsmeny.png";
			}
			
			function musOverRotasjonVenstre(musover)
			{
			musover.src = "RoterVenstre.png";
			}
			function musOverRotasjonHoyre(musover)
			{
			musover.src = "RoterHoyre.png";
			}
			function musOverAvslutt(musover)
			{
			musover.src = "LukkVindu.png";
			}
			function musIkkeOverRotasjonsknapp(musover)
			{
			musover.src = "";
			}
		</script>	
        
    </head>
        
    <body>

    
    <?php
    include_once("mysql.php");
    include_once("funksjoner.php");
    db_connnect();
	
	// alert_message("POST allert: ".print_r($_POST));
	
		global $imgListStr;
		if (empty($_GET['tag']) || $_GET['tag']=='null'){
		//	$imgList = db_select('file_liste', 'filename', '', 'filename');//test
			
		// ALERT TESTZONE FOR IMAGE-SCROLL BASED ON SEARCH RESULTS
			
		$ratinginput = 'null';
		$search = 'null';
		$ratingcategory = 'null';

		if(!empty($_GET['ratinginput'])){$ratinginput = $_GET['ratinginput'];}
		if(!empty($_GET['search'])){$search = $_GET['search'];}
		if(!empty($_GET['ratingcategory'])){$ratingcategory = $_GET['ratingcategory'];}
	
		if($ratinginput == 'null'){$ratinginput = "";}
		if($search == 'null'){$search = "";}
		if($ratingcategory == 'null'){$ratingcategory = "";}
	
		if(!$ratingcategory==""){
			$imgList = get_search_list($ratinginput, $search, $ratingcategory);
		}
		else{
			$imgList = db_select('file_liste', 'filename', '', 'filename');
		}
		
		// TESTZONE END
		
		}else{
			$group = "inner join tag on file_liste.fileid = tag.fileid where tag.tags = '".$_GET['tag']."'";
			$imgList = db_select('file_liste', 'filename', $group, 'filename');
		}
		$imgListStr = array_to_string($imgList);
	
	global $currentImage;
	global $result3;
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
	<?php	
		$leggTilTaggnavn = $_GET["leggTilTaggnavn"];
		if ("null" != $leggTilTaggnavn)
		{
			$query = "INSERT INTO tag(fileid, tags) VALUES ($result3 , '$leggTilTaggnavn')";
			$result = $db->query($query);
		}
		
		$slettTaggnavn = $_GET["slettTaggnavn"];
		if ("null" != $slettTaggnavn)
		{
			$query = "DELETE FROM tag WHERE tags = '$slettTaggnavn' AND fileid=$result3;";
			$result = $db->query($query);
		}
	?>
        
       <div id="containermain">
           
           <div id="leftcontainer">
           

                      
			   <div id="details">
					<h3>Picture details</h3>
					<b>Current file: </b>
						<span id='nameStr'>
							<?php print_r($currentImage);?>
						</span><br>
					<b>Tags:</b>
					<span id='tagsStr'>
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
					?>
					</span><br>
					
					<b>Comment:</b>
					<span id='CommentStr'>
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
					</span>
					
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
					</span><br>
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
					<span class="rating" id = "ratingstar"> 
						<input type="radio" value="5" class="rating-input"
							id="rating-input-1-5" name="ratinginput" '.$rating5.' onChange="setRate(5)">
						<label for="rating-input-1-5" class="rating-star"></label>
						
						<input type="radio" value="4" class="rating-input"
							id="rating-input-1-4" name="ratinginput" '.$rating4.' onChange="setRate(4)">
						<label for="rating-input-1-4" class="rating-star"></label>
						
						<input type="radio" value="3" class="rating-input"
							id="rating-input-1-3" name="ratinginput" '.$rating3.' onChange="setRate(3)">
						<label for="rating-input-1-3" class="rating-star"></label>
						
						<input type="radio" value="2" class="rating-input"
							id="rating-input-1-2" name="ratinginput" '.$rating2.' onChange="setRate(2)">
						<label for="rating-input-1-2" class="rating-star"></label>
						
						<input type="radio" value="1" class="rating-input"
							id="rating-input-1-1" name="ratinginput" '.$rating1.' onChange="setRate(1)">
						<label for="rating-input-1-1" class="rating-star"></label>
					</span>';
				?>
				
				</form>
				<script>
					var $_GET = parseGetParams();
					var fileNames = getImgList();
					var corImg = getCurrentImg();
					
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

					function rotate(angle){
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						xmlhttp.open('POST', 'rotation.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("angle=" + encodeURIComponent(angle) + "&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText!==""){
								alert('Rotation '+angle+' Error!!! '+ xmlhttp.responseText);
							}else{
								hiddenImg 		= new Image();
								hiddenImg.src 	= fileName+'?trash='+Math.random();
								var fullimg 	= document.getElementById('fullimg');
								fullimg.src 	= hiddenImg.src;
							};
						}
					}
				
					function getImgList(){
						var str = "<?php   echo($imgListStr) ?>";
						var imgList = str.split(', ')
						return imgList;
					}
				
					function getCurrentImg(){
						var fileName = $_GET['bilde'].substr(7);
						for (var i=0,len=fileNames.length; i<len; i++)
						{ 
							if (fileNames[i] == fileName){
								return i;
							}
						}
					}
			
					function nextImg(){
						var length = fileNames.length;
						var next = (corImg+1)%length;
						
						var hiddenNextImg = new Image();
						hiddenNextImg.src = 'Bilder/'+fileNames[next]+'?rand='+Math.random();
						var fullimg = document.getElementById('fullimg');
						fullimg.src = hiddenNextImg.src;
						
						corImg = next;
						
						showData();
					}
		
					function prevImg(){
						var length = fileNames.length;
						var prev = (corImg-1)%length;
						if (prev < 0){
							prev = length-1;
						}
						var hiddenNextImg = new Image();
						hiddenNextImg.src = 'Bilder/'+fileNames[prev]+'?rand='+Math.random();
						
						var fullimg = document.getElementById('fullimg');
						fullimg.src = hiddenNextImg.src;
						
						corImg = prev;
						
						showData();
					}
			
					function showData(){
						var imgData = getImgData();
						showRate(imgData[0]);
						showComment(imgData[1]);
						showTags(imgData[2]);
						document.getElementById('nameStr').innerHTML = fileNames[corImg];
					}
				
					//---- checks the string by template(rate: [1-5]#comment: STRING#tags: STRING,STRING,STRING;)
					function checkImgData(str){
						var checkStart 	= str.search("rate: ");
						var checkEnd	= str[str.length-1];
						return (checkStart==0 && checkEnd==';')
					}
			
					function getImgData(){
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						
						xmlhttp.open('POST', 'imgData.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText!=""){
								var respText = xmlhttp.responseText;
								if (checkImgData(respText)){
									var imgStrData 	= respText.split("#");

									var rate = getRating(imgStrData[0]);
									var comment	= getComment(imgStrData[1]);
									var tags = getTags(imgStrData[2]);
									
									var imgData	= [rate,comment,tags];
									return imgData;
								}else{
									return ['0','',['']];
								}
							}else{
								return [0,'',['']];
							};
						}
					}
		
					function getRating(str){
						if(str.length==7){
							return str.substr(6,1);
						}else{
							return '0';
						}
					}
		
					function getComment(str){
						if(str.length>9){
							return str.substr(9);
						}else{
							return ' ';
						}
					}
		
					function getTags(str){
						if(str.length>7){
							str = str.substr(6);
							str = str.slice(0,-1);
							
							return str.split(",");
						}else{
							return [' '];
						}
					}
					
					function setRate(rate){
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						xmlhttp.open('POST', 'imgData.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("rate=" + encodeURIComponent(rate) + "&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText==""){
								showRate(rate);
							}else{
								alert('Rating Error!!! \n'+ xmlhttp.responseText);
							};
						}
					}
					
					function setComment(comment){
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						xmlhttp.open('POST', 'imgData.php', false);
						xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
						xmlhttp.send("comment=" + encodeURIComponent(comment) + "&name=" + encodeURIComponent(fileName));
						if(xmlhttp.status == 200) {
							if (xmlhttp.responseText==""){
								showComment(comment);
							}else{
								alert('Rating Error!!! \n'+ xmlhttp.responseText);
							};
						}
					}
					
					function setTags(str){
						
					}
					
					function showRate(rate){
						var rateI = parseInt(rate, 10);
						var span = document.getElementById('ratingStr');
						var star = document.getElementById('ratingstar');
						if ( rateI ){
							if (rateI> 0 & rateI < 6){
								span.innerHTML = rateI;
								switch(rateI){
									case 1:
										rating1 = 'checked';
										rating2 = '';
										rating3 = '';
										rating4 = '';
										rating5 = '';
										break;
									case 2:
										rating1 = '';
										rating2 = 'checked';
										rating3 = '';
										rating4 = '';
										rating5 = '';
										break;
									case 3:
										rating1 = '';
										rating2 = '';
										rating3 = 'checked';
										rating4 = '';
										rating5 = '';
										break;
									case 4:
										rating1 = '';
										rating2 = '';
										rating3 = '';
										rating4 = 'checked';
										rating5 = '';
										break;
									case 5:
										rating1 = '';
										rating2 = '';
										rating3 = '';
										rating4 = '';
										rating5 = 'checked';
										break;
								}
							}else{
								span.innerHTML = "0";
								rating1 = '';
								rating2 = '';
								rating3 = '';
								rating4 = '';
								rating5 = '';
							}
						}else{
							span.innerHTML = "0";
							rating1 = '';
							rating2 = '';
							rating3 = '';
							rating4 = '';
							rating5 = '';

						}
						star.innerHTML = ''+
								'<input type="radio" value="5" class="rating-input"'+
									'id="rating-input-1-5" name="ratinginput" '+rating5+' onChange="setRate(5)">'+
								'<label for="rating-input-1-5" class="rating-star"></label>'+
								'<input type="radio" value="4" class="rating-input"'+
									'id="rating-input-1-4" name="ratinginput" '+rating4+' onChange="setRate(4)">'+
								'<label for="rating-input-1-4" class="rating-star"></label>'+
								'<input type="radio" value="3" class="rating-input"'+
									'id="rating-input-1-3" name="ratinginput" '+rating3+' onChange="setRate(3)">'+
								'<label for="rating-input-1-3" class="rating-star"></label>'+
								'<input type="radio" value="2" class="rating-input"'+
									'id="rating-input-1-2" name="ratinginput" '+rating2+' onChange="setRate(2)">'+
								'<label for="rating-input-1-2" class="rating-star"></label>'+
								'<input type="radio" value="1" class="rating-input"'+
									'id="rating-input-1-1" name="ratinginput" '+rating1+' onChange="setRate(1)">'+
								'<label for="rating-input-1-1" class="rating-star"></label>';
					}
			
					function showComment(str){
						var span = document.getElementById('CommentStr');
						span.innerHTML = str;
					}
				
					function showTags(arr){
						var span = document.getElementById('tagsStr');
						span.innerHTML = arr.toString();
					}
				</script>
				
                   <!kommentarfelt>
        
<form action="" method="post" id='commentForm'>
<input type = "text" name = "comment" value="<?php	
	$query2 = "SELECT commentary FROM file_liste WHERE fileid=$result3";
	$result2 = $db->query($query2);
	foreach($result2 as $rr)
		
	if (!empty($result2)){
		foreach($result2 as $rr) {
			print_r(array_to_string($rr));
			if(null === (array_to_string($rr))) echo("click to comment");
		}
	}
	if(!empty($_POST['comment'])){
	$svaret = $_POST['comment'];
	$query = "UPDATE file_liste SET commentary='$svaret' WHERE fileid=$result3";
	$result = $db->query($query);
	echo'<meta http-equiv="refresh" content="0" />';
	
	if(!empty($_POST['comment'])){
	$svaret = $_POST['comment'];
	$query = "UPDATE file_liste SET commentary='$svaret' WHERE fileid=$result3";
	$result = $db->query($query);
	echo'<meta http-equiv="refresh" content="0" />';
}
}
?>" onFocus="javascript:this.select()" size="27";>
<!--input ondblclick="exchange(this);" onkeydown="onKeyDown(this)" id="itm1b" class="replace" type="text" value="click to comment"  name="comment"-->
<!--span id="itm1" onclick="exchange(this);"-->
<!--/span-->

</form>
					
					
               
           
                 
                   
                <!tagfelt>
               
               <script type="text/javascript">
                    $(document).ready(function()
					{
                        $("#myTags").tagit();
																		
						var sletteTagHendelse = $('#myTags');
						
						var ratinginput = getURLParameter('ratinginput');
						var search = getURLParameter('search');
						var ratingcategory = getURLParameter('ratingcategory');
						var tag = getURLParameter('tag');
						var total = "&ratinginput=" + ratinginput + "&search=" + search + "&ratingcategory=" + ratingcategory;
						
						if(ratingcategory=='null'){
							var total="";
						}
	
						sletteTagHendelse.tagit(
						{
							afterTagAdded: function(evt, ui)
							{
								window.location.assign("http://localhost/Project/fullscreen.php?tag=" + tag + "&bilde=" + getURLParameter('bilde') + "&leggTilTaggnavn=" + $("#myTags").tagit('tagLabel', ui.tag) + "&slettTaggnavn=null" + total);
							},
							afterTagRemoved: function(evt, ui)
							{
								window.location.assign("http://localhost/Project/fullscreen.php?tag=" + tag + "&bilde=" + getURLParameter('bilde') + "&slettTaggnavn=" + $("#myTags").tagit('tagLabel', ui.tag) + "&leggTilTaggnavn=null" + total);
							}
						}
						);
                    }
					);
                </script>
               
               

				<ul id="myTags">
            <!-- Existing list items will be pre-added to the tags -->
					<?php
						$query2 = "SELECT tags FROM tag WHERE fileid=$result3";
						$result2 = $db->query($query2);
						if (!empty($result2))
						{
							foreach($result2 as $rr)
							{
								echo '<li>'. array_to_string($rr).'</li>';
							}	
						}
					?>
				</ul>
                   
                        
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
                    else { 
						$showFile = substr($_GET['bilde'], 7); 
						echo '<img id = "fullimg" src='.$big.$showFile.'?rand='.rand().' height=85%><br/>'; 
					}
                    // if ((isset($_GET['previous'])) or (isset($_GET['next']))) {
                        // echo '<img id = "fullimg" src='.$_GET['bilde'].' height=85% ><br/>'; 
                    // }
                    // echo '<a href="?previous=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    // <img src= "Lbutton.png"width="40" height="40"></a>';
                    // echo '<a href="?next=1&amp;tag='.$_GET['tag'].'&amp;bilde='.urlencode($big.$showFile).'">
                    // <img src= "Rbutton.png"width="40" height="40"></a>';
               
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
		
		<div id="venstreknapp">
			<img src="Tom.png" width="256" height="77%" onmouseover="musOverPilVenstre(this)" onmouseout="musIkkeOver(this)" onclick="prevImg()">
		</div>
		<div id="hoyreknapp">
			<img src="Tom.png" width="256" height="77%" onmouseover="musOverPilHoyre(this)" onmouseout="musIkkeOver(this)" onclick="nextImg()">
		</div>
		<div id="rotasjonsmeny">
			<img src="TomRotasjonsmeny.png" width="256" height="128" onmouseover="musOverKnapp(this)" onmouseout="musIkkeOverKnapp(this)">
		</div>		
		<div id="roterVenstre">
			<img width="64" height="64" onmouseover="musOverRotasjonVenstre(this)" onmouseout="musIkkeOverRotasjonsknapp(this)" onclick="rotate(-90)">
		</div>
		<div id="roterhoyre">
			<img width="64" height="64" onmouseover="musOverRotasjonHoyre(this)" onmouseout="musIkkeOverRotasjonsknapp(this)" onclick="rotate(90)">
		</div>
		<div id="lukkVindu">
			<img width="64" height="64" onmouseover="musOverAvslutt(this)" onmouseout="musIkkeOverRotasjonsknapp(this)" onclick="window.close('fs')">
		</div>
			
    </body>
    </html>
