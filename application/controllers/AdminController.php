<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * Class AdminController
 */
class AdminController extends BUCKTY_User {

    /**
     * @var
     */
    public $user;

    /**
     * @var
     */
    public $loggedin;

    /**
     * @var
     */
    public $is_admin;

    /**
     * Embeding parent constructor.
     * AdminController constructor.
     */
    function __construct(){
       parent::__construct();
        $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        $this->load->model('BucktySettings');
        $this->load->model('BucktyContent');
        $this->BucktySettings->settings();
         if($this->aauth->is_loggedin() && $this->aauth->is_admin()){
            $data['user'] = $this->aauth->get_user();
            $this->load->model('BucktySettings');
            $this->site = (object) $this->BucktySettings->LoadSettings();
            $this->load->vars($data);
         } else {
             redirect('/folders');
         }
    }

    /**
     *  Admin dashboard index load.
     */
    public function index(){
        $data['users'] = $this->aauth->users_data();
        $data['files_count'] = count((array) $this->BucktyContent->GetContentAdmin('files'));
        $data['folders_count'] = count((array) $this->BucktyContent->GetContentAdmin('folder'));
        $data['content'] = 'dashboard';
        $data['disk_size'] = $this->UsedSpace(null,1);
        $this->load->view('admin/admin',$data);
    }

    /**
     * Get users list for administrator
     * @param bool $group_par
     * @param bool $limit
     * @param bool $offset
     * @param bool $include_banneds
     */
    public function getUsers($group_par = FALSE,$limit = FALSE,$offset = FALSE,$include_banneds = FALSE){
        $this->load->library('pagination');
        $data['base_url']  = '/admin/users';
        $data['total_rows'] =  $this->aauth->users_data($group_par,$limit,$offset,true)->count;
        $data['per_page']   = 10;
        $data['num_links']  = 4;
        $offset = $this->uri->segment(3,FALSE) == FALSE ? 0 : ($this->uri->segment(3,FALSE) - 1 ) * $data['per_page'];
        $data['use_page_numbers'] = TRUE;
        $data['full_tag_open'] = '<ul class="pagination">';
        $data['full_tag_close'] = '</ul>';
        $data['first_link'] = 'First';
        $data['first_tag_open'] = '<li>';
        $data['first_tag_close'] = '</li>';
        $data['last_link'] = 'Last';
        $data['last_tag_open'] = '<li>';
        $data['last_tag_close'] = '</li>';
        $data['next_link'] = 'Next';
        $data['next_tag_open'] = '<li>';
        $data['next_tag_close'] = '</li>';
        $data['prev_link'] = 'Last';
        $data['prev_tag_open'] = '<li>';
        $data['prev_tag_close'] = '</li>';
        $data['cur_tag_open'] = '<li><a href="javascript:void(0);"><strong>';
        $data['cur_tag_close'] = '</strong></a></li>';
        $data['num_tag_open'] = '<li>';
        $data['num_tag_close'] = '</li>';
        $data['users'] = $this->aauth->users_data($group_par,$data['per_page'],$offset,true);
        $data['records']    = $this->aauth->users_data($group_par,$data['per_page'],$offset,true);
        $this->pagination->initialize($data);
        $data['content'] = 'userslist';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Language page view for admin
     */
    public function languageManager(){
        $data = array();
        $data['langs'] = $this->BucktySettings->getLangs();
        $data['keys']  = $this->BucktySettings->getKeys();
        $data['content'] = 'language';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Settings page view for admin
     */
    public function settings(){
        $data['content'] = 'settings';
        $data['social']  = $this->BucktySettings->getApi();
        $this->load->view('admin/admin',$data);
    }

    /**
     * Api settings for admin
     */
    public function socialSettings(){
        $data = array();
        $data['content'] = 'socialsettings';
        $data['social']  = $this->BucktySettings->getApi();
        $this->load->view('admin/admin',$data);
    }

    /**
     * Mail settings views for admin
     */
    public function smtpSettings(){
        $data = array();
        $data['content'] = 'smtpsettings';
        $data['social']  = $this->BucktySettings->getApi();
        $this->load->view('admin/admin',$data);
    }

    /**
     * Save site settings
     */
    public function saveSettings(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                            'message' => 'Invalid csrf Token',
                            'error_code' => 1
                        );
            echo json_encode($message);
            exit();
        }
        /**
         * Validate settings.
         */
       $this->load->library('form_validation');
        $this->form_validation->set_rules('site_name','Site Name','required|trim|xss_clean');
        $this->form_validation->set_rules('site_url','Site Url','required|trim|valid_url');
        $this->form_validation->set_rules('admin_email','Admin Email','required|trim|valid_email');
        if($this->form_validation->run() == false){
            $message = array('message'=>strip_tags(validation_errors()),'error_code'=>1);
            echo json_encode($message);
        } else {
            $data['site_name'] = $this->input->post('site_name',TRUE);
            $data['site_url']  = $this->input->post('site_url',TRUE);
            $data['site_description'] = $this->input->post('site_description',true);
            $data['site_keywords']    = $this->input->post('site_keywords',true);
            $data['admin_email'] = $this->input->post('admin_email',TRUE);
            $data['allowed_extensions'] = serialize(explode(',',$this->input->post('allowed_extensions')));
            $data['blacklist_extensions'] = serialize(explode(',',$this->input->post('blacklist_extensions')));
            $data['upload_limit'] = $this->input->post('upload_limit',true);
            $data['max_file_size'] = $this->input->post('max_file_size',true);
            $data['email_activation'] = $this->input->post('email_activation',true);
            $data['register_active']= $this->input->post('register_active',true);
            $data['site_home_tagline']  = $this->input->post('site_home_tagline',true);
            $data['site_home_description'] = $this->input->post('site_home_description',true);
            $data['ad_780']                = $this->input->post('ad_780',FALSE);
            $data['ad_320']                = $this->input->post('ad_320',FALSE);
            if($this->BucktySettings->updateConfig($data)){
                $message = array('message'=>'Settings Were Updated SuccessFully','error_code'=>0);
                echo json_encode($message);
            } else {
                $message = array('message'=>'Something Went Wrong','error_code'=>1);
                echo json_encode($message);
            }
        }
    }

    /**
     * Update mail settings.
     */
    public function updateSmtpSettings(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('smtp_host','Smtp host','required|trim');
        $this->form_validation->set_rules('smtp_port','Smtp port','required|trim');
        $this->form_validation->set_rules('smtp_user','Smtp user','required|trim|valid_email');
        $this->form_validation->set_rules('smtp_password','Smtp password','required');
        if($this->form_validation->run() == false){
            $message = array('message'=>strip_tags(validation_errors()),'error_code'=>1);
            echo json_encode($message);
        } else {
            $data['smtp_host'] = $this->input->post('smtp_host',TRUE);
            $data['smtp_port']  = $this->input->post('smtp_port',TRUE);
            $data['smtp_user'] = $this->input->post('smtp_user',true);
            $data['smtp_password']    = $this->input->post('smtp_password',true);
            if($this->BucktySettings->updateConfig($data)){
                $message = array('message'=>'Settings Were Updated SuccessFully','error_code'=>0);
                echo json_encode($message);
            } else {
                $message = array('message'=>'Something Went Wrong','error_code'=>1);
                echo json_encode($message);
            }
        }
    }


    /**
     * Update / save api settings.
     */
    public function saveApiSettings(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                            'message' => 'Invalid csrf Token',
                            'error_code' => 1
                        );
            echo json_encode($message);
            exit();
        }
       $this->load->library('form_validation');
        $this->form_validation->set_rules('social','Social Settings','trim');
        if($this->form_validation->run() == false){
            $message = array('message'=>strip_tags(validation_errors()),'error_code'=>1);
            echo json_encode($message);
        } else {
            $data['facebook'] = serialize($this->input->post('soc',true)['facebook']);
            $data['google'] = serialize($this->input->post('soc',true)['google']);
            $data['twitter'] = serialize($this->input->post('soc',true)['twitter']);
            $data['dropbox'] = serialize($this->input->post('soc',true)['dropbox']);
            $data['disqus']  = serialize($this->input->post('soc',true)['disqus']);
            $data['pexels']  = serialize($this->input->post('soc',true)['pexels']);
            if($this->BucktySettings->saveApi($data)){
                $message = array('message'=>'Settings were saved','error_code'=>0);
                echo json_encode($message); 
            } else {
                $message = array('message'=>'Something went wrong','error_code'=>1);
                echo json_encode($message);
            }
        }
    }

    /**
     * Create new language.
     */
    public function saveLanguage(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                            'message' => 'Invalid csrf Token',
                            'error_code' => 1
                        );
            echo json_encode($message);
            exit();
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('langName','Language Name','required|trim');
        $this->form_validation->set_rules('langSlug','Langauge Slug','required|trim');
        if($this->form_validation->run() == false){
            $message = array('message'=>strip_tags(validation_errors()),'error_code'=>1);
            echo json_encode($message);
        } else {
            $data['langName'] = $this->input->post('langName',TRUE);
            $data['langSlug']  = $this->input->post('langSlug',TRUE);
        if($this->BucktySettings->checkLang($data)){
            if($this->BucktySettings->addLang($data)){
                $message = array('message'=>'Language Was Added Successfully','error_code'=>0);
                echo json_encode($message);
            } else {
                $message = array('message'=>'Something Went Wrong','error_code'=>1);
                echo json_encode($message);
            }
        } else {
            $message = array('message'=>'Language With Slug <b>'.$data['langName'].'</b> is Already Present In Database','error_code'=>1);
            echo json_encode($message);
        }
        }
    }

    /**
     * Get language translations
     */
    public function getFieldsLang(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $id = $this->input->get('id');
        $fields = $this->BucktySettings->getLangFields($id);
       if(!empty($fields)){
            $fields = array('data'=>$fields,'empty'=>'no');
            echo json_encode($fields);
        } else {
            $fields = array('data'=>$fields,'empty'=>'yes');
            echo json_encode($fields);
        }
    }

    /**
     * Get user and it's detials.
     */
    public function getUser(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $user_id = $this->input->get('user_id');
        $data['data'] = $this->aauth->get_user($user_id);
        $data['groups'] = $this->aauth->list_groups();
        if(!empty($data)){
            $this->load->view('admin/blocks/edituser',$data);
        } else {

        }
    }

    /**
     *  Add new keys to language.
     */
    public function addlangKeys(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $keys = explode(',',$this->input->post('keyNames'));
        if($this->BucktySettings->ManageKeys($keys)){
            $keyin = array();
            foreach($keys as $key){
                $keyname = str_replace(' ','_',$key);
                $keyin[] =  '<div class="margin pull-left key"><span class="label label-primary">'.$key.'</span><i class="fa fa-close" onclick="Buckty.key.remove(\''.$keyname.'\')"></i></div>';
            }
            $data = array('data'=>$keyin,'error_code'=>0);
            echo json_encode($data);
        }
    }

    /**
     * Delete language from database
     */
    public function delLang(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $langId = $this->input->get('langId');
        if($this->BucktySettings->delLanguage($langId)){
            $message = array('message'=>'Language was removed from database','error_code'=>0);
            echo json_encode($message);
        } 
    }


    /**
     * Set language as default by id.
     */
    public function setOfficialLang(){
        $id = $this->input->get('L');
        if($this->BucktySettings->setLang($id)){
           $message = array('message'=>'Language was changed','error_code'=>0);
            echo json_encode($message); 
        } else {
            $message = array('message'=>'There was an error while setting up a language','error_code'=>1);
            echo json_encode($message);
        }
    }

    /**
     * Get folders list for admin
     */
    public function getFolders(){
        $this->load->library('pagination');
        $data['base_url']  = base_url().'/admin/folders';
        $data['total_rows'] =  count((array)$this->BucktyContent->GetContentAdmin('folder'));
        $data['per_page']   = 10;
        $data['num_links']  = 4;
        $offset = $this->uri->segment(3,FALSE) == FALSE ? 0 : ($this->uri->segment(3,FALSE) - 1 ) * $data['per_page'];
        $data['use_page_numbers'] = TRUE;
        $data['full_tag_open'] = '<ul class="pagination">';
        $data['full_tag_close'] = '</ul>';
        $data['first_link'] = 'First';
        $data['first_tag_open'] = '<li>';
        $data['first_tag_close'] = '</li>';
        $data['last_link'] = 'Last';
        $data['last_tag_open'] = '<li>';
        $data['last_tag_close'] = '</li>';
        $data['next_link'] = 'Next';
        $data['next_tag_open'] = '<li>';
        $data['next_tag_close'] = '</li>';
        $data['prev_link'] = 'Last';
        $data['prev_tag_open'] = '<li>';
        $data['prev_tag_close'] = '</li>';
        $data['cur_tag_open'] = '<li><a href="javascript:void(0);"><strong>';
        $data['cur_tag_close'] = '</strong></a></li>';
        $data['num_tag_open'] = '<li>';
        $data['num_tag_close'] = '</li>';
        $data['folders'] = $this->BucktyContent->GetContentAdmin('folder',$offset,$data['per_page']);
        $data['records']    = $this->BucktyContent->GetContentAdmin('folder',$offset,$data['per_page']);
        $this->pagination->initialize($data);
        $data['content'] = 'folders';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Get files list for admin.
     */
    public function getFiles(){
        $this->load->library('pagination');
        $data['base_url']  = base_url().'/admin/files';
        $data['total_rows'] =  count((array)$this->BucktyContent->GetContentAdmin('files'));
        $data['per_page']   = 10;
        $data['num_links']  = 4;
        $offset = $this->uri->segment(3,FALSE) == FALSE ? 0 : ($this->uri->segment(3,FALSE) - 1 ) * $data['per_page'];
        $data['use_page_numbers'] = TRUE;
        $data['full_tag_open'] = '<ul class="pagination">';
        $data['full_tag_close'] = '</ul>';
        $data['first_link'] = 'First';
        $data['first_tag_open'] = '<li>';
        $data['first_tag_close'] = '</li>';
        $data['last_link'] = 'Last';
        $data['last_tag_open'] = '<li>';
        $data['last_tag_close'] = '</li>';
        $data['next_link'] = 'Next';
        $data['next_tag_open'] = '<li>';
        $data['next_tag_close'] = '</li>';
        $data['prev_link'] = 'Last';
        $data['prev_tag_open'] = '<li>';
        $data['prev_tag_close'] = '</li>';
        $data['cur_tag_open'] = '<li><a href="javascript:void(0);"><strong>';
        $data['cur_tag_close'] = '</strong></a></li>';
        $data['num_tag_open'] = '<li>';
        $data['num_tag_close'] = '</li>';
        $data['files'] = $this->BucktyContent->GetContentAdmin('files',$offset,$data['per_page']);
        $data['records']    = $this->BucktyContent->GetContentAdmin('files',$offset,$data['per_page']);
        $this->pagination->initialize($data);
        $data['content'] = 'files';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Remove user from database
     */
    public function removeUsers(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if($this->aauth->is_admin()):
        $users = $this->input->post('users');
        foreach($users as $user){
            $this->aauth->delete_user($user);
            $this->BucktyContent->UnlinkFiles($user,'file_author');
            $this->BucktyContent->RemoveFolders($user,'folder_author');
        }
            $message = array('message'=>'Users were removed','error_code'=>0);
            echo json_encode($message);
        else:
            $message = array('message'=>'Not a admin','error_code'=>1);
            echo json_encode($message);
         endif;
    }


    /**
     * Save translation  for speicific language
     */
    public function saveTranslation(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $data = $this->input->post('fields_value');
        $keys = $this->input->post('fields_lang');
        $tran = array();
        foreach($data as $key => $t){
            $lang = explode('_',$key);
            $tran[$keys[$key]] = array('lang'=>$lang[1],'key'=>$keys[$key],'value'=>$t);
        }
        if($this->BucktySettings->saveTrans($tran)){
            $message = array('message'=>'Translation Was saved','error_code'=>0);
            echo json_encode($message);
        }
    }

    /**
     * Get user 's used space.
     * @param null $uid
     * @param int $term
     * @return string
     */
    private function UsedSpace($uid = null,$term = 1){
        $u_hash = $this->aauth->get_user_var('user_hash', $uid);
        $size = $this->getDirectorySize(FCPATH.'application/views/uploads/content')['size'];
        if($term == 1){
            return $this->sizeFormat($size);
        } elseif($term ==0){
            return $size;
        }
    }

    /**
     * Get total size of a directory.
     * @param $path
     * @return mixed
     */
    private function getDirectorySize($path)
    {
        $totalsize = 0;
        $totalcount = 0;
        $dircount = 0;
        $today    = date('Ym');
        if (!is_dir($path)):
            mkdir($path, 0777, true);
        endif;
        if ($handle = opendir ($path))
        {
            while (false !== ($file = readdir($handle)))
            {
                $nextpath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link ($nextpath))
                {
                    if (is_dir ($nextpath))
                    {
                        $dircount++;
                        $result = $this->getDirectorySize($nextpath);
                        $totalsize += $result['size'];
                        $totalcount += $result['count'];
                        $dircount += $result['dircount'];
                    }
                    elseif (is_file ($nextpath))
                    {
                        $totalsize += filesize ($nextpath);
                        $totalcount++;
                    }
                }
            }
        }
        closedir ($handle);
        $total['size'] = $totalsize;
        $total['count'] = $totalcount;
        $total['dircount'] = $dircount;
        return $total;
    }

    /**
     * Convert normal bytes into a readable size.
     * @param $bytes
     * @return string
     */
    private function sizeFormat($bytes){
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;

        if (($bytes >= 0) && ($bytes < $kb)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            return ceil($bytes / $kb) . ' KB';

        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            return ceil($bytes / $mb) . ' MB';

        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            return ceil($bytes / $gb) . ' GB';

        } elseif ($bytes >= $tb) {
            return ceil($bytes / $tb) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }


    /**
     * Search for users by specific keyword
     *
     */
    public function searchUsers(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $key = $this->input->get('user_search');
        $data['users'] = $this->aauth->users_data(NULL,NULL,NULL,true,$key);
        $this->load->view('admin/blocks/searchuser',$data);
    }

    /**
     * Search for items files/ folders
     */
    public function searchItems(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $type = $this->input->get('type');
        if($type == 'folder') {
            $key = $this->input->get('folder_search');
            $data['folders'] = $this->BucktyContent->GetContentAdmin('folder', NULL, NULL, $key);
            $this->load->view('admin/blocks/searchfolder', $data);
        } else if($type == 'file'){
            $key = $this->input->get('file_search');
            $data['files'] = $this->BucktyContent->GetContentAdmin('files', NULL, NULL, $key);
            $this->load->view('admin/blocks/searchfile', $data);
        }
    }

    /**
     * Create page view fro admin.
     */
    public function addPage(){
        $data['content'] = 'addpage';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Create page add it to database
     */
    public function publishPage(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $this->load->library('form_validation');

        $this->form_validation->set_rules('pageTitle', 'Page title', 'required');
        $this->form_validation->set_rules('pageSlug', 'Page Slug', 'required');
        $this->form_validation->set_rules('pageBody', 'Page Content', 'required');

        if ($this->form_validation->run()):
            $data['pageTitle']  = $this->input->post('pageTitle');
            $data['pageSlug']   = $this->input->post('pageSlug');
            $data['pageBody']   = $this->input->post('pageBody');
            $data['pageStatus'] = $this->input->post('pageStatus');
            $data['inFooter']   = $this->input->post('inFooter');
            $data['inSitemap']  = $this->input->post('inSitemap');

            if($this->BucktyContent->addPage($data))
                $message = array(
                    'message' => 'Page was saved',
                    'error_code' => 0
                );
                echo json_encode($message);

            else:

            $message = array(
                'message' => 'Some of the fields are invalid',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;

    }

    /**
     * Update page contents.
     */
    public function savePage(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $this->load->library('form_validation');

        $this->form_validation->set_rules('pageTitle', 'Page title', 'required');
        $this->form_validation->set_rules('pageSlug', 'Page Slug', 'required');
        $this->form_validation->set_rules('pageBody', 'Page Content', 'required');

        if ($this->form_validation->run()):
        $data['pageID']     = $this->input->post('page_id');
        $data['pageTitle']  = $this->input->post('pageTitle');
        $data['pageSlug']   = $this->input->post('pageSlug');
        $data['pageBody']   = $this->input->post('pageBody');
        $data['pageStatus'] = $this->input->post('pageStatus');
        $data['inFooter']   = $this->input->post('inFooter');
        $data['inSitemap']  = $this->input->post('inSitemap');
            if($this->BucktyContent->savePage($data))
                $message = array(
                    'message' => 'Page was saved',
                    'error_code' => 0
                );
                echo json_encode($message);

        else:
            $message = array(
                'message' => 'Some of the fields are invalid',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
    }

    /**
     * List all the pages list
     */
    public function allPages(){
        $this->load->library('pagination');
        $data['base_url']  = base_url(). '/admin/files';
        $data['total_rows'] =  count((array)$this->BucktyContent->getPages(1));
        $data['per_page']   = 10;
        $data['num_links']  = 4;
        $offset = $this->uri->segment(3,FALSE) == FALSE ? 0 : ($this->uri->segment(3,FALSE) - 1 ) * $data['per_page'];
        $data['use_page_numbers'] = TRUE;
        $data['full_tag_open'] = '<ul class="pagination">';
        $data['full_tag_close'] = '</ul>';
        $data['first_link'] = 'First';
        $data['first_tag_open'] = '<li>';
        $data['first_tag_close'] = '</li>';
        $data['last_link'] = 'Last';
        $data['last_tag_open'] = '<li>';
        $data['last_tag_close'] = '</li>';
        $data['next_link'] = 'Next';
        $data['next_tag_open'] = '<li>';
        $data['next_tag_close'] = '</li>';
        $data['prev_link'] = 'Last';
        $data['prev_tag_open'] = '<li>';
        $data['prev_tag_close'] = '</li>';
        $data['cur_tag_open'] = '<li><a href="javascript:void(0);"><strong>';
        $data['cur_tag_close'] = '</strong></a></li>';
        $data['num_tag_open'] = '<li>';
        $data['num_tag_close'] = '</li>';
        $data['pages'] = $this->BucktyContent->getPages(1,$offset,$data['per_page']);
        $data['records']    = $this->BucktyContent->getPages(1,$offset,$data['per_page']);
        $this->pagination->initialize($data);
        $data['content'] = 'listpages';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Edit page view for admin
     */
    public function editPage(){
        $pageid = $this->uri->segment(4,0);
        $data['page'] = $this->BucktyContent->getPage($pageid,'id');
        $data['content'] = 'edit_page';
        $this->load->view('admin/admin',$data);
    }

    /**
     * Remove key from keys.
     */
    public function removeKey(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if(!$this->input->is_ajax_request()){
            redirec(base_url('admin/languages'));
            exit();
        }
        $key = $this->input->post('key');
        $key = str_replace('_',' ',$key);
        $res = $this->BucktySettings->removeKey($key);
        if($res) {
            $Keys = $this->BucktySettings->getKeys();
            $data['keys'] = $Keys;
            $this->load->view('admin/blocks/langkeys',$data);
        }
    }

    /**
     * Remove page from database
     */
    public function removePage(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $pageid = $this->input->post('id');
        $res = $this->BucktyContent->removePage($pageid);
        if($res){
            $message = array(
                'message' => 'Page was deleted',
                'error_code' => 0
            );
            echo json_encode($message);
        } else {
            $message = array(
                'message' => 'Something went wrong',
                'error_code' => 1
            );
            echo json_encode($message);
        }
    }

    /**
     * Get states about files / folders.
     */
    public function getStats(){
        $files = $this->BucktyContent->getStats('files');
        $folders = $this->BucktyContent->getStats('folder');
        echo json_encode(array('files'=>$files,'folders'=>$folders));
    }
}