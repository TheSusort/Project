<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title> Gruppe F's fantastiske løsning til å laste opp filer </title>
</head>

<body>

<table border="1">

<tr>
<td>
    <h1> Gruppe F's fantastiske løsning til å laste opp filer </h1>


    <form action="upload_file.php" method="post"
          enctype="multipart/form-data">

        <label for="file">Filename:</label>
        <input type="file" name="file" id="file"><br>
        <input type="submit" name="submit" value="Submit">
    </form>

</td>
</tr>


<tr>
<td>

	<p> This is the directory script </p>
	<?php $folder = 'upload/'; $filetype = '*.*'; 
	$files = glob($folder.$filetype); echo '<table>'; 
	for ($i=0; $i<count($files); $i++) { echo '<tr><td>'; 
	echo '<a name="'.$i.'" href="#'.$i.'"><img src="'.$files[$i].'"height=25%" width="25%" /></a>'; 
	echo substr($files[$i],strlen($folder),strpos($files[$i], '.')-strlen($folder)); 
	echo '</td></tr>'; } echo '</table>'; ?>
</td>
</tr>

<tr>
<td>
	<p> This is the EXIF script </p>
</tr>
</td>

</table>

</body>
</html>