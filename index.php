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
	<table border="1">
		<tr>
			<td>
				<h1>Gruppe F's fantastiske løsning til å laste opp filer</h1>
				<form action="Opplasting.php" method="post" enctype="multipart/form-data">
					<input name="bildefil[]" id="bildefil" type="file" multiple=""><br>
					<input type="submit" name="submit" value="Submit">
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<p>This is the directory script</p>
				<?php					
					LastInnMetadata();
					VisBilder();
				?> 
			</td>
		</tr>
		<tr>
			<td>
				<p>This is the EXIF script</p>
			</td>
		</tr>
	</table>
	<embed height="50" width="100" src="Kalimba.mp3" style= visibilty: hidden>
</body>
</html>