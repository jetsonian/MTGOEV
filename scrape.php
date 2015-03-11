<?php	
	function scrape_supernovabots_boosterprice ( $price_list )
	{
		$scraped_prices = array();
		if ( $booster_price_file = file_get_contents ( $price_list ) )
		{
			$price_array = preg_split ( "/[\s]+/", $booster_price_file );
			$i = 0;
			while ( $price_array[$i][0] != '=' )
			{
				$i++;
			}
			$i += 3;
			while ( $i < count ( $price_array ) )
			{
				if ( substr( $price_array[$i], 0, 1 ) ==  "[" )
				{
					$temp_code = substr( $price_array[$i], 1, strlen ( $price_array[$i] ) - 2 );
					$temp_buy = (double) $price_array[$i+1];
					$temp_sell = (double) $price_array[$i+2];
					$new_size = array_push ( $scraped_prices, array ( $temp_code, $temp_buy, $temp_sell ) );
					$i += 2;
				}
				$i++;
			}
/*			for ($j = 0; $j < count ( $scraped_prices ); $j+=1 )
			{
				echo $j . ": ";
				echo $scraped_prices[$j][0] . " ";
				echo $scraped_prices[$j][1] . " ";
				echo $scraped_prices[$j][2] . "<br/>";
			}
*/			return $scraped_prices;
		}
		else
		{
			return false;
		}
	}

	$price_list = 'http://www.supernovabots.com/prices_6.txt';
	$scraped_list = scrape_supernovabots_boosterprice( $price_list );

	$db_host = "localhost";
	$db_name = "mtgoev";
	$db_user = "mtgoev";
	$db_pass = "password";

	$db_connection = new mysqli ( $db_host, $db_user, $db_pass, $db_name );

	$db_query = "TRUNCATE TABLE prices";
	$db_result = $db_connection->query ( $db_query );

	foreach ( $scraped_list as $entry )
	{
		$db_query = "INSERT INTO prices ( code, snb_buy, snb_sell ) VALUES ( '"  .
			$entry[0] . "', " .
			$entry[1] . ", " . 
			$entry[2] . " )";
		$db_result = $db_connection->query ( $db_query );
	}

	$db_connection->close ();
?>