<?php
error_reporting(1);
defined('BASEPATH') OR exit('No direct script access allowed');

class BUCKTY_Content extends BUCKTY_User {

    public $site;
    
    public $config_vars;
    
    public $current_user;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('BucktySettings');
        $this->load->model('BucktyContent');
        $this->config->load('buckty');
		$this->config_vars = $this->config->item('buckty');
        $this->site = (object) $this->BucktySettings->LoadSettings();
        $this->current_user = $this->logged_data();
    }
    
    public function getFolders($uid,$inId = null,$term = null){
        $data = $this->BucktyContent->GetContent('folder',$inId,$uid,$term);
        return $data;
    }
    public function getTreeFolders($uid,$inId = null,$term = null){
        $data = $this->BucktyContent->GetTree($uid);
        return $data;
    }
    public function getFiles($uid = null,$inId = null,$term = null){
        $data = $this->BucktyContent->GetContent('files',$inId,$uid,$term);
        return $data;
    }
     public function getTrashFolders($uid,$inId = null){
        $data = $this->BucktyContent->GetTrash('folder',$inId,$uid);
        return $data;
    }
    
    public function getTrashFiles($uid,$inId = null){
        $data = $this->BucktyContent->GetTrash('files',$inId,$uid);
        return $data;
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
    public function actionFile($hash,$action){
        $this->db->select('d.file_author,f.ID');
        $this->db->from($this->config_vars['files'].' f');
        $this->db->join($this->config_vars['files_data'].' d','d.file_id = f.ID');
        $this->db->where('f.hash',$hash);
        $file = $this->db->get();
        $file = $file->row();
        if($file->file_author == $this->current_user->id) {
            $data = $action == 'restore' ? array('trashed' => 0) : array('trashed' => 1);
            $this->db->where('hash', $hash);
            $this->db->update($this->config_vars['files'], $data);
        } elseif($file->file_author != $this->current_user->id && $action == 'remove') {
            $this->BucktyContent->unshareFile($hash,$this->current_user->id);
        }
        return true;
    }
    public function actionFolder($hash,$action){
        $this->db->select('f.folder_author,f.folder_id');
        $this->db->from($this->config_vars['folders'].' f');
        $this->db->where('f.folder_hash',$hash);
        $folder = $this->db->get();
        $folder = $folder->row();
        if($folder->folder_author == $this->current_user->id) {
            $data = $action == 'restore' ? array('trashed' => 0) : array('trashed' => 1);
            $this->db->where('folder_hash', $hash);
            $this->db->update($this->config_vars['folders'], $data);
        } elseif($folder->folder_author != $this->current_user->id && $action == 'remove'){
            $this->BucktyContent->unshareFolder($hash,$this->current_user->id);
            $this->db->delete($this->config_vars['relations'],array('content_id'=>$folder->folder_id,'author_id'=>$this->current_user->id,'content_type'=>'folder'));
        }
        return true;
    }
    
    public function getFile($file_id,$uid = null){
        return $this->BucktyContent->getFile($file_id,$uid);
    }
    
    public function getFolder($folder_id,$uid = null){
        return $this->BucktyContent->getFolder($folder_id,$uid);
    }
    public function getFolderCrumb($hash){
        return $this->BucktyContent->getFolderCrumb($hash);
    } 
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
    public function userUsedSpace($uid = null,$term = 1){
        $uid = $uid == null ? $this->current_user->id : $uid;
        $u_hash = $this->aauth->get_user_var('user_hash', $uid);
        $size = $this->getDirectorySize(FCPATH.'application/views/uploads/content/'.$u_hash)['size'];
        if($term == 1){
            return $this->sizeFormat($size);
        } elseif($term == 0){
            return $size;
        }
    }

    public function userUsedSpaceP($uid = null){
        $uid = $uid == null ? $this->current_user->id : $uid;
        $u_hash = $this->aauth->get_user_var('user_hash', $uid);
        $size = $this->getDirectorySize(FCPATH.'application/views/uploads/content/'.$u_hash)['size'];
        $percentage = ($size / $this->site->upload_limit) * 100;
        $total = $percentage > 100 ? 100 : $percentage;
        return $total;
    }
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
    
    public function resize($image_path,$width, $height)
    {   
        $config['image_library'] = 'gd2';
        $config['source_image'] = $image_path;
        $config['dynamic_output'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $width;
        $config['height'] = $height;

        $this->load->library('image_lib', $config); 
        $this->image_lib->initialize($config);
        return $this->image_lib->resize();
    }
    
}