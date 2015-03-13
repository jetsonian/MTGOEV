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
			$temp_array = array ( $db_row[1], $db_row[2], $db_row[3], $db_row[4] );
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
				elseif ( $price_type = "sell" )
				{
					return $set[2];
				}
				elseif ( $price_type = "wotc" )
				{
					return $set[3];
				}
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
		} elseif ( $format == "4-3-2-2" )
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
					+ ( ( $pack_prices[1] + $pack_prices[0] ) * $win_percentage * ( 1 - $win_percentage ) );
			}
			else
			{
				$ev = ( ( 2 * $pack_prices[0] + 1 * $pack_prices[1] + 1 * $pack_prices[2] ) * pow( $win_percentage, 3 ) )
					+ ( ( $pack_prices[0] + $pack_prices[1] + $pack_prices[2] ) *  pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( ( $pack_prices[0] + $pack_prices[1] ) * $win_percentage * ( 1 - $win_percentage ) );
			}
		} elseif ( $format == "swiss" )
		{
			if ( $entry[0] == $entry[1] && $entry[1] == $entry[2] )
			{
				$ev = ( 3 * $pack_prices[0] * pow( $win_percentage, 3 ) )
					+ ( 2 * $pack_prices[0] * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( 1 * $pack_prices[0] * $win_percentage * pow ( 1 - $win_percentage, 2 ) );
			}
			elseif ( $entry[0] != $entry[1] && $entry[1] == $entry[2] )
			{	
				$ev = ( ( 2 * $pack_prices[1] + $pack_prices[0] ) * pow ( $win_percentage, 3 ) )
					+ ( ( $pack_prices[1] + $pack_prices[0] ) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( $pack_prices[1] * $win_percentage * pow ( 1 - $win_percentage, 2) );
			}
			else
			{
				$ev = ( ( $pack_prices[2] + $pack_prices[1] + $pack_prices[0] ) * pow( $win_percentage, 3) ) +
					+ ( ( $pack_prices[2] + $pack_prices[1] ) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( $pack_prices[1] * $win_percentage * pow( 1 - $win_percentage, 2 ) );
			}
		}

		return $ev;
	}

	function calculate_prize_value_sealed ( $price_list, $entry, $win_percentage, $format )
	{
		$pack_prices = array ( );
		$ev = 0.00;

		foreach ( $entry as $pack )
		{
			$pack_price = find_price ( $price_list, $pack, "buy" );
			array_push ( $pack_prices, $pack_price );

		}

		if ( $format == "4-pack" )
		{
			if ( $entry[0] == $entry[2] && $entry[1] == $entry[3] )
			{
				$ev = ( 6 * $pack_prices[0] * pow( $win_percentage, 3 ) )
					+ ( 3 * $pack_prices[0] * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( 1 * $pack_prices[0] * $win_percentage * ( 1 - $win_percentage ) );
			} elseif ( $entry[0] == $entry[1] && $entry[0] != $entry[2] )
			{
				$ev = ( ( 3 * $pack_prices[2] + 3 * $pack_prices[0] ) * pow( $win_percentage, 3 ) )
					+ ( ( 2 * $pack_prices[2] + $pack_prices[0] ) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
					+ ( $pack_prices[0] * $win_percentage * pow( 1 - $win_percentage, 2 ) );
			}
		}
		elseif ( $format == "16-man" )
		{

		}
		elseif ( $format == "daily" )
		{
			if ( $entry[0] == $entry[2] && $entry[1] == $entry[3] )
			{
				$ev = ( 11 * $pack_prices[0] * pow( $win_percentage, 4 ) )
					+ ( 6 * $pack_prices[0] * pow( $win_percentage, 3 ) * ( 1 - $win_percentage ) )
					+ ( 3 * $pack_prices[0] * pow( $win_percentage. 2 ) * pow( 1 - $win_percentage, 2 ) );
			} elseif ( $entry[0] == $entry[1] && $entry[0] != $entry[3] )
			{
				$ev = ( ( 4 * $pack_prices[2] + 7 * $pack_prices[0] ) * pow( $win_percentage, 4 ) )
					+ ( ( 3 * $pack_prices[2] + 3 * $pack_prices[0] ) * pow( $win_percentage, 3 ) * ( 1 - $win_percentage ) )
					+ ( ( 1 * $pack_prices[0] + 2 * $pack_prices[0] ) * pow( $win_percentage, 2 ) * pow( 1 - $win_percentage, 2 ) );
			}
		}

		return $ev;
	}

	function calculate_prize_value_constructed ( $price_list, $prizes, $win_percentage, $format )
	{
		$pack_prices = array ( );
		$ev = 0.00;

		foreach ( $prizes as $pack )
		{
			$pack_price = find_price ( $price_list, $pack, "buy" );
			array_push ( $pack_prices, $pack_price );

		}

		if ( $format == "2-man" )
		{
			$ev = ( $pack_prices[2] * $win_percentage );
		}
		elseif ( $format == "5-3-2-2" )
		{
			$ev = ( ( 3 * $pack_prices[2] + 2 * $pack_prices[1]) * pow( $win_percentage, 3 ) )
				+ ( ( 2 * $pack_prices[2] + 1 * $pack_prices[1]) * pow( $win_percentage, 2 ) * ( 1 - $win_percentage ) )
				+ ( ( 1 * $pack_prices[2] + 1 * $pack_prices[1]) * $win_percentage * ( 1 - $win_percentage) );
		}
		elseif ( $format == "daily")
		{
			$ev = ( ( 7 * $pack_prices[2] + 4 * $pack_prices[1]) * pow( $win_percentage, 4 ) )
				+ ( ( 4 * $pack_prices[2] + 2 * $pack_prices[1]) * pow( $win_percentage, 3 ) * ( 1 - $win_percentage ) );
		}

		return $ev;
	}

	function calculate_entry_value ( $price_list, $entry, $tickets, $source )
	{
		$total = 0.00;
		if ( $source == "owned" )
		{
			$price_type = "buy";
		}
		elseif ( $source == "bot" )
		{
			$price_type = "sell";
		}
		elseif ( $source == "wotc" )
		{
			$price_type = "wotc";
		}

		foreach ( $entry as $pack )
		{
			$total += find_price ( $price_list, $pack, $price_type );
		}

		return $total + $tickets;
	}

	$price_list = get_db_prices ( );

?>