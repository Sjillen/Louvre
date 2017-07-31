<?php

namespace Louvre\BookingBundle\BookingManager;

class LouvreTypeChecker
{
	public function checkType($date, $type)
	{
		$today = new \Datetime('now');
		$day = (int) date_diff($today, $date)->format('&a');
		$hour = (int) $today->format('h');
			
		$errorType =  $day === 0 && $hour >= 14 && $type === "Journée"? true: false;

		return $errorType;
	}
}