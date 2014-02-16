    <!DOCTYPE html>
    <html>
    <head>
        <title>fullscreen</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <!-------------------------------------------------------------------------------------->
    <body>
        
        
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
                   <input type="button" style="background-color:lightgrey; width:100px; position: absolute;" value="Tools">
                   
               </div>
                      
           
           
           
           
           
               <!brunt felt>
               <div id="bottomrow">    
                <!tagfelt>
                   <textarea rows="1,5" cols="30" placeholder="Tags"></textarea>
                   
               </div>
               
            
           </div>
           </div>
    
        
    </body>
    </html>