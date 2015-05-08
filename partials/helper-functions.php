<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 5/7/15
 * Time: 2:04 PM
 */

function activedemand_getHTML($url, $timeout)
{
    $ch = curl_init($url); // initialize curl with given url
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);//force IP4
    $result = curl_exec($ch);
    if (curl_exec($ch) === false) {
        echo 'ActiveDEMAND Web Form error: ' . curl_error($ch);
    }

    curl_close($ch);


    return $result;
}

?>