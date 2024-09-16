<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DropBox
{
    public function __construct()
    {
        require_once APPPATH.'third_party/Dropbox/autoload.php';
    }
}