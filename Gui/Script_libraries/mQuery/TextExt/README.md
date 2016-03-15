Original script file: https://github.com/PRGfx/mQscripts

This library offers a bunch of handy functions extending the possibilities of the native TextLib library of ManiaScript. Note that some functions have been integrated in the standard TextLib by now.

Requirements
Several of these functions require the TextLib and MathLib included with these names!

File TextExt.Script.txt
Functions

Text TextExt_CharAt(Text string, Integer offset)
Returns the char at the given offset in a string. "" if out of bounds.

Text[] TextExt_Chars(Text string[, Boolean whitespaces])
Returns all single chars of the given string in an array. Optionally say whether or not whitespaces should be returned.

Text TextExt_TrimL(Text string)
Removes all whitespaces left of the given string.

Text TextExt_TrimR(Text string)
Removes all whitespaces right of the given string.

Text TextExt_Trim(Text string)
Removes all whitespaces left and right of the given string.

Boolean TextExt_Empty(Text string)
Returnes whether or not a string is empty, means if it is equal to "".

Text TextExt_Join(Text glue, Text[] strings)
Joins several strings of an array with a given string, for example

TextExt_Join(", ", ["One", "Two", "Three"]) // returns "One, Two, Three"

Text[] TextExt_Split(Text delimiter, Text string[, Integer length])
Basically identical to the native TextLib method, but allows an optional parameter determining in how many parts should be split into, for example

TextExt_Split(", ", "One, Two, Three", 2) // returns ["One", "Two, Three"]

Text TextExt_Repeat(Text string, Integer count[, Text separator])
Repeats the given string count times, optionally separated by the given separator

Text[] TextExt_Words(Text string[, Text separator])
Returns an array with all the words in the given string. You can optionally declare a different separator than " ".

Text TextExt_Uppercase(Text string)
Returns the given string all uppercase.

Text TextExt_Lowercase(Text string)
Returns the given string all lowercase.

Text TextExt_Capitalize(Text string)
Returns the given string with the first letter uppercase.

Text TextExt_Titleize(Text string)
Returns the given string capitalizing every word.

Integer TextExt_StrPos(Text heystack, Text needle)
Returns the offset of the first appearence of needle in the input heystack. Returns -1 if the needle cannot be found in the heystack.

Boolean TextExt_Contains(Text heystack, Text needle)
Returns whether or not the heystack contains the string needle.

Boolean TextExt_StartsWith(Text string, Text start)
Returns whether or not the string begins with the second parameter.

Boolean TextExt_EndsWith(Text string, Text end) Returns whether or not the string ends with the second parameter.

Text TextExt_Replace(Text search, Text replace, Text subject[, Integer occurences])
Replaces search with replace in the string subject occurences times, normally replaces every occurence.

Text TextExt_Replace(Text[] search, Text[] replace, Text subject)
Replaces every string in search with the word at same index in replace. Note, that search and replace have to be of the same length!

Text TextExt_Replace(Text[] search, Text replace, Text subject)
Replaces every string in search with replace.

Real TextExt_Levenshtein(Text word1, Text word2) Returns the levenshtein distance between the two words. The library uses cost of 1 for insert, replace and remove.
Text[] TextExt_Levenshtein(Text input, Text[] p)

Returns the input list p ordered by the respective levenshtein distances to the input.
Text TextExt_Reverse(Text string)
Returns the input text in reverse order.

Text TextExt_StripFormat(Text string)
Removes $o, $w, $n, $i, $s formattings from the string.

Text TextExt_StripColors(Text string)
Removes $[0-9a-f]{1,3} color formattings from the string.

Text TextExt_StripLinks(Text string)
Remvoes $(l|h|p) links from the string, including optional links in square brackets.

Text TextExt_StripTags(Text string)
Removes all of the before.
