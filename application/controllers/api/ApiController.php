<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiController extends BUCKTY_Content
{


    /**
     * Authorization key default is null
     * @var null
     */
    private $authorization = null;

    /**
     * User key for authenticating authorization key
     * @var null
     */
    private $user_key = null;

    /**
     * @var bool
     */
    private $valid = FALSE;

    /**
     * User id
     * @var int
     */
    private $user_id = 0;

    private $today;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->output->set_header('Content-Type: application/json; charset=utf-8');
        $this->authorization = $this->input->get('b_auth') == NULL ? $this->input->post('b_auth') == NULL ? 0 : $this->input->post('b_auth') : $this->input->get('b_auth');
        $this->user_key = $this->input->get('b_user') == NULL ? $this->input->post('b_user') == NULL ? 0 : $this->input->post('b_user') : $this->input->get('b_user');
        $this->load->model('BucktyApi');
        $this->load->model('BucktyContent');
        $this->valid = !empty($this->authorization) && $this->BucktyApi->checkAuth($this->authorization, $this->user_key) ? TRUE : FALSE;
        if(!$this->valid){
            $message = array('message'=>'invalid api details');
            GenerateMsg($message,1);
            exit;
        }
        $this->user_id = $this->aauth->get_user_id_by_hash($this->user_key);
        if (!$this->user_id) {
            $message = array('message' => 'invalid api details');
            GenerateMsg($message, 1);
            $this->response(403);
            exit;
        }
        $ip = $this->input->ip_address();
        $this->BucktyApi->ApiLogInsert($this->authorization,$ip);
        $this->today = date('Ym');
    }


    public function requestUpload()
    {
        $absoluteUrl = $this->input->get('absoluteUrl');
        if($absoluteUrl != NULL || false){
            $isvalid = $this->checkUrl($absoluteUrl) ? true: false;

            if($isvalid):
                if($this->getRemoteFile($absoluteUrl)):
                    GenerateMsg(array('message'=>'Request was made successfully'),0);
                    exit;
                else:
                    GenerateMsg(array('message'=>'File could not be uploaded'),0);
                    exit;
                endif;
            else:
                GenerateMsg(array('message'=>'Url provided is not valid'),0);
                exit;
            endif;
        }
        $message = array('message' => 'Invalid api request');
        GenerateMsg($message, 1);
    }

    /**
     * Pass url and the system will save the file into user's directory ($user_key is meant to be user)
     * @param null $absoluteUrl
     * @return bool
     */
    private function getRemoteFile($absoluteUrl = null)
    {
        // URL to file (link)
        $file = $absoluteUrl;
        $file_name = basename($file);
        $data = $this->get_headers($file);
        var_dump($data);
        die();
        $det['hash'] = $this->user_key;
        $det['date'] = $this->today;
        $dir = $this->dirCheck($det);
        $file_extension = explode('/',$data['content_type']);
        $is_image = $file_extension[0] == 'image' ? true:false;
        if($is_image){
          $info = getimagesize($absoluteUrl);
          $image_width = $info[0];
          $image_height = $info[1];
        } else {
            $image_width = 0;
            $image_height = 0;
        }
        $data = array('file_mime'=>$data['content_type'],'file_size'=>$data['download_content_length'],'image_width'=>$image_width,'image_height'=>$image_height,'file_ext'=>$file_extension[1]);
        $remoteFilename = generateRandomString().'.'.$file_extension[1];
        $occu = $this->userUsedSpace(null,0) + $data['file_size'];
        if($occu > $this->site->upload_limit):
            $message = array('message'=>'Not enough space');
            GenerateMsg($message,0);
            exit();
        endif;
        $this->CopyFile($absoluteUrl,$dir.'/'.$remoteFilename);
        $file_ = array('file_name'=>$file_name,
            'hash'=>generateRandomString(10)
        );
        $file_id = $this->BucktyContent->insertFile($this->config_vars['files'],$file_);
        if(is_numeric($file_id)):
            $file_data = array('file_id'=>$file_id,
                'file_author'=>$this->user_id,
                'file_mime'=>$data['file_mime'],
                'file_date'=> date('Y-m-d H:i:s'),
                'file_size'=>$data['file_size'],
                'image_width'=>$data['image_width'],
                'image_height'=>$data['image_width'],
                'file_preview'=>$remoteFilename,
                'file_path' => '/'.$this->user_key.'/'.$this->today.'/',
                'file_type'=>$data['file_ext'],
                'file_ext'=>$data['file_ext']
            );
            $relation = array('author_id'=>$this->user_id,
                'content_id'=>$file_id,
                'content_parent'=>0,
                'content_type'=>'file',
                'permission'=>1,
                'owner'=>1);
            $this->BucktyContent->insertFile($this->config_vars['files_data'],$file_data);
            $this->BucktyContent->insertFile($this->config_vars['relations'],$relation);
            $note_body = 'Remote file was saved to your root';
            $this->BucktyContent->addNotification($this->user_id,null,$note_body,$file_id,'cloud');
            return true;
            endif;
    }

    /**
     * Get user requested user details
     * @output json
     */
    public function userInfo()
    {
        $userInfo = $this->aauth->get_user_offline($this->user_id);
        echo json_encode($userInfo);
        exit();
    }

    /**
     *
     */
    public function loginUser()
    {
            $this->load->library('form_validation');
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_rules('identity', 'Email Address', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');

            if ($this->form_validation->run() !== false):
                if ($this->aauth->login($this->input->get('identity'), $this->input->get('password'), $this->input->get('remember'))):
                    $message = array('message'=>'Logged in');
                    GenerateMsg($message,0);
                else:
                    $auth_err = $this->aauth->print_errors();
                    $message = array('message'=> $auth_err);
                    GenerateMsg($message,1);
                endif;
            else:
                $message = array('message' => strip_tags(validation_errors()));
                GenerateMsg($message,1);
            endif;
    }

    /**
     * Check if provided url exist or not
     * @param $domain
     * @return bool
     */
    private function checkUrl($domain){
        //check, if a valid url is provided
        if(!filter_var($domain, FILTER_VALIDATE_URL))
        {
            return false;
        }

        //initialize curl
        $curlInit = curl_init($domain);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

        //get answer
        $response = curl_exec($curlInit);

        curl_close($curlInit);

        if ($response) return true;

        return false;
    }


    /**
     * Get headers for a file.
     * @param $url
     * @return bool
     */
    function get_headers($url) {
        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_NOBODY, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
        curl_exec( $ch );
        $headers = curl_getinfo( $ch );
        curl_close( $ch );

        return $headers;
    }

    /**
     * Copy the files into user directory with curl.
     * @param $file
     * @param $output
     */
    public function CopyFile($file,$output){
        $source = $file;
        $target = $output;

        $ch = curl_init($source);
        $fp = fopen($target, "wb");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    /**
     * Check if provided user's directory exist or not
     * @param $dir
     * @return string
     */
    private function dirCheck($dir){
        if (!is_dir(FCPATH.'application/views/uploads/content/' . $dir['hash'])):
            mkdir(FCPATH.'application/views/uploads/content/' . $dir['hash'], 0777, true);
        endif;
        if(!is_dir(FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'])):
            mkdir(FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'],0777,true);
        endif;
        return FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'];
    }

    /**
     * Get user's files with api details.
     *
     */
    public function ApiFiles(){
        $content = array();
        $folder = $this->input->get('in_folder') == NULL ? $this->input->post('in_folder') == NULL ? null : $this->input->post('in_folder') : $this->input->get('in_folder');
        $query = $this->input->get('search_query') == NULL ? $this->input->post('search_query') == NULL ? null : $this->input->post('search_query') : $this->input->get('search_query');
        $content['files'] = $this->BucktyApi->GetContent('files',$folder,$this->user_id,$query);
        echo json_encode($content);
        exit();
    }

    /**
     *  Get user's folders with api details.
     */
    public function ApiFolder(){
        $content = array();
        $folder = $this->input->get('in_folder') == NULL ? $this->input->post('in_folder') == NULL ? null : $this->input->post('in_folder') : $this->input->get('in_folder');
        $content['folders'] = $this->BucktyApi->GetContent('folder',$folder,$this->user_id);
        echo json_encode($content);
        exit();
    }

    public function ApiGetFile(){
        $file = $this->input->get('file') == NULL ? $this->input->post('file') == NULL ? null : $this->input->post('file') : $this->input->get('file');
        $content = $this->BucktyContent->getFile($file,$this->user_id);
        $content = $content != null ? $content : array('msg'=>array('message'=>'Nothing found'),'error_code'=>1);
        echo json_encode($content);
        exit();
    }
    public function ApiGetFolder(){
        $file = $this->input->get('folder') == NULL ? $this->input->post('folder') == NULL ? null : $this->input->post('folder') : $this->input->get('folder');
        $content = $this->BucktyContent->getFolder($file,$this->user_id);
        $content = $content != null ? $content : array('msg'=>array('message'=>'Nothing found'),'error_code'=>1);
        echo json_encode($content);
        exit();
    }
}