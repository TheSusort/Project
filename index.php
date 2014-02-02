<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title> Gruppe F's fantastiske løsning til å laste opp filer </title>
	<style>
		body {
			margin: 0 auto 20px;
			padding: 0;
			background: #000000;
			text-align: center;

		}

		td {
			padding: 0 0 10px;
			text-align: center;
			font: 9px sans-serif;
			color: #FFFFFF;
		}
		table {

			width: 100%;
		}
		img {
			display: block;
			margin: 20px auto 10px;
			max-width: 900px;
			outline: none;
			
		}
		img:active {
			max-width: 100%;
		}
		a:focus {
			outline: none;
		}
	</style>
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
	 <?php

$images = "upload/"; # Location of small versions
$big    = ""; # Location of big versions (assumed to be a subdir of above)
$cols   = 3; # Number of columns to display

if ($handle = opendir($images)) {
   while (false !== ($file = readdir($handle))) {
       if ($file != "." && $file != ".." && $file != rtrim($big,"/")) {
           $files[] = $file;
       }
   }
   closedir($handle);
}

$colCtr = 0;

echo '<table width="100%" cellspacing="3"><tr>';

foreach($files as $file)
{
  if($colCtr %$cols == 0)
	echo '</tr><tr><td colspan="'.$cols.'"><hr /></td></tr><tr>';
	echo '<td align="center"><a href="' . $images . $big . $file . '"><img src="' . $images . $file . ' "width="200px" length="auto" /></a></td>';
	$colCtr++;
}

echo '</table>' . "\r\n";

?> 
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