<?php

/**
* Class and Function List:
* Function list:
* - array_sort_by_fields()
* - smarty_modifier_sortby()
* - smarty_outputfilter_strip()
*/
/*
* sorts an array of named arrays by the supplied fields
*   code by dholmes at jccc d0t net
*   taken from http://au.php.net/function.uasort
* modified by cablehead, messju and pscs at http://www.phpinsider.com/smarty-forum
*/

// TODO: Sort out license for this file

function array_sort_by_fields(&$data, $sortby)
{
    static $sort_funcs = array();

    if (empty($sort_funcs[$sortby])) {
        $code = "\$c=0;";

        foreach (explode(',', $sortby) as $key) {
            $d = '1';

            if (substr($key, 0, 1) == '-') {
                $d = '-1';
                $key = substr($key, 1);
            }

            if (substr($key, 0, 1) == '#') {
                $key = substr($key, 1);
                $code.= "if ( \$a['$key'] > \$b['$key']) return $d * 1;\n";
                $code.= "if ( \$a['$key'] < \$b['$key']) return $d * -1;\n";
            } else {
                $code.= "if ( (\$c = strcasecmp(\$a['$key'],\$b['$key'])) != 0 ) return $d * \$c;\n";
            }
        }
        $code.= 'return $c;';
        $sort_func = $sort_funcs[$sortby] = create_function('$a, $b', $code);
    } else {
        $sort_func = $sort_funcs[$sortby];
    }
    uasort($data, $sort_func);
}

//
// Modifier: sortby - allows arrays of named arrays to be sorted by a given field
//


function smarty_modifier_sortby($arrData, $sortfields)
{
    array_sort_by_fields($arrData, $sortfields);

    return $arrData;
}

function smarty_outputfilter_strip($output, &$smarty)
{

    return trim(preg_replace(array(
        "`>\s+`",
        "`\s+<`"
    ), array(
        ">",
        "<"
    ), $output));
}
