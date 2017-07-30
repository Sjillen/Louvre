<?php

namespace Louvre\BookingBundle\BookingManager;

class LouvrePriceChecker
{
	/**
	 * Set price according to ticket's age and discount attributes
	 *
	 */

	public function checkPrice($birthdate, $type, $discount)
	{
		$today = new \Datetime('now');
		$diff = date_diff($birthdate, $today);
		$age = (int) $diff->format("%a");
		$age /= 365.25;

		$price = 16;
		if ($age < 4)
		{
			$price = 0;
		}
		elseif ($age >= 4 && $age <= 12 ) 
		{
			$price = 8;
		}
		elseif ($age >= 60) 
		{
			$price = 12;
		}
		elseif($discount)
		{
			$price = 10;
		}
		else
		{
			$price = 16;
		}

		$price = $type==="Demi-journée"? $price/2 : $price;

		return $price;
	}
}