<?php
	$price_list = "prices_6.txt";

	if ( file_exists ( $price_list )  &&  is_readable ( $price_list ) )
	{
		$booster_price_file = file_get_contents ( $price_list );
		echo $booster_price_file;
	/*	$price_array = preg_split ( "/[\s]+/", $booster_price_file );

		for ( $i = 0; $i < count ( $price_array ); $i++ )
		{
			echo $price_array[$i];
		}
*/
	}
	else
	{
		echo: "Error: Price List not found.";
	}



?>