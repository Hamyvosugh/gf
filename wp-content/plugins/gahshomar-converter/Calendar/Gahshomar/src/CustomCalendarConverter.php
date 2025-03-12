<?php

namespace Gahshomar;

class CustomCalendarConverter
{
    const CALENDARS = [
        'padeshahi' => 1180,
        'eilami' => 3821,
        'zoroastrian' => 2359,
        'madi' => 1321,
        'new_iran' => -1396
    ];

    public static function convertToJalaaliYear($year, $calendar)
    {
        if (!isset(self::CALENDARS[$calendar])) {
            throw new \Exception("Unsupported calendar: $calendar");
        }
        return $year - self::CALENDARS[$calendar];
    }

    public static function convertFromJalaaliYear($jy, $calendar)
    {
        if (!isset(self::CALENDARS[$calendar])) {
            throw new \Exception("Unsupported calendar: $calendar");
        }
        return $jy + self::CALENDARS[$calendar];
    }
}