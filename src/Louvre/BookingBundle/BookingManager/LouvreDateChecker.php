<?php

namespace Louvre\BookingBundle\BookingManager;

class LouvreDateChecker
{
	/**
	 * check if booking day is not a day off
	 *
	 */
	public function checkDayOff($date)
	{
		
		$month = (int) $date->format('m');
		$weekDay = (int) $date->format('w');
		$day = (int) $date->format('d'); 
		$dayOff = $weekDay === 0 || $weekDay === 2 || ($day === 1 && ($month === 5 || $month === 11)) || ($day === 25 && $month === 12)? true: false;

		return $dayOff;
	}

	/**
	 * check if date is not passed
	 *
	 */
	public function checkValidDate($date)
	{
		$today = new \Datetime('now');
		$day = (int) date_diff($today, $date)->format('%r%a');
		$hour = (int) $date->format('h');
		$invalidDate = $day < 0 || ($day === 0 && $hour >= 17)? true: false;

		return $invalidDate;
	}
}