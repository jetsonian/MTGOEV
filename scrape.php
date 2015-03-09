<?php
	
	$price_list = "./prices_6.txt";

	$scraped_prices = array();

	if ( file_exists ( $price_list ) )
	{
//		echo "file exists<br/>";
		$booster_price_file = file_get_contents ( $price_list );
//		echo "file read<br/>";
//		echo $booster_price_file;
		$price_array = preg_split ( "/[\s]+/", $booster_price_file );

		$i = 0;
		
		while ( $price_array[$i][0] != '=' )
		{
			$i++;
		}
		$i += 3;
//		echo $price_array[$i] . "<br/>";
		while ( $i < count ( $price_array ) )
		{
			if ( $price_array[$i][0] == '[')
			{
//				echo substr( $price_array[$i], 1, 3 ) . "<br/>";
				$temp_code = substr( $price_array[$i], 1, strlen ( $price_array[$i] ) - 2 );
				$temp_buy = (double) $price_array[$i+1];
				$temp_sell = (double) $price_array[$i+2];

				$new_size = array_push ( $scraped_prices, array ( $temp_code, $temp_buy, $temp_sell ) );

//				$temp_set = array ( $temp_code, $temp_buy, $temp_sell );
//				echo $temp_set[0] . " ";
//				echo $temp_set[1] . " ";
//				echo $temp_set[2] . "<br/>";

				$i += 2;
			}
			$i++;
		}
		for ($j = 0; $j < count ( $scraped_prices ); $j+=1 )
		{
			echo $j . ": ";
			echo $scraped_prices[$j][0] . " ";
			echo $scraped_prices[$j][1] . " ";
			echo $scraped_prices[$j][2] . "<br/>";
		}
	}

?>