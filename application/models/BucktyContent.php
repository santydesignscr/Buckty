<?php

/**
 * Class BucktyContent
 */
class BucktyContent extends CI_Model
{
    
    /**
     * The CodeIgniter object variable
     * @access public
     * @var object
     */
    public $CI;
    
    /**
     * Variable for loading the config array into
     * @access public
     * @var array
     */
    public $config_vars;

    /**
     * BucktyContent constructor.
     */
    function __construct()
    {
        // get main CI object
        $this->CI =& get_instance();
        
        // Dependancies
        if (CI_VERSION >= 2.2) {
            $this->CI->load->library('driver');
        }
        $this->CI->load->library('session');
        $this->CI->load->library('email');
        $this->CI->load->library('Aauth');
        $this->CI->load->helper('url');
        $this->CI->load->helper('string');
        $this->CI->load->helper('email');
        $this->CI->load->helper('language');
        $this->CI->lang->load('aauth');
        // config/buckty.php
        $this->CI->config->load('buckty');
        $this->config_vars = $this->CI->config->item('buckty');
    }

    /**
     * Get dashboard content for user | provided user with various other parameters
     * @param null $type | required
     * @param null $inId | optional
     * @param null $uid | optional
     * @param null $term | optional
     * @return object
     */
    public function GetContent($type = null, $inId = null, $uid = null,$term = null)
    {
        $inId = (string) $inId;
        $order = !empty($_COOKIE['order']) ? $_COOKIE['order']: null;
        $order_in = !empty($_COOKIE['order']) ? $_COOKIE['order_in']: null;
        switch ($type) {
            case 'folder':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                if($inId != null && $term == null):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', $inId);
                endif;
                if($term == 'shared'):
                     $this->db->where($this->config_vars['relations'] . '.content_parent', '0');
                endif;
                $this->db->where($this->config_vars['folders'] . '.trashed', 0);
                if($uid != null):
                    $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                endif;
                $this->db->where($this->config_vars['relations']. '.content_type','folder');
                switch ($term){
                    case 'starred':
                        $this->db->where($this->config_vars['relations']. '.starred',1);   
                    break;
                    case 'shared':
                        $this->db->where($this->config_vars['relations']. '.shared',1);
                    break;
                    case 'recent':
                        $this->db->where($this->config_vars['relations'].'.shared',0);
                        $this->db->order_by($this->config_vars['folders'].'.date','DESC');
                    break;
                    default:
                        $this->db->like($this->config_vars['folders'].'.folder_name',$term);

                }
                $details = $this->db->get($this->config_vars['folders']);
                $fetchedFolders = $details->result();
                $folders = array();
                foreach($fetchedFolders as $folder){
                    $this->db->select('*');
                    $this->db->join($this->config_vars['relations'],
                                    $this->config_vars['relations'] .
                                    '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                    $this->db->where($this->config_vars['relations'].'.content_parent',$folder->folder_hash);
                    $r = $this->db->get($this->config_vars['folders']);
                    $r = $r->result();
                    $folder->has_sub = !empty($r) ? 1 : 0;
                    $folder->root = $folder->content_parent == 0 ? 1 : 0;
                    $folders[] = $folder;
                }
                return (object) $folders;
                break;
            case 'files':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
                if($inId != null && $term == null):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', $inId);
                endif;
                if($term == 'shared'):
                     $this->db->where($this->config_vars['relations'] . '.content_parent', '0');
                endif;
                if($uid != null):
                    $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                endif;
                $this->db->where($this->config_vars['files'] . '.trashed', 0);
                $this->db->where($this->config_vars['relations']. '.content_type','file');
                switch ($term){
                    case 'starred':
                        $this->db->where($this->config_vars['relations']. '.starred',1);   
                    break;
                    case 'shared':
                        $this->db->where($this->config_vars['relations']. '.shared',1);
                    break;
                    case 'recent':
                        $this->db->where($this->config_vars['relations'].'.shared',0);
                        $this->db->order_by($this->config_vars['files_data'].'.file_date','DESC');
                        break;
                    default:
                        $this->db->like($this->config_vars['files'].'.file_name',$term);
                        
                }
                if($order != null){
                    $this->db->order_by($order,$order_in); 
                }
                $this->db->group_by($this->config_vars['relations'].'.content_id');
                $details = $this->db->get($this->config_vars['files']);
                $files = $details->result();
                $files_ = array();
                foreach($files as $file){
                    $file->absoluteUrl = base_url().'userfile/'.$file->hash;
                    $file->getfileUrl  = base_url().'useraction/get/'.$file->hash;
                    $files_[] = $file;
                }
                return (object) $files_;
                break;

        }
    }

    /**
     * Get tree view for provided folder .
     * @param $hash
     * @param null $uid
     * @return object
     */
    
    public function GetTree($hash,$uid = null){
        $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                $this->db->where($this->config_vars['relations'] . '.content_parent', $hash);
                $this->db->where($this->config_vars['folders'] . '.trashed', 0);
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                $this->db->where($this->config_vars['relations']. '.content_type','folder');
                $details = $this->db->get($this->config_vars['folders']);
                $fetchedFolders = $details->result();
                $folders = array();
                foreach($fetchedFolders as $folder){
                    $this->db->select('*');
                    $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                    $this->db->where($this->config_vars['relations'].'.content_parent',$folder->folder_hash);
                    $this->db->where($this->config_vars['relations'].'.content_type','folder');
                    $r = $this->db->get($this->config_vars['folders']);
                    $r = $r->result();
                    if(!empty($r)){
                        $folder->has_sub = 1;
                    } else {
                        $folder->has_sub = 0;
                    }
                    $folders[] = $folder;
                }
                return (object) $folders;
    }

    /**
     * Get sub folders
     * @param $hash
     * @param $uid
     * @return mixed
     */
    public function GetSubFolders($hash,$uid){
         $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                $this->db->where($this->config_vars['relations'] . '.content_parent', $hash);
                $this->db->where($this->config_vars['folders'] . '.trashed', 0);
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                $this->db->where($this->config_vars['relations']. '.content_type','folder');
                $details = $this->db->get($this->config_vars['folders']);
                $fetchedFolders = $details->result();
                return $fetchedFolders;
    }

    /**
     * Get trashed files or folders for specific user with optional folder.
     * @param null $type | required
     * @param null $inId | optional
     * @param $uid | required
     * @return mixed
     */
    public function GetTrash($type = null, $inId = null, $uid)
    {
        $inId = (string) $inId;
        

        switch ($type) {
            case 'folder':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                
                $this->db->where($this->config_vars['folders'] . '.trashed', 1);
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                $this->db->where($this->config_vars['relations'].'.content_type','folder');
                $this->db->group_by($this->config_vars['folders'].'.folder_id');
                $details = $this->db->get($this->config_vars['folders']);
                return $details->result();
                break;
            case 'files':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                $this->db->where($this->config_vars['files'] . '.trashed', 1);
                $this->db->where($this->config_vars['relations'].'.content_type','file');
                $this->db->group_by($this->config_vars['files'].'.ID'); 
                $details = $this->db->get($this->config_vars['files']);
                return $details->result();
                break;
                
        }
    }

    /**
     * Get img data for file.
     * @param $fileid
     * @return mixed
     */
    public function getimg($fileid)
    {
        $this->db->select('*');
        $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
        $this->db->where($this->config_vars['files'] . '.hash', $fileid);
        $details = $this->db->get($this->config_vars['files']);
        return $details->row();
    }

    /**
     * Create folder for a user
     * @param $uid
     */

    public function makeUserFolder($uid){
        $data = array('folder_name'=>'Folders','folder_hash'=>'0','folder_author'=>$uid,'date'=>date('Y-m-d H:m:s'),'protected'=>1);
        $this->db->insert($this->config_vars['folders'],$data);
    }

    /**
     * Get sub folders for parent folder
     * @param $hash
     * @return array
     */
    public function GetSub($hash){
        $this->db->select('*');
        $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
        $this->db->where($this->config_vars['relations'].'.content_parent',$hash);
        $this->db->where($this->config_vars['relations'].'.content_type','folder');
        $subs = $this->db->get($this->config_vars['folders']);
        $fetchedFolders = $subs->result();
                $folders = array();
                foreach($fetchedFolders as $folder){
                    $this->db->select('*');
                    $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                    $this->db->where($this->config_vars['relations'].'.content_parent',$folder->folder_hash);
                    $r = $this->db->get($this->config_vars['folders']);
                    $r = $r->result();
                    if(!empty($r)){
                        $folder->has_sub = 1;
                    } else {
                        $folder->has_sub = 0;
                    }
                    $folders[] = $folder;
                }
                return $folders;
    }

    /**
     * Get uploaded content details for admin with various parameters
     * @param null $type | required
     * @param null $offset | optional
     * @param null $limit | optional
     * @param null $key | optional
     * @return object
     */
    public function GetContentAdmin($type = null,$offset = NULL ,$limit = NULL,$key = NULL) {
        switch ($type) {
            case 'folder':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                $this->db->join($this->config_vars['users'],$this->config_vars['users'].'.id = '.$this->config_vars['folders'].'.folder_author');
                $this->db->where($this->config_vars['relations']. '.content_type','folder');
                $this->db->group_by($this->config_vars['folders'].'.folder_id');
                if ($limit) {

                    if ($offset == FALSE)
                        $this->db->limit($limit);
                    else
                        $this->db->limit($limit, $offset);
                }
                if($key != NULL){
                    $this->db->like($this->config_vars['folders'].'.folder_name',$key);
                }
                $details = $this->db->get($this->config_vars['folders']);
                $fetchedFolders = $details->result();
                $folders = array();
                foreach($fetchedFolders as $folder){
                    $this->db->select('*');
                    $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                    $this->db->where($this->config_vars['relations'].'.content_parent',$folder->folder_hash);
                    $r = $this->db->get($this->config_vars['folders']);
                    $r = $r->result();
                    $folder->has_sub = !empty($r) ? 1 : 0;
                    $folder->root = $folder->content_parent == 0 ? 1 : 0;
                    $folders[] = $folder;
                }
                return (object) $folders;
                break;
            case 'files':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->join($this->config_vars['files_data'],$this->config_vars['files_data'].'.file_id = '.$this->config_vars['files'].'.ID','left');
                $this->db->join($this->config_vars['users'],$this->config_vars['users'].'.id = '.$this->config_vars['files_data'].'.file_author','left');
                $this->db->where($this->config_vars['relations']. '.content_type','file');
                if ($limit) {
                    if ($offset == FALSE)
                        $this->db->limit($limit);
                    else
                        $this->db->limit($limit, $offset);
                }
                if($key != NULL){
                    $this->db->like($this->config_vars['files'].'.file_name',$key);
                }
                $this->db->group_by($this->config_vars['files'].'.ID');
                $details = $this->db->get($this->config_vars['files']);
                $files = $details->result();
                return $files;

                break;
                
        }
    }

    /**
     * Move files or folders from one directory to another | Download files/folder from user one to user two 's directory by dropping or moving.
     * @param $uid
     * @param $hash
     * @param $files_hash
     * @return object
     */
    public function moveItem($uid,$hash,$files_hash){
        $userspace = $this->userUsedSpace(null,0);
        if($hash != '0') {
            $this->db->select('*');
            $this->db->where('folder_hash', $hash);
            $folder = $this->db->get($this->config_vars['folders']);
            $folder = $folder->row();
        } else {
            $folder = (object) array('folder_hash'=>'0','is_shared'=>'0');
        }
        foreach($files_hash as $file){
            $f = explode('/',$file);
            if($f[1] == 'folder'){
                if($f[0] == $hash || in_array($hash,$this->folderFileList($f[0]))){
                    $message = (object) array('error_code'=>1,'message'=>'Can\'t move files');
                    return $message;
                }
                $this->db->select('folder_id,folder_hash,folder_author');
                $this->db->where('folder_hash',$f[0]);
                $fol = $this->db->get($this->config_vars['folders']);
                $fol = $fol->row();
                if($fol->folder_author == $uid) {
                    $data = array('content_parent' => $hash);
                    $this->db->where('content_id', $fol->folder_id);
                    $this->db->where('author_id', $uid);
                    $this->db->where('content_type','folder');
                    $this->db->update($this->config_vars['relations'], $data);
                    if ($folder->is_shared == '1') {
                        $users = $this->getFolderUsers($folder->folder_hash);
                        if (!empty($users)):
                            foreach ($users as $user) {
                                $this->stampSharedFolder($hash, $user->user_id);
                            }
                        endif;
                    }
                    $message =  (object) array('error_code'=>0,'message'=>'Folder / files moved');
                } else {
                    if(!empty($file)) {
                        $folder = $this->getFolder($f[0]);
                        $total = $userspace + $this->folderSize($folder->folder_hash);
                        if($total > SiteAllowedSpace()){
                            return (object) array('error_code'=>2,'message'=>'Not enough space');
                        }
                        $this->saveFolder($f[0],$hash,$uid);
                    }
                    $message = (object) array('error_code'=>0,'message'=>'Folder / files moved');
                }
            } elseif($f[1] == 'file') {
                $this->db->select('*');
                $this->db->join($this->config_vars['files_data'],$this->config_vars['files_data'].'.file_id = '.$this->config_vars['files'].'.ID','left');
                $this->db->where($this->config_vars['files'].'.hash',$f[0]);
                $file = $this->db->get($this->config_vars['files']);
                $file = $file->row();
                if($file->file_author == $uid) {
                    $data = array('content_parent' => $hash);
                    $this->db->where('content_id', $file->ID);
                    $this->db->where('author_id', $uid);
                    $this->db->where('content_type','file');
                    $this->db->update($this->config_vars['relations'], $data);
                    if ($folder->is_shared == '1') {
                        $users = $this->getFolderUsers($hash);
                        if (!empty($users)):
                            foreach ($users as $user) {
                                $this->stampSharedFile($hash, $user->user_id);
                            }
                        endif;
                    } else {
                        $users = $this->getFileUsers($file->hash);
                        if (!empty($users)):
                            foreach ($users as $user) {
                                if ($file->file_author != $uid)
                                    $this->unstampSharedFile($hash, $user->user_id);
                            }

                        endif;
                    }
                    $message =  (object) array('error_code'=>0,'message'=>'Folder / files moved');
                } else {
                    $file = $this->getFile($f[0]);
                    if(!empty($file)) {
                        $total = $userspace + $file->file_size;
                        if($total > SiteAllowedSpace()){
                            return (object) array('error_code'=>2,'message'=>'Not enough space');
                        }
                        $this->saveFile($f[0], $hash, $uid);
                    }
                }
                $message = (object) array('error_code'=>0,'message'=>'Folder / files moved');
            }
        }
        return $message;
    }

    /**
     * Save filefrom user one to user two 's drectory | must be collabrative
     * @param $hash
     * @param $parent
     * @param $userid
     * @return bool
     */

    public function saveFile($hash,$parent,$userid){
        $today    = date('Ym');
        $u_hash = $this->aauth->get_user_var('user_hash', $userid);
        $u_dir = $this->dirCheck(array('hash'=>$u_hash,'date'=>$today));
        $file = $this->getFile($hash);
        $path = FCPATH.'application/views/uploads/content'.$file->file_path.$file->file_preview;
        $copyname = $this->generateRandomString(15).'.'.$file->file_ext;
        $path_new = $u_dir.'/'.$copyname;
        copy($path,$path_new);
        $hash = $this->generateRandomString(10);
        $file_ = array('file_name'=>$file->file_name,
            'hash'=>$hash
        );
        $file_id = $this->insertFile($this->config_vars['files'],$file_);
        if(is_numeric($file_id)):
            $file_data = array('file_id'=>$file_id,
                'file_author'=>$userid,
                'file_mime'=>$file->file_mime,
                'file_date'=> date('Y-m-d H:i:s'),
                'file_size'=>$file->file_size,
                'image_width'=>$file->image_width,
                'image_height'=>$file->image_height,
                'file_preview'=>$copyname,
                'file_path' => '/'.$u_hash.'/'.$today.'/',
                'file_type'=>$file->file_ext,
                'file_ext'=>$file->file_ext
            );
            $relation = array('author_id'=>$userid,
                'content_id'=>$file_id,
                'content_parent'=>$parent,
                'content_type'=>'file',
                'permission'=>1,
                'owner'=>1);
            $this->insertFile($this->config_vars['files_data'],$file_data);
            $this->insertFile($this->config_vars['relations'],$relation);
            $folder = $parent == '0' ? (object) array('is_shared'=>0): $this->getFolder($parent,$userid);
            if($folder->is_shared == '1'){
                $this->shareFile($hash,$userid,null,2);
            }
      endif;
        return true;
    }

    /**
     * Save folder from user one to user two 's drectory | must be collabrative
     * @param $hash
     * @param $parent
     * @param $userid
     */
    public  function saveFolder($hash,$parent,$userid){
        $folder = $this->getFolder($hash,$userid);
        $created_data = $this->makeFolder($userid,$folder->folder_name,$parent);
        $this->db->select('*');
        $this->db->where('content_parent',$hash);
        $this->db->where('shared',0);
        $result = $this->db->get($this->config_vars['relations'])->result();
        if(!empty($result)){
            foreach($result as $item){
                switch($item->content_type){
                    case 'file':
                        $file = $this->getFile($item->content_id);
                        $this->saveFile($file->hash,$created_data['hash'],$userid);
                    break;
                    case 'folder':
                        $folder = $this->getFolder($item->content_id);
                        $this->saveFolder($folder->folder_hash,$created_data['hash'],$userid);
                    break;
                }
            }
        }
    }

    /**
     * Get folder data by it's id or hash
     * @param $folder_id
     * @param null $uid
     * @param string $includeTrashed
     * @return mixed
     */
    
    public function getFolder($folder_id,$uid = null,$includeTrashed = 'y'){
        $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                 if(is_numeric($folder_id)) {
                     $this->db->where($this->config_vars['folders'] . '.folder_id', $folder_id);
                 } else {
                     $this->db->where($this->config_vars['folders'] . '.folder_hash', $folder_id);
                 }
                if($uid != null){
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                }
                $this->db->where($this->config_vars['relations']. '.content_type','folder');
                if($includeTrashed == 'n'){
                    $this->db->where($this->config_vars['folders'].'.trashed',0);
                }
                $details = $this->db->get($this->config_vars['folders']);     
                return $details->row();
    }

    /**
     * Get file data by it's id or hash
     * @param $file_id int | hash
     * @param null $uid
     * @param string $includeTrashed y/n
     * @return mixed
     */
    public function getFile($file_id,$uid = null,$includeTrashed = 'y'){
        $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
                if(is_numeric($file_id)){
                    $this->db->where($this->config_vars['files'] . '.ID', $file_id);
                } else {
                    $this->db->where($this->config_vars['files'] . '.hash', $file_id);
                }
                if($uid != null){
                $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                }
                $this->db->where($this->config_vars['relations']. '.content_type','file');
                if($includeTrashed == 'n'){
                   $this->db->where($this->config_vars['files'].'.trashed',0);
                }
                $details = $this->db->get($this->config_vars['files']);
                return $details->row();
    }

    /**
     *  Get files by folder hash
     * @param $hash
     * @return mixed
     */
    public function getFilesByFolder($hash)
    {
        $this->db->select('*');
        $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
        $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
        $this->db->where($this->config_vars['relations'] . '.content_parent', $hash);
        $this->db->where($this->config_vars['relations'] . '.content_type', 'file');
        $details = $this->db->get($this->config_vars['files']);
        return $details->result();
    }
    /**
     * Rename file / folder
     * @param array $data
     * @return bool
     */
    public function RenameItem($data = array()){
        switch($data['type']){
            case 'folder':
        $args = array('folder_name'=>$data['name']);
        $this->db->where($this->config_vars['folders'].'.folder_hash',$data['hash']);
        $this->db->update($this->config_vars['folders'],$args);
                return true;
                break;
            case 'file':
                $args = array('file_name'=>$data['name']);
        $this->db->where($this->config_vars['files'].'.hash',$data['hash']);
        $this->db->update($this->config_vars['files'],$args);
                return true;
                break;
            return false;
        }
    }


    /**
     * Delete files / folder permanently
     * @param array $data
     * @return bool
     */
    public function RemovePerma($data = array()){
        if(!empty($data)){
            foreach($data['hash'] as $file){
                $file = explode('/',$file);
                if($file[1] == 'folder'){
                    $user = $data['user'] != 'admin' ? 'AND f.folder_author = '.$data["user"]: '';
                $folder = $this->db->query('SELECT * FROM '.$this->config_vars['folders'].' f WHERE f.folder_hash = "'.$file[0].'" '.$user);
                $folder = $folder->row();
                if(!empty($folder)){
                    $this->UnlinkFiles($file[0]);
                    $this->RemoveFolders($file[0]);
                    $this->db->query('DELETE f,d FROM '.$this->config_vars['folders'].
                             ' f LEFT JOIN '.$this->config_vars['relations'].
                             ' d ON d.content_id = f.folder_id WHERE f.folder_hash = "'.$file[0].'" AND d.content_type = "folder"'
                    );
                }
                } elseif($file[1] == 'file'){
                    $user = $data['user'] != 'admin' ? 'AND d.file_author = '.$data["user"]: '';
                    $item = $this->db->query('SELECT * FROM '.$this->config_vars['files'].
                                             ' f LEFT JOIN '.$this->config_vars['files_data'].
                                             ' d ON d.file_id = f.ID WHERE f.hash = "'.$file[0].'" '.$user
                                            );
                    $item = $item->row();
                    if(!empty($item)){
                    $file_path = FCPATH.'application/views/uploads/content'.$item->file_path.$item->file_preview;
                        if(file_exists($file_path)){
                            unlink($file_path);
                    }
                    $this->db->query('DELETE f,d,r FROM '.$this->config_vars['files'].
                                             ' f LEFT JOIN '.$this->config_vars['files_data'].
                                             ' d ON d.file_id = f.ID LEFT JOIN '.$this->config_vars['relations'].
                                             ' r ON r.content_id = f.ID WHERE f.ID = '.$item->ID.' AND r.content_type = "file"'
                                            );
                    }
                } 
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete files from specific flder.
     * @param $id
     * @param string $type
     */
    public function UnlinkFiles($id,$type = 'content_parent'){
        if($type == 'content_parent') {
            $where = 'WHERE r.content_parent = \'' . $id . '\'';
        } elseif($type == 'file_author') {
            $id = (int)$id;
            $where = 'WHERE d.file_author = ' . $id;
        }
        $files = $this->db->query('SELECT * FROM '.$this->config_vars['files'].
                                             ' f LEFT JOIN '.$this->config_vars['files_data'].
                                             ' d ON d.file_id = f.ID LEFT JOIN '.$this->config_vars['relations'].
                                             ' r ON r.content_id = f.ID '.$where.' AND r.content_type = "file"'
                                            );
        $files = $files->result();
        foreach($files as $key => $file){
                        $file_path = FCPATH.'application/views/uploads/content'.$file->file_path.$file->file_preview;
                        if(file_exists($file_path)){
                            unlink($file_path);
                        }
                        $this->db->query('DELETE f,d,r FROM '.$this->config_vars['files'].
                                             ' f LEFT JOIN '.$this->config_vars['files_data'].
                                             ' d ON d.file_id = f.ID  LEFT JOIN '.$this->config_vars['relations'].' r ON r.content_id = f.ID  WHERE f.ID = '.$file->ID.' AND r.content_type = "file"'
                                            );
        }
    }

    /**
     * Delete folder and it's files / folders from database and directories
     * @param $id
     * @param string $type
     */
    public function RemoveFolders($id,$type = 'content_parent'){
        if($type == 'content_parent') {
            $where = 'WHERE r.content_parent = \'' . $id . '\'';
        } elseif($type == 'folder_author') {
            $id = (int)$id;
            $where = 'WHERE f.folder_author = ' . $id;
        }
        $files = $this->db->query('SELECT * FROM '.$this->config_vars['folders'].' f LEFT JOIN '.$this->config_vars['relations'].
                                  ' r ON r.content_id = f.folder_id '.$where.' AND r.content_type = "folder"');
        $folders = $files->result();
        foreach($folders as $key => $folder){
            $this->UnlinkFiles($folder->folder_hash);
            $this->RemoveFolders($folder->folder_hash);
            $this->db->query('DELETE f,d FROM '.$this->config_vars['folders'].
                             ' f LEFT JOIN '.$this->config_vars['relations'].
                             ' d ON d.content_id = f.folder_id WHERE f.folder_id = '.$folder->folder_id.' AND d.content_type = "folder"'
                             );
        }
    }


    /**
     * Insert file data into database
     * @param $table
     * @param array $data
     * @return mixed
     */
    public function insertFile($table,$data = array()){
        if($this->db->insert($table, $data)):   
        return $this->db->insert_id();
        else:
        return $this->db->_error_message();
        endif;
    }

    /**
     * Generate folder's bread crumbs
     * @param $hash
     */
    public function getFolderCrumb($hash){
 
        $array = $this->generateTreeCrumb($hash);
        $numItems = count($array);
        $crumbs = array();
        for ($i = 0; $i<=$numItems-1; $i++) {
            $crumbs[] = $array[$i];
            }
        }

    /**
     * Generate tree of folders
     * @param $hash
     * @return array
     */
    public function generateTreeCrumb($hash){
        if($hash != '0'){ 
        $folder = $this->db->query("SELECT f.folder_hash,f.folder_name, FROM "
                                    .$this->config_vars['folders']." f LEFT JOIN "
                                    .$this->config_vars['relations']." r ON r.content_id = f.folder_id WHERE f.folder_hash = '$hash'");
        $row = $folder->row();
        } else {
            $row = new stdClass();
            $row->folder_parent = '0';
            $row->folder_name = 'Folders';
        }
        echo $row->folder_parent.'<br><br>';
        $path = array();
        if (!$row->folder_parent == '') {
           
            $path[] = array('name'=>$row->folder_name,'hash'=>$row->folder_hash);
            $path = array_merge($this->generateTreeCrumb($row->folder_parent),$path);
        }
        return $path;
    }

    /**
     * Generate folder's breadcrumbs
     * @param $hash
     * @param $uid
     * @return array
     */
    public function getFolderCrumbs($hash,$uid){
        if($hash != '0'){
            $folder = $this->db->query("SELECT f.folder_hash,f.folder_name,r.content_parent as folder_parent FROM ".$this->config_vars['folders']."   f LEFT JOIN ".$this->config_vars['relations']." r ON r.content_id = f.folder_id WHERE f.folder_hash = '$hash' AND r.author_id = $uid AND r.content_type = 'folder'");
            $row = $folder->result();
        } else {
            $row = array();
        }
        return $row;
    }

    /**
     * Add file to favorites
     * @param $hash
     * @param $uid
     * @param null $remove
     * @return bool
     */
    public function starFile($hash,$uid,$remove = NULL){
        $this->db->select('ID');
        $this->db->where('hash',$hash);
        $file = $this->db->get($this->config_vars['files']);
        $file = $file->row();
        if(!empty($file)){
            if($remove != NULL):
            $data = array('starred'=>0);
            else:
            $data = array('starred'=>1);
            endif;
            $this->db->where('content_id',$file->ID);
            $this->db->where('author_id',$uid);
            $this->db->where('content_type','file');
            $this->db->update($this->config_vars['relations'],$data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add folder to favorites
     * @param $hash
     * @param $uid
     * @param null $remove
     * @return bool
     */
    public function starFolder($hash,$uid,$remove = NULL){
        $this->db->select('folder_id');
        $this->db->where('folder_hash',$hash);
        
        $folder = $this->db->get($this->config_vars['folders']);
        $folder = $folder->row();
        if(!empty($folder)){
            if($remove != NULL):
            $data = array('starred'=>0);
            else:
            $data = array('starred'=>1);
            endif;
            $this->db->where('content_id',$folder->folder_id);
            $this->db->where('author_id',$uid);
            $this->db->where('content_type','folder');
            $this->db->update($this->config_vars['relations'],$data);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Share file with another user.
     * @param $hash
     * @param $uid
     * @param null $from
     * @param $permission
     * @return bool
     */
    public function shareFile($hash,$uid,$from = null,$permission){
        $this->db->select('ID');
        $this->db->where('hash',$hash);
        $file = $this->db->get($this->config_vars['files']);
        $file = $file->row();
        if(!empty($file)){
            $data = array('content_id'=>$file->ID,
                          'content_parent'=>0,
                          'author_id'=>$uid,
                          'content_type'=>'file',
                          'permission'=>$permission,
                          'shared'=>1
                         );
            $this->db->insert($this->config_vars['relations'],$data);
            if($from != null) {
                $note_body = 'Shared file with you';
                $this->addNotification($uid,$from,$note_body,$file->ID,'file');
            }
            $data = array('is_shared'=>1);
            $this->db->where('ID',$file->ID);
            $this->db->update($this->config_vars['files'],$data);
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Share folder and it's child folder/files with another user.
     *
     * @param $hash
     * @param $uid
     * @param $from
     * @param $permission
     * @return bool
     */
    public function shareFolder($hash,$uid,$from,$permission){
        $this->db->select('folder_id,folder_hash');
        $this->db->where('folder_hash',$hash);
        $folder = $this->db->get($this->config_vars['folders']);
        $folder = $folder->row();
        if(!empty($folder)){
            $data = array('content_id'=>$folder->folder_id,
                          'content_parent'=>0,
                          'author_id'=>$uid,
                          'content_type'=>'folder',
                          'permission'=>(int) $permission,
                          'shared'=>1
                         );
            $this->db->insert($this->config_vars['relations'],$data);
            $up = array('is_shared'=>1);
            $this->db->where('folder_hash',$hash);
            $this->db->update($this->config_vars['folders'],$up);
            if($from != null) {
                $note_body = 'Shared folder with you';
                $this->addNotification($uid,$from,$note_body,$folder->folder_id,'folder');
            }
            $this->stampSharedFolder($folder->folder_hash,$uid,$permission);
            $this->stampSharedFile($folder->folder_hash,$uid,$permission);
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param $hash | the unqiue code of folder (required)
     * @param $uid | user id for sharing the folder to specific user
     * @param int $permission
     * @return bool
     */
    public function stampSharedFolder($hash,$uid,$permission = 1){
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','folder');
        $items = $this->db->get($this->config_vars['relations']);
        $items = $items->result();
        foreach($items as $item){
            $this->db->select('*');
            $this->db->join($this->config_vars['relations'],$this->config_vars['relations'].'.content_id = '.$this->config_vars['folders'].'.folder_id');
            $this->db->where($this->config_vars['folders'].'.folder_id',$item->content_id);
            $this->db->where($this->config_vars['relations'].'.author_id',(int) $uid);
            $result = $this->db->get($this->config_vars['folders']);
            $result = $result->row();
            if(!empty($result)) {
                continue;
            }
            $this->db->select('*');
            $this->db->where($this->config_vars['folders'].'.folder_id',$item->content_id);
            $folder = $this->db->get($this->config_vars['folders']);
            $folder = $folder->row();
                $this->stampSharedFolder($folder->folder_hash,$uid);
                $this->stampSharedFile($folder->folder_hash,$uid);
                $data = array('author_id'=>$uid,'content_type'=>'folder','content_parent'=>$hash,'content_id'=>$item->content_id,'shared'=>1,'permission'=>(int) $permission);
                $this->db->insert($this->config_vars['relations'],$data);
                $up = array('is_shared'=>1);
                $this->db->where('folder_hash',$hash);
                $this->db->update($this->config_vars['folders'],$up);

        }
        return true;
    }

    /**
     * Mark the file as shared
     * @param $hash
     * @param $uid | the unqiue code of file (required)
     * @param int $permission | user id for sharing the file to specific user
     * @return bool
     */
    public function stampSharedFile($hash,$uid,$permission = 1){
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','file');
        $items = $this->db->get($this->config_vars['relations']);
        $items = $items->result();
        foreach($items as $item){
            $this->db->select('*');
            $this->db->join($this->config_vars['files_data'],$this->config_vars['files_data'].'.file_id = '.$this->config_vars['files'].'.ID','left');
            $this->db->join($this->config_vars['relations'],$this->config_vars['relations'].'.content_id = '.$this->config_vars['files'].'.ID','left');
            $this->db->where($this->config_vars['files'].'.ID',$item->content_id);
            $this->db->where($this->config_vars['relations'].'.author_id',$uid);
            $file = $this->db->get($this->config_vars['files']);
            $file = $file->row();
            if(!empty($file)) {
                continue;
            }

            $data = array('author_id'=>$uid,'content_type'=>'file','content_parent'=>$hash,'content_id'=>$item->content_id,'shared'=>1,'permission'=>(int) $permission,'owner'=>0);
            $this->db->insert($this->config_vars['relations'],$data);
            $up = array('is_shared'=>1);
            $this->db->where('ID',$item->content_id);
            $this->db->update($this->config_vars['files'],$up);

        }
        return true;
    }
    /**
     * Share file with another user.
     * @param $hash | the unqiue code of file (required).
     * @param $uid | user id for sharing the file to specific user.
     * @return bool
     */
    public function unshareFile($hash,$uid){
        $this->db->select('ID');
        $this->db->where('hash',$hash);
        $file = $this->db->get($this->config_vars['files']);
        $file = $file->row();
        if(!empty($file)){
            $data = array('content_id'=>$file->ID,
                'author_id'=>$uid,
                'content_type'=>'file'
            );
            $this->db->delete($this->config_vars['relations'],$data);
            $this->db->select('author_id');
            $this->db->where('content_id',$file->ID);
            $this->db->where('content_type','file');
            $result = $this->db->get($this->config_vars['relations']);
            if($result->num_rows() == 1) {
                $data = array('is_shared'=>0);
                $this->db->where('ID',$file->ID);
                $this->db->update($this->config_vars['files'],$data);
            }
            return true;
        } else {
            return false;
        }
    }

    /*
     * Share folder and it's child folder/files with another user.
     * @param $hash the unqiue code of folder (required).
     * @param $uid user id for sharing the folder to specific user.
     * @param $from user id of who is shared this folder.
     */
    /**
     *  Share folder and it's child folder/files with another user.
     * @param $hash
     * @param $uid
     * @return bool
     */
    public function unshareFolder($hash,$uid){
        $this->db->select('folder_id,');
        $this->db->where('folder_hash',$hash);
        $folder = $this->db->get($this->config_vars['folders']);
        $folder = $folder->row();
        if(!empty($folder)){
            $data = array('content_id'=>$folder->folder_id,
                'author_id'=>$uid,
                'content_type'=>'folder'
            );
            $this->db->delete($this->config_vars['relations'],$data);
            $this->unstampSharedFolder($hash,$uid);
            $this->unstampSharedFile($hash,$uid);
            $this->db->select('author_id');
            $this->db->where('content_id',$folder->folder_id);
            $this->db->where('content_type','folder');
            $result = $this->db->get($this->config_vars['relations']);
            if($result->num_rows() == 1) {
                $data = array('is_shared'=>0);
                $this->db->where('folder_id',$folder->folder_id);
                $this->db->update($this->config_vars['folders'],$data);
            }
            return true;
        } else {
            return false;
        }
    }
    /*
     * Mark the folder as shared
     * @param $hash the unqiue code of folder (required)
     * @param $uid user id for sharing the folder to specific user
     */

    public function unstampSharedFolder($hash,$uid){
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','folder');
        $items = $this->db->get($this->config_vars['relations']);
        $items = $items->result();
        foreach($items as $item){
            $this->db->select('folder_hash,folder_id');
            $this->db->where('folder_id',$item->content_id);
            $folder = $this->db->get($this->config_vars['folders']);
            $folder = $folder->row();
            $this->unstampSharedFolder($folder->folder_hash,$uid);
            $this->unstampSharedFile($folder->folder_hash,$uid);
            $data = array('author_id'=>$uid,'content_type'=>'folder','content_parent'=>$hash,'content_id'=>$item->content_id);
            $this->db->delete($this->config_vars['relations'],$data);
            $this->db->delete($this->config_vars['relations'],$data);
            $this->db->select('author_id');
            $this->db->where('content_id',$folder->folder_id);
            $this->db->where('content_type','folder');
            $result = $this->db->get($this->config_vars['relations']);
            if($result->num_rows() == 1) {
                $data = array('is_shared'=>0);
                $this->db->where('folder_id',$folder->folder_id);
                $this->db->update($this->config_vars['folders'],$data);
            }
        }
        return true;
    }

    /*
     * Mark the file as shared
     * @param $hash the unqiue code of file (required)
     * @param $uid user id for sharing the file to specific user
     */

    public function unstampSharedFile($hash,$uid) {
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','file');
        $items = $this->db->get($this->config_vars['relations']);
        $items = $items->result();
        foreach($items as $item){
            $data = array('author_id'=>$uid,'content_type'=>'file','content_parent'=>$hash,'content_id'=>$item->content_id);
            $this->db->delete($this->config_vars['relations'],$data);
            $this->db->delete($this->config_vars['relations'],$data);
            $this->db->select('author_id');
            $this->db->where('content_id',$item->content_id);
            $this->db->where('content_type','file');
            $result = $this->db->get($this->config_vars['relations']);
            if($result->num_rows() == 1) {
                $data = array('is_shared'=>0);
                $this->db->where('ID',$item->content_id);
                $this->db->update($this->config_vars['files'],$data);
            }
        }
        return true;
    }

    /*
     * Get users connected to a specific file
     * @param $hash the unqiue code of file (required)
     */
    public function getFileUsers($hash){
       $file = $this->db->query("SELECT ID FROM ".$this->config_vars['files']." f WHERE hash = '".$hash."'");
       $file = $file->row();
       if(!empty($file)): 
       $file_id = $file->ID;
       $users = $this->db->query("SELECT u.id as user_id ,u.name,u.email,r.owner, r.permission,r.content_parent,r.author_id FROM ".$this->config_vars['users']."
                                 u LEFT JOIN ".$this->config_vars['relations'].
                                 " r ON r.author_id = u.id WHERE r.content_id = ".$file_id." AND r.content_type = 'file'");
        $users = $users->result();
        $usersdata = array();
        $u = 0;
        foreach($users as $user){
            $userdata[$u] = $user; 
            $this->db->select('value');
            $this->db->where('data_key','user_hash');
            $this->db->where('user_id',$user->user_id);
            $hash = $this->db->get($this->config_vars['user_variables']);
            $hash = $hash->row()->value;
            $userdata[$u]->hash = $hash;
            $u++;
        }
        return (object) $userdata;
        endif;
    }

    /*
     * Get users connected to a specific folder
     * @param $hash the unqiue code of folder (required)
     */

    public function getFolderUsers($hash){
       $folder = $this->db->query("SELECT folder_id FROM ".$this->config_vars['folders']." f WHERE folder_hash = '".$hash."'");
       $folder = $folder->row();
       if(!empty($folder)): 
       $folder_id = $folder->folder_id;
       $users = $this->db->query("SELECT u.id as user_id ,u.name,u.email,r.owner, r.permission,r.content_parent FROM ".$this->config_vars['users']."
                                 u LEFT JOIN ".$this->config_vars['relations'].
                                 " r ON r.author_id = u.id WHERE r.content_id = ".$folder_id." AND r.content_type = 'folder'");
        $users = $users->result();
        $usersdata = array();
        $u = 0;
        foreach($users as $user){
            $userdata[$u] = $user; 
            $this->db->select('value');
            $this->db->where('data_key','user_hash');
            $this->db->where('user_id',$user->user_id);
            $hash = $this->db->get($this->config_vars['user_variables']);
            $hash = $hash->row()->value;
            $userdata[$u]->hash = $hash;
            $u++;
        }
        return (object) $userdata;
        endif;
    }

    /*
     * Get notifications
     * @param $uid is user id of whom we need the notifications for (required)
     * @param $type could be get|post (required)
     */
    public function getNotes($uid,$type,$term = null){
        switch ($type){
            case 'get':
                $this->db->select('*');
                $this->db->where('is_to',$uid);
                if($term != null && $term == 'n'){
                    $this->db->where('is_notified',0);
                } elseif($term != null && $term == 'u'){
                    $this->db->where('is_read',0);
                }
                $this->db->order_by('id','DESC');
                $notes = $this->db->get($this->config_vars['notifications'])->result();
                $notifications = array();
                foreach($notes as $note) {
                    $notifications[$note->id]['note'] = $note;
                    if($term == 'n'){
                        $data = array('is_notified'=>1);
                        $this->db->where('is_notified',0);
                        $this->db->where('is_to',$uid);
                        $this->db->update($this->config_vars['notifications'],$data);
                    }

                    if ($note->is_from != NULL) {
                        $user = $this->aauth->get_user($note->is_from);
                        $notifications[$note->id]['user'] = $user;
                    }
                }
                return $notifications;
            break;
        }
    }

    public function deleteNote($id,$term = 'single'){
        $this->db->select('is_to');
        if($term == 'single') {
            $this->db->where('id', $id);
        } elseif($term == 'multi'){
            $this->db->where('is_to', $id);
        }
        $result = $this->db->get($this->config_vars['notifications'])->row();
        if(!empty($result) && $result->is_to == $this->aauth->get_user()->id) {
            if($term == 'single') {
                return $this->db->delete($this->config_vars['notifications'], array('id' => $id));
            } elseif($term == 'multi'){
                return $this->db->delete($this->config_vars['notifications'], array('is_to' => $id));
            }
            } else {
            return true;
        }
        return false;
    }

    public function markRead($uid){
        $data = array('is_read'=>1);
        $this->db->where('is_to',$uid);
        $this->db->update($this->config_vars['notifications'],$data);
    }

    public function Binsert($table,$data){
        return $this->db->insert($table,$data);
    }
    public function addPage($data = array()){
        if(!empty($data)){
            $data = array(
                'page_name'=>$data['pageTitle'],
                'page_slug'=>$data['pageSlug'],
                'page_body'=>$data['pageBody'],
                'page_status'=>$data['pageStatus'],
                'in_footer'=>$data['inFooter'],
                'in_sitemap'=>$data['inSitemap']);
            $this->db->insert($this->config_vars['pages'],$data);
            return true;
        }
        return false;
    }
    public function savePage($data = array()){
        if(!empty($data)){
            $data_u = array(
                'page_name'=>$data['pageTitle'],
                'page_slug'=>$data['pageSlug'],
                'page_body'=>$data['pageBody'],
                'page_status'=>$data['pageStatus'],
                'in_footer'=>$data['inFooter'],
                'in_sitemap'=>$data['inSitemap']);
            $this->db->where('id',(int) $data['pageID']);
            $this->db->update($this->config_vars['pages'],$data_u);
            return true;
        }
        return false;
    }

    public function getPages($all = null,$offset = FALSE,$limit = FALSE){
        $this->db->select('id,page_name,page_slug,page_status,page_position');

        if($all == null)
        $this->db->where('page_status',1);

        $this->db->where('in_footer',1);
        if ($limit) {

            if ($offset == FALSE)
                $this->db->limit($limit);
            else
                $this->db->limit($limit, $offset);
        }

        $query = $this->db->get($this->config_vars['pages']);
        $pages = $query->result();
        return $pages;
    }

    public function getPage($target,$what = null){
        $this->db->select('id,page_name,page_slug,page_status,page_position,page_body,in_footer,in_sitemap');

        if($what == null):
            $this->db->where('page_slug',$target);
        else:
            $this->db->where($what,$target);
        endif;

        $query = $this->db->get($this->config_vars['pages']);
        $pages = $query->row();
        return $pages;
    }

    public function getStats($term = 'files',$num = '- 30 days'){
        $num = date('Y-m-d', strtotime($num));
        $date = date('Y-m-d');
        if($term == 'files'):
                $result = $this->db->query("SELECT DATE(d.file_date) as date,COUNT(*) as count FROM ".$this->config_vars['files']." f LEFT JOIN ".$this->config_vars['files_data']." d ON d.file_id = f.ID WHERE DATE(d.file_date) BETWEEN '".$num."' AND '".$date."' GROUP BY DATE(d.file_date)");
                $result = $result->result();
                $dates = array();
                $counts = array();
                foreach($result as $r){
                    $dates[] = $r->date;
                    $counts[] = (int) $r->count;
                }
                return array('dates'=>$dates,'counts'=>$counts);
         elseif($term == 'folder'):
                 $result = $this->db->query("SELECT DATE(f.date) as date,COUNT(*) as count FROM ".$this->config_vars['folders']." f WHERE DATE(f.date) BETWEEN '".$num."' AND '".$date."' GROUP BY DATE(f.date)");
                 $result = $result->result();
                 $dates = array();
                 $counts = array();
                 foreach($result as $r){
                     $dates[] = $r->date;
                     $counts[] = (int) $r->count;
                 }
                 return array('dates'=>$dates,'counts'=>$counts);
        endif;
    }
    public function makeFolder($uid,$fname,$fin){
        $hash = $this->generateRandomString();
        $fin = (string) $fin;
        $today = date('Y-m-d H:i:s');
        if($fin != '0') {
            $this->db->select('*');
            $this->db->where('folder_hash', $fin);
            $folder = $this->db->get($this->config_vars['folders']);
            $folder = $folder->row();
        } else {
            $folder = (object) array('folder_hash'=>0,'is_shared'=>0);
        }
        $data = array('folder_name'=>$fname,'folder_hash'=>$hash,'folder_author'=>$uid,'date'=>$today);
        if($this->db->insert($this->config_vars['folders'], $data)):
            $createdid = $this->db->insert_id();
            $data = array('author_id'=>$uid,
                'content_id'=>$createdid,
                'content_parent'=>$folder->folder_hash,
                'content_type'=>'folder',
                'owner'=>1,
                'permission'=>1);

            if($this->db->insert($this->config_vars['relations'], $data)):
                if($folder->is_shared == 1) {
                    $this->db->select('*');
                    $this->db->where('content_parent', $folder->folder_hash);
                    $users = $this->db->get($this->config_vars['relations']);
                    $users = $users->result();
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            if ($user->author_id == $uid)
                                continue;

                            $data = array('author_id' => $user->author_id,
                                'content_id' => $createdid,
                                'content_parent' => $folder->folder_hash,
                                'content_type' => 'folder',
                                'owner' => 0,
                                'shared' => 1,
                                'permission' => 2);
                            $this->db->insert($this->config_vars['relations'], $data);
                        }
                    }
                }
                return array('hash'=>$hash);
            else:
                return $this->db->_error_message();
            endif;
        else:
            return $this->db->_error_message();
        endif;
    }

    public function generateRandomString($length = 15)
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function dirCheck($dir){
        if (!is_dir(FCPATH.'application/views/uploads/content/' . $dir['hash'])):
            mkdir(FCPATH.'application/views/uploads/content/' . $dir['hash'], 0777, true);
        endif;
        if(!is_dir(FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'])):
            mkdir(FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'],0777,true);
        endif;
        return FCPATH.'application/views/uploads/content/'.$dir['hash'].'/'.$dir['date'];
    }

    public function addNotification($is_to = null,$is_from = null,$body,$content_id,$content_type){
        $timestamp = date('Y-m-d H:m:s');
        $data = array('is_to'=>$is_to,'is_from'=>$is_from,'content_id'=>$content_id,'content_type'=>$content_type,'body'=>$body,'created_at'=>$timestamp);
        $this->db->insert($this->config_vars['notifications'],$data);
    }

    /**
     * Calculate all the files and folder's size inside an folder and return it's total size
     * @param $hash
     * @return int
     */
    public function folderSize($hash) {
        $size = 0;
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->group_by('content_id');
        $items = $this->db->get($this->config_vars['relations'])->result();
        foreach($items as $item){
            switch($item->content_type){
                case 'file':
                    $file = $this->getFile($item->content_id,null,'n');
                    if(!empty($file)) {
                        $size = $size + $file->file_size;
                    } else {
                        $size = $size + 0;
                    }
                break;
                case 'folder':
                    $folder = $this->getFolder($item->content_id,null,'n');
                    if(!empty($folder)){
                        $size   = $this->folderSize($folder->folder_hash);
                    } else {
                        $size   = $size + 0;
                    }
                break;
            }
        }
        return $size;
    }

    /**
     * Get list of folders and
     * @param $hash
     * @return array
     */
    public function folderFileList($hash) {
        $items_ = array();
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','folder');
        $this->db->group_by('content_id');
        $items = $this->db->get($this->config_vars['relations'])->result();
        if(!empty($items)) {
            foreach ($items as $item) {
                $this->db->select('folder_hash');
                $this->db->where('folder_id',$item->content_id);
                $folder = $this->db->get($this->config_vars['folders'])->row();
                if (!empty($folder)) {
                    $items_[] = $folder->folder_hash;
                }
            }
        }
        return $items_;
    }

    public function HaveSubs($hash){
        $this->db->select('content_id');
        $this->db->where('content_parent',$hash);
        $this->db->where('content_type','folder');
        $result = $this->db->get($this->config_vars['relations'])->result();
        if(!empty($result)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove a page from database
     * @param $id
     * @return mixed
     */
    public function removePage($id){
        return $this->db->delete($this->config_vars['pages'],array('id'=>$id));
    }

    /**
     * Get Total size for a directory.
     * @param $path
     * @return mixed
     */
    public function getDirectorySize($path)
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
     * Get used space for speicific user with different formats
     * @param null $uid
     */
    public function userSpace($uid = null){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $data['allowed_size'] = $this->sizeFormat($this->site->upload_limit);
        $data['occupied']     = $this->userUsedSpace(null,1).' / '.$data['allowed_size'];
        $data['occupied_p']   = $this->userUsedSpaceP();
        $data['occupied_bytes'] = $this->userUsedSpace(null,0);
        echo json_encode($data);
    }

    /**
     * Get total used space for a speicific user
     * @param null $uid
     * @param int $term
     * @return string
     */
    public function userUsedSpace($uid = null,$term = 1){
        $uid = $uid == null ? $this->aauth->get_user()->id : $uid;
        $u_hash = $this->aauth->get_user_var('user_hash', $uid);
        $size = $this->getDirectorySize(FCPATH.'application/views/uploads/content/'.$u_hash)['size'];
        if($term == 1){
            return $this->sizeFormat($size);
        } elseif($term ==0){
            return $size;
        }
    }

    /**
     * Return the bytes in readable size
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
     * Add password to a content
     * @param $item
     * @param $password
     * @return bool
     */

    public function protectItem($item,$password){
        if(!empty($password) && $password != NULL) {
            $pass = md5($password);
        } else {
            $pass = NULL;
        }
        $item = explode('/',$item);
        if($item[1] == 'file'){
            $file = $this->getFile($item[0]);
            if(!empty($file)){
                $data = array('password'=>$pass);
                $this->db->where('ID',$file->ID);
                $this->db->update($this->config_vars['files'],$data);
            }
            return true;
        } elseif($item[1] == 'folder') {
            $folder = $this->getFolder($item[0]);
            if(!empty($folder)){
                $data = array('password'=>$pass);
                $this->db->where('folder_id',$folder->folder_id);
                $this->db->update($this->config_vars['folders'],$data);
            }
            return true;
        }
        return false;
    }

    /**
     * Validate the file password if correct or not correct
     * @param $hash
     * @param $type
     * @param $password
     * @return bool
     */
    public function checkPassword($hash,$type,$password){
        $pass = md5($password);
        if($type == 'file'){
            $file = $this->getFile($hash);
            if(!empty($file)){
                if($file->password == $pass){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
            return true;
        } elseif($type == 'folder') {
            $folder = $this->getFolder($hash);
            if(!empty($folder)){
                if($folder->password == $pass){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Change user's permission for collaborative content
     * @param $user
     * @param $file
     * @param $type
     * @param $permission
     * @return bool
     */
    public function changePer($user,$file,$type,$permission){
        $item = $type == 'file' ? $this->getFile($file): $this->getFolder($file);
        $content_id = $type == 'file' ? $item->ID : $item->folder_id;
        $user_ = $this->aauth->get_user_id_by_hash($user);
        $this->db->select('author_id');
        $this->db->where('author_id',$this->aauth->get_user()->id);
        $this->db->where('content_id',$content_id);
        $this->db->where('content_type',$type);
        $this->db->where('permission',1);
        $result = $this->db->get($this->config_vars['relations'])->row();
        if(!empty($result)){
            $data = array('permission'=>$permission);
            $this->db->where('content_id',$content_id);
            $this->db->where('author_id',$user_);
            $this->db->where('content_type',$type);
            $this->db->update($this->config_vars['relations'],$data);
            return true;
        } else {
            return false;
        }
    }
}