<!DOCTYPE html>
<html>
    <head>
        <title>fullscreen</title>
        <meta http-equiv="content-type" content="=text/html; charset=utf-8 without BOM"/>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="img/favicon.ico">
        
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css">
        <link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"   type="text/javascript" charset="utf-8"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js"    type="text/javascript" charset="utf-8"></script>
        <script src="js/tag-it.js" type="text/javascript" charset="utf-8"></script>
		
		<?php
			include_once("mysql.php");
			include_once("funksjoner.php");
			db_connnect();
			
			ob_start();
		?>

        <!javascript kommentarboks>
        <script type="text/javascript">
  		
            document.onkeydown = function(evt) {
                evt = evt || window.event;
                switch (evt.keyCode) {
                    case 37:
                        // leftArrowPressed
						prevImg();
                        break;
                    case 39:
                        // rightArrowPressed
						nextImg();
                        break;
                }
            };
            
            function getURLParameter(name) {
                return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
			}

        </script>
		
		<script type="text/javascript">
									
			function musOverPilVenstre(musover)
			{
				musover.src = "img/leftbutton.png";
			}
			function musOverPilHoyre(musover)
			{
				musover.src = "img/rightbutton.png";
			}
			function musIkkeOver(musover)
			{
				musover.src = "img/Tom.png";
			}
		</script>	
        
    </head>
        
    <body>

    
    <?php
    	
		global $imgListStr;
		if (empty($_GET['tag']) || $_GET['tag']=='null'){
				
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
						$result3 = array_to_string($rr);
						$result4 = $rr;
						}	
					}
	
    ?>
	<?php	
		$leggTilTaggnavn = $_GET["leggTilTaggnavn"];
		if ("null" != $leggTilTaggnavn)
		{
			$query = "INSERT INTO tag(fileid, tags) VALUES ($result3 , '$leggTilTaggnavn')";
			$result = $db->query($query);
			
			add_Tag_exif($_GET['bilde'], $leggTilTaggnavn);
		}
		
		$slettTaggnavn = $_GET["slettTaggnavn"];
		if ("null" != $slettTaggnavn)
		{
			$query = "DELETE FROM tag WHERE tags = '$slettTaggnavn' AND fileid='$result3';";
			$result = $db->query($query);
			
			del_Tags_exif($_GET['bilde'], $slettTaggnavn);
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
					   console.log($_GET);
					   return $_GET; 
					} 

					function rotate(angle){
						document.getElementById('progress').style.visibility="visible";
						var xmlhttp = getXmlHttp();
						var fileName = 'Bilder/'+fileNames[corImg];
						console.log(fileName);
						setTimeout(function(){
							xmlhttp.open('POST', 'rotation_1.php', false);
							xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
							xmlhttp.send("angle=" + encodeURIComponent(angle) + "&name=" + encodeURIComponent(fileName));
							
							if(xmlhttp.status == 200) {
								if (xmlhttp.responseText!==""){
									alert('Rotation '+angle+' Error!!! '+ xmlhttp.responseText);
								}else{
									hiddenImg 		= new Image();
									hiddenImg.src 	= fileName+'?trash='+Math.random();
									var fullimg = document.getElementById('fullimg');
									fullimg.src 	= hiddenImg.src;
								};
							}
							document.getElementById('progress').style.visibility="hidden";
						},1000);
					}
					
					function deleteImage(path){
						var r=confirm("you sure that you want to delete the file "+fileNames[corImg]+" ?");
						if (r==true)
						{
							var xmlhttp = getXmlHttp();
							var fileName = 'Bilder/'+fileNames[corImg];
							xmlhttp.open('POST', 'rotation_1.php', false);
							xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
							xmlhttp.send("delete=true" + "&name=" + encodeURIComponent(fileName));
							if(xmlhttp.status == 200) {
								if (xmlhttp.responseText!==""){
									alert('Rotation '+angle+' Error!!! '+ xmlhttp.responseText);
								}else{
									nextImg();
								};
							}
						}
					}
					
					function getImgList(){
						var imgList = <?php  
							$files = get_imgs();
							echo (json_encode($files));
						?>;
						console.log(imgList);
						return imgList;
					}
				
					function getCurrentImg(){
						var fileName = $_GET['bilde'].substr(7);
						console.log(fileName);
						for (var i=0,len=fileNames.length; i<len; i++)
						{ 
							if (fileNames[i] == fileName){
								console.log(i);
								return i;
							}
						}
					}
			
					function nextImg(){
						var tag = getURLParameter('tag');
						var ratinginput = getURLParameter('ratinginput');
						var search = getURLParameter('search');
						var order = getURLParameter('SortingCategory');
			
						
						var extention = '';
						if(!empty(tag)){
							extention += "&tag="+ tag;
						}
						if(!empty(ratinginput)){
							extention += "&ratinginput=" + ratinginput;
						}
						if(!empty(search)){
							extention += "&search="+ search;
						}
						if(!empty(order)){
							extention += "&SortingCategory="+ order;
						}
						var length = fileNames.length;
						var next = (corImg+1)%length;
						var newpic = fileNames[next];
						window.location.assign("?&bilde=Bilder/" + newpic + "&leggTilTaggnavn=null" + "&slettTaggnavn=null" + extention);
					}
		
					function empty( mixed_var ) {   // Determine whether a variable is empty
						return ( mixed_var === "" || mixed_var === 0   || mixed_var === "0" || mixed_var === null || mixed_var == "null" || mixed_var === false  );
					}
					
					function prevImg(){
					
						var tag = getURLParameter('tag');
						var ratinginput = getURLParameter('ratinginput');
						var search = getURLParameter('search');
						var order = getURLParameter('SortingCategory');
						
						var extention = '';
						if(!empty(tag)){
							extention += "&tag="+ tag;
						}
						if(!empty(ratinginput)){
							extention += "&ratinginput=" + ratinginput;
						}
						if(!empty(search)){
							extention += "&search="+ search;
						}
						if(!empty(order)){
							extention += "&SortingCategory="+ order;
						}
					
						var length = fileNames.length;
						var prev = (corImg-1)%length;
						if (prev < 0){
							prev = length-1;
						}
						var newpic = fileNames[prev];
						window.location.assign("?bilde=Bilder/" + newpic + "&leggTilTaggnavn=null" + "&slettTaggnavn=null" + extention);
						
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
									
									var rate = getRating(respText);
									var comment	= getComment(respText);
									var tags = getTags(respText);
									
									var imgData	= [rate,comment,tags];
									return imgData;
								}else{
									return ['0','',['']];
								}
							}else{
								return ['0','',['']];
							};
						}
					}
		
					function getRating(imgStrData){
						var imgData = imgStrData.split("#");
						for(var i=0; i<imgData.length; i++){
							var data = imgData[i].split(':');
							if (data[0] == 'rate'){
								return data[1];
							}
						}
						return '0';
					}
		
					function getComment(imgStrData){
						var imgData = imgStrData.split("#");
						for(var i=0; i<imgData.length; i++){
							var data = imgData[i].split(':');
							if (data[0] == 'comment'){
								return data[1];
							}
						}
						return ' ';
					}
		
					function getTags(imgStrData){
						var imgData = imgStrData.split("#");
						for(var i=0; i<imgData.length; i++){
							var data = imgData[i].split(':');
							if (data[0] == 'tags'){
								data[1] = data[1].slice(0,-1);
								if (data[1].length != 0)
									return data[1].split(",");
							}
						}
						return [' '];
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
								document.getElementById('idCheck').hidden=false;
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
						
						changeTags(arr);
						
					}
					
					function changeTags(value){
						var myNode = document.getElementById("myTags");
						while (myNode.firstChild) {
							myNode.removeChild(myNode.firstChild);
						}
						if (value[0]!=' '){
							for (var i=0; i<value.length; i++){
								var ListElem = document.createElement('LI');
									ListElem.className = "tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable";
							
								var SpanElem0 = document.createElement('SPAN');
									SpanElem0.innerHTML = value[i];
									SpanElem0.className = "tagit-label";
								
								var AElem = document.createElement('A');
									AElem.className = "tagit-close";
								
								var SpanElem1 = document.createElement('SPAN');
									SpanElem1.innerHTML = '×';
									SpanElem1.className = "text-icon";
									
								var SpanElem2 = document.createElement('SPAN');
									SpanElem2.className = "ui-icon ui-icon-close";
									
								var InputElem = document.createElement('INPUT');
									InputElem.className = "tagit-hidden-field";
									InputElem.name = 'tags';
									InputElem.value = value[i];
									InputElem.type = 'hidden';
								
								AElem.appendChild(SpanElem1);
								AElem.appendChild(SpanElem2);
								
								ListElem.appendChild(SpanElem0);
								ListElem.appendChild(AElem);
								ListElem.appendChild(InputElem);
								
								myNode.appendChild(ListElem);
							}
						}
						var ListElem = document.createElement('LI');
							ListElem.className = "tagit-new";
							ListElem.innerHTML = '<input type="text" class="ui-widget-content ui-autocomplete-input" placeholder="click to tag" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">';
							
						myNode.appendChild(ListElem);
					}
					
					function commentFocus(element){
						document.getElementById('idCheck').hidden=true;
						if (element.value == 'click to comment'){
							element.value = ''
						}
					}
					
					function commentBlur(element){
						if (element.value == ''){
							element.value = 'click to comment'
						}
					}
					
					function commentEnter(element){
						if(event.keyCode==13){
							setComment(element.value);
						}
					}

				</script>
				
                   <!kommentarfelt>
				   
        
<div id="commentField" style="text-align:left;padding-left:3px">
<input type = "text" name = "comment" id="commentFeild" size="30" style="padding-left:3px" value="<?php
	$query2 = "SELECT commentary FROM file_liste WHERE fileid=$result3";
	$result2 = $db->query($query2);
	foreach($result2 as $rr){
		if (!empty($result2)){
			foreach($result2 as $rr)
			{
				print_r(array_to_string($rr));
				if(null === (array_to_string($rr))) echo("click to comment");
			}
		}
	}
	if(!empty($_POST['comment'])){
		$svaret = $_POST['comment'];
		$query = "UPDATE file_liste SET commentary='$svaret' WHERE fileid=$result3";
		$result = $db->query($query);
		header("Refresh:0");
	}
?>" onFocus="commentFocus(this)" onBlur="commentBlur(this)" onkeydown="commentEnter(this)" size="27"/>
<img src="img/check.jpg" width="15px" id="idCheck" hidden>
</div>
                   
                <!tagfelt>
               
               <script type="text/javascript">
                    $(document).ready(function()
					{
                        $("#myTags").tagit(
						{
							
						});
																		
						var sletteTagHendelse = $('#myTags');
						
						var img = getURLParameter('bilde');
						var tag = getURLParameter('tag');
						var ratinginput = getURLParameter('ratinginput');
						var search = getURLParameter('search');
						var order = getURLParameter('SortingCategory');;
						
						var extention = '';
						if(!empty(tag)){
							extention += "&tag="+ tag;
						}
						if(!empty(ratinginput)){
							extention += "&ratinginput=" + ratinginput;
						}
						if(!empty(search)){
							extention += "&search="+ search;
						}
						if(!empty(order)){
							extention += "&SortingCategory="+ order;
						}
						
						sletteTagHendelse.tagit(
						{
							afterTagAdded: function(evt, ui)
							{
								window.location.assign("?bilde=" + img + extention + "&leggTilTaggnavn=" + $("#myTags").tagit('tagLabel', ui.tag) + "&slettTaggnavn=null");
							},
							afterTagRemoved: function(evt, ui)
							{
								window.location.assign("?bilde=" + img + extention + "&slettTaggnavn=" + $("#myTags").tagit('tagLabel', ui.tag) + "&leggTilTaggnavn=null");
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

			<div id="fullscreenpic" >
				<div id="picturecontainer"  >
							
				<?php               

					$showFile = $_GET['bilde']; 
					echo '<img id="fullimg" src='.$showFile.'?rand='.rand().' onload="loadImage()"><br/>'; 
               
                ?>
				
					<div id="venstreknapp" >
						<table style="width: 100%; height: 100%; text-align: center;">
							<tr>
								<td> <img src="img/Tom.png" width="256" onmouseover="musOverPilVenstre(this)" onmouseout="musIkkeOver(this)" onclick="prevImg()">  </td>
							</tr>
						</table>
					</div>
					<div id="progress" style="visibility:hidden">
					</div>
					<div id="hoyreknapp">
						<table style="width: 100%; height: 100%; text-align: center;">
							<tr>
								<td> <img src="img/Tom.png" width="256" onmouseover="musOverPilHoyre(this)" onmouseout="musIkkeOver(this)" onclick="nextImg()">  </td>
							</tr>
						</table>
						
					</div>
				</div>
               
               <script type="text/javascript">
					function loadImage(){
						var div = document.getElementById("picturecontainer");
						var img = document.getElementById("fullimg");
						var img_w = img.width;
						var img_h = img.height;
						var img_diw_w = div.offsetWidth*0.85;
						var img_diw_h = div.offsetHeight*0.85;
						if (img_h >= img_w){
							var proportion = img_diw_h/img_h;
							img.height = img_diw_h;
							img.width = img_w*proportion;
						}else{
							var proportion = img_diw_h/img_h;
							img.width = img_diw_w;
							img.height = img_h*proportion;
						}
					}
			   
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
		
		<div id="roterVenstre">
			<img src="img/RoterVenstre.png" width="48" height="48" onclick="rotate(90)">
		</div>
		<div id="roterhoyre">
			<img src="img/RoterHoyre.png" width="48" height="48" onclick="rotate(270)">
		</div>
		<div id="lukkVindu">
			<img src="img/LukkVindu.png" width="48" height="48" onclick="deleteImage()">
		</div>
			
    </body>
</html>
