<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends BUCKTY_Content
{

    /**
     * @var array
     */
    public $temp = array();

    /**
     * @var object
     */
    public $site;

    /**
     * @var
     */
    public $csrf_token;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        /**
         * Parent constructor
         */
        parent::__construct();
        $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        $this->load->model('BucktySettings');
        $this->load->model('BucktyContent');
        $this->BucktySettings->settings();
        $this->load->model('BucktySettings');
        $this->site = (object)$this->BucktySettings->LoadSettings();
        BucktyCheck($this->site->site_url);
    }

    /**
     * Show login page or dashboard based on user's login status
     */
    public function index()
    {
        $this->temp['main_content'] = 'content';

        if ($this->aauth->is_loggedin()):
            redirect($this->site->site_url . 'folders');
        else:
            $data['api'] = $this->BucktySettings->getApi();
            $data['pages'] = $this->BucktyContent->getPages();
            $this->load->view('login', $data);
        endif;
    }

    /**
     * 404 page function.
     */
    public function Error_404()
    {
        $data['pageTitle'] = '404 not found';
        $this->load->view('pages/404', $data);
    }

    /**
     * Load login again popup
     */
    public function loginAgain()
    {
        $this->load->view('popups/login');
    }

    /**
     * Show user settings page.
     */
    public function userSettings()
    {
        if ($this->is_loggedin()):
            $data['main_content'] = 'settings';
            $data['data_keys'] = $this->aauth->get_user_vars($this->logged_data()->id);
            $this->load->view('main', $data);
        else:
            redirect($this->site->site_url);
        endif;
    }

    /**
     * Get page method.
     */
    public function getPage()
    {
        $page = $this->uri->segment(2, 0);
        $data['content'] = $this->BucktyContent->getPage($page);
        if (!empty($data['content'])):
            $data['pageTitle'] = $data['content']->page_name;
            $data['pages'] = $this->BucktyContent->getPages();
            $this->load->view('pages/page', $data);
        else:
            $data['pageTitle'] = '404 not found';
            $this->load->view('pages/404', $data);
        endif;
    }
}