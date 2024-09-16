<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class GdriveController
 */
class GdriveController extends BUCKTY_Content
{
    /**
     * @var array
     */
 
    private $params = array();

    /**
     * @var string
     */
    private $redirect_uris;

    /**
     * @var Google_Client
     */
    private $client;

    /**
     * @var
     */
    private $user;

    /**
     * @var Google_Service_Drive
     */
    private $service;

    /**
     * GdriveController constructor.
     */
    function __construct(){
        /**
         * Load parent constructor
         */
        parent::__construct();
        $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        if ($this->aauth->is_loggedin()):
            $this->current_user = $this->logged_data();
        else:
            if($this->input->is_ajax_request()):
                $message = array('message'=> tran($this->trans->Please_Login_Again),'error_code'=>1,'login'=>'false');
                echo json_encode($message);
                exit();
            else:
                redirect($this->site->site_url);
            endif;
        endif;
        /**
         * Intializing google drive process
         *
         */
        $api = $this->BucktySettings->getApi();
        $google_api = $api['google'];
        if(empty($google_api)):
            redirect($this->site->site_url);
            exit();
        endif;
        $this->client = new Google_Client();
        if($google_api['id'] == '' || $google_api['secret'] == '') {
            if ($this->input->is_ajax_request()):
                $message = array('message' => tran($this->trans->Invalid_api), 'error_code' => 1);
                echo json_encode($message);
            else:
                redirect($this->site->site_url);
            endif;
            exit();
        } elseif($google_api['drive_activation'] == 0){
            $message = array('message'=>'Google drive '.tran($this->trans->was_disabled),'error_code'=>1);
            GenerateMsg($message,1);
            exit();
        }
        $this->params['web']['client_id'] = $google_api['id'];
        $this->params['web']['client_secret'] = $google_api['secret'];
        $this->redirect_uris = $this->site->site_url.'gdrive/end';
        $oauth_credentials = json_encode($this->params);
        $this->client->setAuthConfig($oauth_credentials);
        $this->client->setRedirectUri($this->redirect_uris);
        $this->client->addScope("https://www.googleapis.com/auth/drive");
        $this->service = new Google_Service_Drive($this->client);
        $this->user = $this->aauth->get_user_var('gdrive_token');

    }

    /**
     * Get access to google drive.
     */
    public function Gauth() {
        if(!$this->user && $this->client->isAccessTokenExpired()){
            redirect($this->client->createAuthUrl());
        } else {
            echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
            exit();
        }
    }

    /**
     * Google drive endpoint
     */
    public function endPoint(){
        $code = $this->input->get('code');
        try {
            $this->client->authenticate($code);
            $token = $this->client->getAccessToken();
            $this->aauth->set_user_var("gdrive_token", $token);
            echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
            exit();
        } catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * Push file to google drive.
     */
    public function uploadFile(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->user){
            $this->client->setAccessToken($this->user);
            if ($this->client->isAccessTokenExpired() && $this->input->get('code') == NULL) {
                if($this->input->is_ajax_request()):
                    $message = array('message'=>tran($this->trans->Login_again_to_google_drive),'auth'=>0);
                    GenerateMsg($message,1);
                    exit();
                else:
                    echo '<script type="text/javascript">Buckty.AuthGdrive();</script>';
                    exit();
                endif;
            }
        }
        $hash = $this->input->post('hash');
        $item = explode('/',$hash);
        $item_ = $item[1] == 'file' ? $this->BucktyContent->getFile($item[0]): '';
        if(!empty($item_)) {
            $path = FCPATH . 'application/views/uploads/content' . $item_->file_path . $item_->file_preview;
        // Now lets try and send the metadata as well using multipart!
        $file = new Google_Service_Drive_DriveFile();
        $file->setTitle($item_->file_name);
        $result2 = $this->service->files->insert(
            $file,
            array(
                'data' => file_get_contents($path),
                'mimeType' => $item_->file_mime,
                'uploadType' => 'multipart'
            )
        );
        if($result2){
            $message = array('message'=>tran($this->trans->folder_or_files_were_uploaded_to).' Drive');
            GenerateMsg($message,0);
        } else {
            $message = array('message'=> tran($this->trans->Something_went_wrong));
            GenerateMsg($message,1);
        }
        } else {
            $message = array('message'=>'No File');
            GenerateMsg($message,1);
        }
    }

    /**
     * Get files / folder list of specific user.
     */
    public function getList(){
        if ($this->user){
            $this->client->setAccessToken($this->user);
            if ($this->client->isAccessTokenExpired() && $this->input->get('code') == NULL) {
                if($this->input->is_ajax_request()):
                    $message = array('message'=>tran($this->trans->Login_again_to_google_drive),'auth'=>0,'error_code'=>1);
                    echo json_encode($message);
                    exit();
                else:
                    echo '<script type="text/javascript">Buckty.AuthGdrive();</script>';
                    exit();
                endif;
            }
        }
        if($this->client->isAccessTokenExpired() || $this->current_user->Googledrive == false){
                $data['main_content'] = 'contents/driveconnect';
                $this->load->view('main',$data);
        } else {
            $folderhash = $this->input->get('f');
            if ($folderhash != NULL) {
                $folderId = $this->input->get('f');
            } else {
                $info = $this->getInfo($this->service);
                $folderId = $info['folder_id'];
            }
            try {
                $children = $this->service->children->listChildren($folderId);
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
            $files = array();

            foreach ($children as $file) {
                $file = $this->fileInfo($this->service, $file['id']);
                $is_folder = explode('/', $file['mimeType'])[1] == 'vnd.google-apps.folder' ? 'folder' : 'file';
                $files[] = array('item_name' => $file['title'],
                    'item_size' => $file['fileSize'],
                    'item_ext' => $file['fileExtension'],
                    'item_id' => $file['id'],
                    'item_mime' => $file['mimeType'],
                    'type' => $is_folder);
            }

            $data['main_content'] = 'contents/drivelist';
            $data['in'] = 'drive';
            $data['files'] = $files;
            $data['parent'] = $this->getParents($this->service, $folderId);
            $this->load->view('main', $data);
        }
    }

    /**
     * Upload file to user's buckty account
     */
    public function getFileToServer(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->user){
            $this->client->setAccessToken($this->user);
            if ($this->client->isAccessTokenExpired() && $this->input->get('code') == NULL) {
                if($this->input->is_ajax_request()):
                    $message = array('message'=>tran($this->trans->Login_again_to_google_drive),'auth'=>0);
                    GenerateMsg($message,1);
                    exit();
                else:
                    echo '<script type="text/javascript">Buckty.AuthGdrive();</script>';
                    exit();
                endif;
            }
        }
        if($this->client->isAccessTokenExpired()){
            $message = array('message'=>tran($this->trans->Login_again_to_google_drive),'auth'=>0);
            GenerateMsg($message,1);
            exit();
        }
        $fileid = $this->input->post('file');
        $file = $this->fileInfo($this->service,$fileid);
        $occu = $this->userUsedSpace(null,0) + $file['fileSize'];
        if($occu > $this->site->upload_limit):
            $message = array('message'=>tran($this->trans->Not_enough_space));
            GenerateMsg($message,0);
            exit();
        endif;
        $user_id = $this->current_user->id;
        $today    = date('Ym');
        $u_hash = $this->aauth->get_user_var('user_hash');
        $det = array();
        $det['hash'] = $u_hash;
        $det['date'] = $today;
        $dir = $this->dirCheck($det);
        $file_name = $file['id'].'.'.$file['fileExtension'];
        if(in_array($file['fileExtension'],$this->getBlacklistTypes())):
            $message = array('message'=>tran($this->trans->File_type_not_allowed));
            GenerateMsg($message,0);
            exit();
        elseif(!in_array($file['fileExtension'],$this->getTypes())):
            $message = array('message'=>tran($this->trans->File_type_not_allowed));
            GenerateMsg($message,0);
            exit();
        endif;
        $is_image = explode('/',$file['mimeType'])[0] == 'image' ? true : false;
        $content = $this->downloadFile($this->service,$file);
        $myfile = fopen($dir.'/'.$file_name, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
        $file_ = array('file_name'=>$file['title'],
            'hash'=>$this->generateRandomString()
        );
        $file_id = $this->BucktyContent->insertFile($this->config_vars['files'],$file_);
        if($is_image):
            list($img_w, $img_h) = getimagesize($dir.'/'.$file_name);
        else:
            $img_w = 0;
            $img_h = 0;
        endif;
        if(is_numeric($file_id)):
            $file_data = array('file_id'=>$file_id,
                'file_author'=>$user_id,
                'file_mime'=>$file['mimeType'],
                'file_date'=> date('Y-m-d H:i:s'),
                'file_size'=>$file['fileSize'],
                'image_width'=>$img_w,
                'image_height'=>$img_h,
                'file_preview'=>$file_name,
                'file_path' => '/'.$u_hash.'/'.$today.'/',
                'file_type'=>$file['fileExtension'],
                'file_ext'=>$file['fileExtension']
            );
            $relation = array('author_id'=>$user_id,
                'content_id'=>$file_id,
                'content_parent'=>0,
                'content_type'=>'file',
                'permission'=>1,
                'owner'=>1);
            $this->BucktyContent->insertFile($this->config_vars['files_data'],$file_data);
            $this->BucktyContent->insertFile($this->config_vars['relations'],$relation);
            $note_body = 'Google Drive '.tran($this->trans->file_was_saved_to_root);
            $this->BucktyContent->addNotification($user_id,null,$note_body,$file_id,'google');
            $message = array('message'=>tran($this->trans->Files_were_saved_to_root_folder));
            GenerateMsg($message,0);
          else:
             $message = array('message'=> tran($this->trans->Something_went_wrong));
             GenerateMsg($message,1);
          endif;
    }

    /**
     * Get files from the specified folder
     * @param $service
     * @param $folderId
     * @return mixed
     */
    function printFilesInFolder($service, $folderId) {
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $children = $service->children->listChildren($folderId, $parameters);
                return $children;
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
    }

    /**
     * Print a file's metadata.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param string $fileId ID of the file to print metadata for.
     * @return  instance.
     */
    function fileInfo($service, $fileId) {
        try {
            $file = $service->files->get($fileId);
            return $file;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

    /**
     * Print a file's parents.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param String $fileId ID of the file to print parents for.
     * @return parent id.
     */
    function getParents($service, $fileId) {
        try {
            $parents = $service->parents->listParents($fileId);
            $parent = '';
            foreach ($parents->getItems() as $parent) {
                $parent = $parent['id'];
            }
            return $parent;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
    /**
     * Download a file's content.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @param File $file Drive File instance.
     * @return String The file's content if successful, null otherwise.
     */
    function downloadFile($service, $file) {
        $downloadUrl = $file->getDownloadUrl();
        if ($downloadUrl) {
            $request = new Google_Http_Request($downloadUrl, 'GET', null, null);
            $httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
            if ($httpRequest->getResponseHttpCode() == 200) {
                return $httpRequest->getResponseBody();
            } else {
                // An error occurred.
                return null;
            }
        } else {
            // The file doesn't have any content stored on Drive.
            return null;
        }
    }

    /**
     * Get google drive account information.
     * @param $service
     * @return mixed
     */
    function getInfo($service) {
        try {
            $about = $service->about->get();

            $info['name'] = $about->getName();
            $info['folder_id']  = $about->getRootFolderId();
            $info['total_space']   = $about->getQuotaBytesTotal();
            $info['occupied'] = $about->getQuotaBytesUsed();

        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return $info;
    }

    /**
     * Remove google drive access
     */
    public function removeGdrive(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $res =  $this->aauth->unset_user_var('gdrive_token');
        if($res){
            $message = array('message'=>'Drive '.tran($this->trans->access_was_removed));
            GenerateMsg($message,0);
        } else {
            $message = array('message'=>tran($this->trans->Something_went_wrong));
            GenerateMsg($message,1);
        }
    }

    /**
     * Generate random string
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
     * Check if user directory is available else create one.
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
     * Get blacklisted files.
     * @return array
     */
    public function getBlacklistTypes(){
        return explode(',',$this->site->blacklist_extensions);
    }
}