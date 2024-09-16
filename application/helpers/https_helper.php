<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Is current url is https if yes ture else false.
 * @return bool
 */
function is_https_on()
{
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || $_SERVER[''];
}

/**
 * Check if setting's url is https or not.
 * @param $url_
 */
function BucktyCheck($url_){
    $url = parse_url($url_);
    if($url['scheme'] == 'https'){
        use_ssl(TRUE);
    } else {
        use_ssl(FALSE);
    }
}


/**
 * Check if system requested https if yes then use https else go to normal url.
 * @param bool $turn_on
 */
function use_ssl($turn_on = TRUE)
{
    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

    if ( $turn_on )
    {
        if ( ! is_https_on() && $_SERVER['HTTP_HOST'] != 'localhost')
        {
            redirect('https://' . $url, 'location', 301 );
            exit;
        }
    }
    else
    {
        if ( is_https_on() )
        {
            redirect('http://' . $url, 'location', 301 );
            exit;
        }
    }
}
