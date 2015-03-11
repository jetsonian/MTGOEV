<?php
	function get_db_prices ( )
	{
		$price_list = array ( );

		$db_host = "localhost";
		$db_name = "mtgoev";
		$db_user = "mtgoev";
		$db_pass = "password";

		$db_connection = new mysqli ( $db_host, $db_user, $db_pass, $db_name );

		$db_query = "SELECT * FROM prices";
		$db_result = $db_connection->query ( $db_query );

		while ( $db_row = $db_result->fetch_row ( ) )
		{
			$temp_array = array ( $db_row[1], $db_row[2], $db_row[3] );
			array_push ( $price_list, $temp_array );
		}

		return $price_list;
	}

	function find_price ( $price_list, $set_code, $price_type )
	{
		foreach ( $price_list as $set )
		{
			if ( $set[0] == $set_code )
			{
				if ( $price_type == "buy" )
					return $set[1];
				else
					return $set[2];
			}
		}
		return false;
	}

	function calculate_prize_value_draft ( $price_list, $entry, $win_percentage, $format )
	{
		$pack_prices = array ( );
		$ev = 0.00;

		foreach ( $entry as $pack )
		{
			$pack_price = find_price ( $price_list, $pack, "buy" );
			array_push ( $pack_prices, $pack_price );

		}
		if ( $format == "8-4" )
		{
			if ( $entry[0] == $entry[1] && $entry[1] == $entry[2] )
			{
				$expected_packs = ( 8 * pow ( $win_percentage, 3 ) )
					+ ( 4 * ( pow ( $win_percentage, 2 ) * ( 1 - $win_percentage ) ) );
				$ev = $expected_packs * $pack_prices[0];
			}
			elseif ( $entry[0] != $entry[1] && $entry[1] == $entry[2] )
			{	
				$ev = ( ( 5 * $pack_prices[1] + 3 * $pack_prices[0] ) * pow( $win_percentage, 3 ) ) +
					( ( 3 * $pack_prices[1] + 1 * $pack_prices[0] ) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) );
			}
			else
			{
				$ev = ( ( 2 * $pack_prices[0] + 3 * $pack_prices[1] + 3 * $pack_prices[2] ) * pow( $win_percentage, 3 ) ) +
					( ( 2 * $pack_prices[0] + $pack_prices[1] + $pack_prices[2] ) *  pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) );
			}
		} elseif ( $format = "4-3-2-2" )
		{
			if ( $entry[0] == $entry[1] && $entry[1] == $entry[2] )
			{
				$expected_packs = ( 4 * pow ( $win_percentage, 3 ) )
					+ ( 3 * ( pow ( $win_percentage, 2 ) * ( 1 - $win_percentage ) ) )
					+ ( 2 * ( $win_percentage * ( 1 - $win_percentage ) ) );
				$ev = $expected_packs * $pack_prices[0];
			}
			elseif ( $entry[0] != $entry[1] && $entry[1] == $entry[2] )
			{	
				$ev = ( ( 3 * $pack_prices[1] + 1 * $pack_prices[0] ) * pow( $win_percentage, 3 ) )
					+ ( ( 2 * $pack_prices[1] + 1 * $pack_prices[0] ) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( ( $pack_prices[1] + $pack_prices[0] ) * ( $win_percentage * ( 1 - $win_percentage ) ) );
			}
			else
			{
				$ev = ( ( 2 * $pack_prices[0] + 1 * $pack_prices[1] + 1 * $pack_prices[2] ) * pow( $win_percentage, 3 ) )
					+ ( ( $pack_prices[0] + $pack_prices[1] + $pack_prices[2] ) *  pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( ( $pack_prices[0] + $pack_prices[1] ) * ( $win_percentage * ( 1 - $win_percentage ) ) );
			}
		}

		return $ev;
	}

	$price_list = get_db_prices ( );

	$entry = array ( "M15", "M15", "M15" );
	$format = "4-3-2-2";

	for ( $i = 0.00; $i <= 100.00; $i++ )
	{ 
		echo $i . "%: " . 
			number_format ( calculate_prize_value_draft ( $price_list, $entry, $i / 100.00, $format ), 3 ) . 
			" tix<br/>";
	}
?>