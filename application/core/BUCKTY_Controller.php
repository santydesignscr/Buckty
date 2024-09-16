<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BUCKTY_Controller extends CI_Controller{
    
    public $site;
    
    public $data = array();

    public $trans;
    public function __construct(){
        parent::__construct();
        if($this->aauth->is_loggedin()):
            $this->data['user'] = $this->aauth->get_user();
            $this->data['user']->is_admin = $this->aauth->is_admin();
            $this->data['user']->is_logged = $this->aauth->is_loggedin();
        else:
            $this->data['user'] = (object) array();
            $this->data['user']->is_logged = false;
        endif;
        $this->load->model('BucktySettings');
        $this->data['site'] = (object) $this->BucktySettings->LoadSettings();
        $this->data['trans'] = (object) $this->BucktySettings->getTrans();
        $this->trans = (object) $this->BucktySettings->getTrans();
        $this->site = $this->data['site'];
        $this->load->vars($this->data);
        $config['protocol']    = 'smtp';
        $config['smtp_host']    = $this->site->smtp_host;
        $config['smtp_port']    = $this->site->smtp_port;
        $config['smtp_timeout'] = '7';
        $config['smtp_user']    = $this->site->smtp_user;
        $config['smtp_pass']    = $this->site->smtp_password;
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";
        $config['mailtype'] = 'html';
        $config['validation'] = TRUE;
        $this->email->initialize($config);
    }

    public function batch_email($recipients, $subject, $message)
    {
        $this->email->clear(TRUE);
        $this->email->from($this->site->smtp_user, $this->site->site_name);
        $this->email->to($this->site->smtp_user);
        $this->email->bcc($recipients);
        $this->email->subject($subject);
        $this->email->message($message);

        $this->email->send();

        return TRUE;

    }
}