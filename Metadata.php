<?php
	$data;
	
	// Kjøres når programmet starter.
	function LastInnMetadata()
	{	
		$teller = 0;
		
		// Setter opp en todimensjonal array til meatadata.
		$data = array(array());
		foreach (glob("Bilder/*.jpg") as $bilder)
		{
			$data[$teller][0] = "Legg til filnavn her";
			$data[$teller][1] = "Legg til karakter her";
			$data[$teller][2] = "Legg til tagg her";
			$data[$teller][3] = "Legg til komentarer her";
			
			$teller++;
		}
		
		return;
	}	
?>