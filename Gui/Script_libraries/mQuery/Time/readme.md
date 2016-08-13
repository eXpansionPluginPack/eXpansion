Script originally: https://github.com/PRGfx/mQscripts/blob/master/lib/Time.Script.txt
Wiki-page originally:  https://github.com/PRGfx/mQscripts/wiki/Time

This library offers some time-related functions.

Requirements
These functions require the TextLib with exact this name.

File
Time.Script.txt
Functions

Void Time_SetTimezone(Text _TimezoneName)
Sets the current timezone to _TimezoneName and uses this zone for all further function calls. You can use any name from the list below.

Integer Time_GetTimezoneOffset([Text _TimezoneName])
Returns the Offset for the given timezone as (hours * 100 + minutes). You can calculate the hours as hours = offset / 100 and minutes as minutes = offset % 1000 % 100.

Text Time_GetTimezoneName([Text _TimezoneName])
Returns a string like Coordinated Universal Time according to the current timezone.

Text Time_GetTimezoneLocation([Text _TimezoneName])
Returns a string like Europe, Africa according to the current timezone.

Text Time_GetTimezoneAbbreviation([Text _TimezoneName])
Returns a string like GMT according to the current timezone.

Boolean Time_IsLeapYear(Integer _Year)
Returns whether the given year is a leap year.

Integer Time_DayOfWeek(Integer _Year, Integer _Month, Integer _Day)
Returns the Day within the week from 0 (Sunday) to 6 (Saturday).

Integer Time_WeekOfYear(Integer _Year, Integer _Month, Integer _Day)
Returns the Week within the year.

Integer Time_Timestamp(Integer _Year, Integer _Month, Integer _Day, Integer _Hour, Integer _Minute, Integer _Second[, Integer _UTCOffset])
Returns the Unix Timestamp in seconds for the given date.

Integer[Text] Time_FromText(Text date)
Parses the CurrentLocalDateText format into the fields year, month, day, hour, minute, second.

Integer Time_Timestamp([Integer _UTCOffset])
Returns the Unix Timestamp in seconds from the CurrentLocalDateText field, thus you might want to consider giving an offset to the UTC time.

Integer[Text] Time_FromTimestamp(Integer _Timestamp)
Translates the given _Timestamp into the fields year, month, day, hour, minute, second.

Text Time_Date([Integer[Text] _Parts, Text _Format])
Formats a date according to _Format. Is no _Parts array given, it uses the current Time, is no _Format given, it uses the CurrentLocalDateText format.

Text Time_Date(Integer _Timestamp[, Text _Format])
Text Time_Date(Integer _Year, Integer _Month, Integer _Day, Integer _Hour, Integer _Minute, Integer _Second, Integer _UTCOffset[, Text _Format])
Text Time_Date(Integer _Year, Integer _Month, Integer _Day, Integer _Hour, Integer _Minute, Integer _Second[, Text _Format])
Formats the given time with the given Format.
TimeFormats

Time comes with predefined date formats:

    TIME_DATE_ATOM
    "Y-d-m\TH:i:sP" (example: 2005-08-15T15:52:01+00:00)
    TIME_DATE_COOKIE
    "l, d-M-Y H:i:s T" (example: Monday, 15-Aug-2005 15:52:01 UTC)
    TIME_DATE_ISO8601
    "Y-d-m\TH:i:sO" (example: 2005-08-15T15:52:01+0000)
    TIME_DATE_RFC822
    "D, d M y H:i:s O" (example: Mon, 15 Aug 05 15:52:01 +0000)
    TIME_DATE_RFC850
    "l, d-M-y H:i:s T" (example: Monday, 15-Aug-05 15:52:01 UTC)
    TIME_DATE_RFC1036
    "D, d M y H:i:s O" (example: Mon, 15 Aug 05 15:52:01 +0000)
    TIME_DATE_RFC1123
    "D, d M Y H:i:s O" (example: Mon, 15 Aug 2005 15:52:01 +0000)
    TIME_DATE_RFC2822
    "D, d M Y H:i:s O" (example: Mon, 15 Aug 2005 15:52:01 +0000)
    TIME_DATE_RFC3339
    "Y-d-m\TH:i:sP" (example: 2005-08-15T15:52:01+00:00)
    TIME_DATE_RSS
    "D, d M Y H:i:s O" (example: Mon, 15 Aug 2005 15:52:01 +0000)
    TIME_DATE_W3C
    "Y-m-d\TH:i:sP" (example: 2005-08-15T15:52:01+00:00)
    TIME_DATE_MANIAPLANET
    "Y/m/d H:i:s" (example: 2014/11/14 10:35:20)

See http://php.net/manual/en/function.date.php for explanation of the identifiers. 