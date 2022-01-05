<?php


/**
 * @deprecated moved to ArrayUtils
 * this function is used to overide a value in an array and returns the string code to write
 * @params : $array_name - a String containing the name of an array.
 * @params : $value_name - a String containing the name of a variable in the array.
 * @params : $value      - a String containing the associated value with $value_name.
 * 
 * @returns: String. Example - override_value_to_string($name, 'b', 1) = '$name['b'] = 1;'
 */
function override_value_to_string($array_name, $value_name, $value)
{
    $string = "\${$array_name}[" . var_export($value_name, true) . "] = ";
    $string .= var_export($value, true);
    return $string . ";";
}


/**
 * @deprecated moved to ArrayUtils
 * Given an array and key names, return a string in the form of $array_name[$key_name[0]][$key_name[1]]... = $value recursively.
 * @params : $key_names - array of keys
 * 			 $array_name- name of the array
 * 			 $value -value of the array
 * 			 $eval - evals the generated string if true, note that the array name must be in the global space!
 * @return : example - string $array_name['a']['b']['c'][.] = 'hello'
 */
function override_value_to_string_recursive2($array_name, $value_name, $value, $save_empty = true)
{
    $quoted_vname = var_export($value_name, true);
    if (is_array($value)) {
        $str = '';
        $newArrayName = $array_name . "[$quoted_vname]";
        foreach ($value as $key => $val) {
            $str .= override_value_to_string_recursive2($newArrayName, $key, $val, $save_empty);
        }
        return $str;
    } else {
        if (!$save_empty && empty($value)) {
            return;
        } else {
            return "\$$array_name" . "[$quoted_vname] = " . var_export($value, true) . ";\n";
        }
    }
}

/**
 * @deprecated moved to ArrayUtils
 * This function returns an array of all the key=>value pairs in $array1
 * that are wither not present, or different in $array2.
 * If a key exists in $array2 but not $array1, it will not be reported.
 * Values which are arrays are traced further and reported only if thier is a difference
 * in one or more of thier children.
 *
 * @param array $array1 , the array which contains all the key=>values you wish to check againts
 * @param array $array2 , the array which
 * @param array $allowEmpty , will return the value if it is empty in $array1 and not in $array2,
 * otherwise empty values in $array1 are ignored.
 * @return array containing the differences between the two arrays
 */
function deepArrayDiff($array1, $array2, $allowEmpty = false)
{
    $diff = [];
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if ((!isset($array2[$key]) || !is_array($array2[$key])) && (isset($value) || $allowEmpty)) {
                $diff[$key] = $value;
            } else {
                $value = deepArrayDiff($array1[$key], $array2[$key], $allowEmpty);
                if (!empty($value) || $allowEmpty)
                    $diff[$key] = $value;
            }
        } else if ((!isset($array2[$key]) || $value != $array2[$key]) && (isset($value) || $allowEmpty)) {
            $diff[$key] = $value;
        }
    }
    return $diff;
}

/**
 * @deprecated moved to ArrayUtils
 * Recursivly set a value in an array, creating sub arrays as necessary
 *
 * @param unknown_type $array
 * @param unknown_type $key
 */
function setDeepArrayValue(&$array, $key, $value)
{
    //if _ is at position zero, that is invalid.
    if (strrpos($key, "_")) {
        list ($key, $remkey) = explode('_', $key, 2);
        if (!isset($array[$key]) || !is_array($array[$key])) {
            $array[$key] = [];
        }
        setDeepArrayValue($array[$key], $remkey, $value);
    } else {
        $array[$key] = $value;
    }
}


/**
 * @deprecated moved to ArrayUtils
 * Search an array for a given value ignorning case sensitivity
 *
 * @param unknown_type $key
 * @param unknown_type $haystack
 */
function array_search_insensitive($key, $haystack)
{
    if (!is_array($haystack))
        return false;

    $found = false;
    foreach ($haystack as $k => $v) {
        if (strtolower($v) == strtolower($key)) {
            $found = true;
            break;
        }
    }

    return $found;
}

