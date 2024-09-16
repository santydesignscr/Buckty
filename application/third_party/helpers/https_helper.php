<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function is_https_on()
{
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || $_SERVER[''];
}

function BucktyCheck($url_){
    $url = parse_url($url_);
    if($url['scheme'] == 'https'){
        use_ssl(TRUE);
    } else {
        use_ssl(FALSE);
    }
}

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
