<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class DropBoxController
 */
class DropBoxController extends BUCKTY_Content
{
    /**
     * @var array
     */
    private $params = array();

    /**
     * @var \Dropbox\AppInfo
     */
    private $appInfo;

    /**
     * @var \Dropbox\ArrayEntryStore
     */
    private $csrfTokenStore;

    /**
     * @var \Dropbox\WebAuth
     */
    private $webAuth;

    /**
     * @var
     */
    private $user;

    /**
     * @var \Dropbox\Client
     */
    private $client;

    /**
     * DropBoxController constructor.
     */
    public function __construct()
    {
        /**
         * load parent constructor
         */
    	parent::__construct();
            $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        if ($this->aauth->is_loggedin()):
            $this->current_user = $this->logged_data();
        else:
        if($this->input->is_ajax_request()):
            $message = array('message'=>tran($this->trans->Please_Login_Again),'error_code'=>1,'login'=>'false');
            echo json_encode($message);
            exit();
        else:
            redirect($this->site->site_url);
        endif;
        endif;
        /**
         * Intialize the dropbox process.
         */
        $api = $this->BucktySettings->getApi();
        $dropbox = $api['dropbox'];
        if(empty($dropbox)):
            redirect($this->site->site_url);
            exit();
        endif;
        $this->params['key'] = $dropbox['id'];
        $this->params['secret'] = $dropbox['secret'];
        $this->params['appName'] = $dropbox['appname'];
        $this->params['end_uri'] = $this->site->site_url.'dropbox/end';
        if($this->params['key'] == '' || $this->params['secret'] == '' || $this->params['appName'] == ''){
            if($this->input->is_ajax_request()):
                $message = array('message'=>tran($this->trans->Invalid_api),'error_code'=>1);
                echo json_encode($message);
                exit();
            else:
                redirect($this->site->site_url);
            endif;
        }
        $this->appInfo = new Dropbox\AppInfo($this->params['key'],$this->params['secret']);
        $this->csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION,'dropbox-auth-csrf-token');
        $this->webAuth = new Dropbox\WebAuth($this->appInfo,$this->params['appName'],$this->params['end_uri'],$this->csrfTokenStore);
        $this->user = $this->aauth->get_user_var('dropbox_token');
        if($this->user){
        $this->client = new Dropbox\Client($this->user,$this->params['appName'],'UTF-8');
        }
    }

    /**
     * Auth to dropbox
     */
    public function index()
	{
        if($this->user){
            try{
                $this->client->getAccountInfo();
            } catch(Dropbox\Exception_InvalidAccessToken $e){
                $authUrl = $this->webAuth->start();
                redirect($authUrl);
                exit("<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>");
            }
        } else {
            $authUrl = $this->webAuth->start();
            redirect($authUrl);
            exit("<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>");
        }
	}

    /**
     * Dropbox authentication endpoint
     */
	public function DropEnd()
	{
        try {
            list($token) = $this->webAuth->finish($this->input->get());
            $this->aauth->set_user_var("dropbox_token", $token);
            echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
        } catch(Exception $e){
            echo $e->getMessage();
        }
	}

    /**
     * Get request from system to upload file to dropbox
     */
	public function Upload()
	{
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                            'message' => 'Invalid csrf Token',
                            'error_code' => 1
                        );
            echo json_encode($message);
            exit();
        }
        if(!$this->user){
            $message = array(
                'message' => tran($this->trans->Login_Again),
                'error_code' => 1,
                'auth'=>0
            );
            echo json_encode($message);
            exit();
        }
        $hash = $this->input->post('hash');
        $item = explode('/',$hash);
        if($item[1] == 'file'):
            $this->pushFile($item[0]);
        elseif($item[1] == 'folder'):
            $this->pushFolder($item[0]);
        endif;
	}

    /**
     * Upload file to dropbox
     * @param $hash
     */
    private function pushFile($hash){
        $item_ = $this->BucktyContent->getFile($hash);
        if(!empty($item_)){
            $path = FCPATH.'application/views/uploads/content'.$item_->file_path.$item_->file_preview;
            $file = fopen($path,'rb');
            $response = $this->client->uploadFile('/'.$item_->file_name,Dropbox\WriteMode::add(),$file,(int) $item_->file_size);
            if(!empty($response)){
            $message = array('message'=>$item_->file_name.' | '.tran($this->trans->was_uploaded_to).' dropbox');
            GenerateMsg($message,0);
            }
        } else {
            $message = array('message'=>tran($this->trans->Something_went_wrong));
            GenerateMsg($message,1);
        }  
    }

    /**
     * Upload folder to dropbox
     * @param $hash
     */
    private function pushFolder($hash){
        $folder_ = $this->BucktyContent->getFolder($hash);
        if(!empty($folder_)):
            $files = $this->BucktyContent->getFilesByFolder($folder_->folder_hash);
            foreach($files as $file):
                $item_ = $this->BucktyContent->getFile($file->hash);
                if(!empty($item_)):
                    $path = FCPATH.'application/views/uploads/content'.$item_->file_path.$item_->file_preview;
                    $file = fopen($path,'rb');
                    $response = $this->client->uploadFile('/'.$folder_->folder_name.'/'.$item_->file_name,Dropbox\WriteMode::force(),
                                                          $file,
                                                          (int) $item_->file_size);
                else:
                    continue;
                endif;
            endforeach;
            $message = array('message'=>tran($this->trans->folder_or_files_were_uploaded_to).' dropbox');
            GenerateMsg($message,1);
        else:
            $message = array('message'=>tran($this->trans->Folder_was_empty));
            GenerateMsg($message,1);
        endif;
    }

    /**
     * Get dropbox files list.
     */
    public function getList(){
        if(!array_key_exists('dropbox',$this->current_user)){
            $data['main_content'] = 'contents/dropboxconnect';
            $this->load->view('main',$data);

        } else {
            $path = $this->input->get('path') === NULL ? '/' : $this->input->get('path');
            $parent = $this->input->get('parent') === NULL ? 'n' : $this->input->get('parent');
            $list = $this->client->getMetadataWithChildren($path);
            $data['list'] = $list;
            $data['parent'] = $parent;
            $data['in'] = 'dropbox';
            $data['main_content'] = 'contents/dropboxlist';
            $this->load->view('main', $data);
        }
    }

    /**
     * Remove the dropbox access
     */
    public function removeAuth(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                            'message' => 'Invalid csrf Token',
                            'error_code' => 1
                        );
            echo json_encode($message);
            exit();
        }
        $userid = $this->current_user->id;
        $res = $this->aauth->unset_user_var('dropbox_token');
        if($res){
            $message = array('message'=>'Dropbox '.tran($this->trans->access_was_removed));
            GenerateMsg($message,0);
        } else {
           $message = array('message'=>tran($this->trans->Something_went_wrong));
            GenerateMsg($message,1); 
        }
    }

    /**
     * Download file to user account.
     */
    public function getDbFile(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $user_id = $this->current_user->id;
        $file = $this->input->post('path');
        $filemeta = $this->client->getMetadata($file);
        $occu = $this->userUsedSpace(null,0) + $filemeta['bytes'];
        if($occu > $this->site->upload_limit):
            $message = array('message'=>tran($this->trans->Not_enough_space));
            GenerateMsg($message,0);
            exit();
        endif;
        $today    = date('Ym');
        $u_hash = $this->aauth->get_user_var('user_hash');
        $det = array();
        $det['hash'] = $u_hash;
        $det['date'] = $today;
        $ext = explode('.',$file);
        if(in_array($ext[1],$this->getBlacklistTypes())):
            $message = array('message'=>tran($this->trans->File_type_not_allowed));
            GenerateMsg($message,0);
            exit();
        elseif(!in_array($ext[1],$this->getTypes())):
            $message = array('message'=>tran($this->trans->File_type_not_allowed));
            GenerateMsg($message,0);
            exit();
        endif;
        $dir = $this->dirCheck($det);
        $file_name = $this->generateRandomString(20);
        $file_ = fopen($dir.'/'.$file_name,'wb');
        $res = $this->client->getFile($file,$file_);
        if($res != NULL):
        $data = $res;
            $folder = '0';
            $fname = explode('/',$file);
            $data['orig_name'] = end($fname);
            $data['is_image'] = explode('/',$data['mime_type'])[0] == 'image' ? true : false;
        $img_w = $data['is_image'] == true ? $data['photo_info']['image_dimensions'][0]: 0;
        $img_h = $data['is_image'] == true ? $data['photo_info']['image_dimensions'][1]: 0;
        $file_size = $data['bytes'];
        $file_ = array('file_name'=>$data['orig_name'],
            'hash'=>$this->generateRandomString()
        );
        $file_id = $this->BucktyContent->insertFile($this->config_vars['files'],$file_);
        if(is_numeric($file_id)):
            $file_data = array('file_id'=>$file_id,
                'file_author'=>$user_id,
                'file_mime'=>$data['mime_type'],
                'file_date'=> date('Y-m-d H:i:s'),
                'file_size'=>$file_size,
                'image_width'=>$img_w,
                'image_height'=>$img_h,
                'file_preview'=>$file_name,
                'file_path' => '/'.$u_hash.'/'.$today.'/',
                'file_type'=>$ext[1],
                'file_ext'=>$ext[1]
            );
            $relation = array('author_id'=>$user_id,
                'content_id'=>$file_id,
                'content_parent'=>$folder,
                'content_type'=>'file',
                'permission'=>1,
                'owner'=>1);
            $this->BucktyContent->insertFile($this->config_vars['files_data'],$file_data);
            $this->BucktyContent->insertFile($this->config_vars['relations'],$relation);
            $note_body = 'Dropbox '.tran($this->trans->file_was_saved_to_root);
            $this->BucktyContent->addNotification($user_id,null,$note_body,$file_id,'dropbox');
            $message = array('message'=> tran($this->trans->Files_were_saved_to_root_folder));
            GenerateMsg($message,0);
          endif;
        else:
            $message = array('message'=>tran($this->trans->File_not_available));
            GenerateMsg($message,1);
        endif;
    }

    /**
     * Generate a random string
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 15) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Check if the user directory exsits or not if not then create one.
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
     * Get allowed extentions
     * @return mixed
     */
    private function getTypes()
    {
        return explode(',',$this->site->allowed_extensions);
    }

    /**
     * @return array
     */
    public function getBlacklistTypes(){
        return explode(',',$this->site->blacklist_extensions);
    }
}