<?php

/**
 * Class BucktyContent
 */
class BucktyApi extends CI_Model
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
        $this->CI->config->load('buckty');
        $this->config_vars = $this->CI->config->item('buckty');
    }

    /**
     *  Check if auth key and user key are valid or not
     * @access public
     * @param $authorization | authorization key
     * @param $user_key | user's hash
     * @return bool
     */
    public function checkAuth($authorization, $user_key)
    {
        $this->db->select('authorization_key,user_key');
        $this->db->where('absolute_key', $authorization);
        $this->db->where('user_key', $user_key);
        $result = $this->db->get($this->config_vars['api'])->row();
        if ($result) {
            if ($result->authorization_key . $result->user_key == $authorization) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     *
     * @param $key
     * @param $userHash
     */
    public function InsertUserApi($key, $userHash)
    {
        $data = array('authorization_key' => $key, 'user_key' => $userHash, 'absolute_key' => $key . $userHash, 'creation_date' => date('Y-m-d H:m:i'));
        $this->db->insert($this->config_vars['api'], $data);
    }

    /**
     * Get specific user 's api key.
     * @param $userHash
     * @return bool
     */
    public function GetUserApi($userHash)
    {
        $this->db->select('absolute_key');
        $this->db->where('user_key', $userHash);
        $key = $this->db->get($this->config_vars['api'])->row();
        if (!empty($key) || $key != NULL) {
            return $key->absolute_key;
        } else {
            return false;
        }
    }

    /**
     * Generate api and insert into database
     * @param $key
     * @param $ip
     */
    public function ApiLogInsert($key,$ip){
        $data = array('absolute_key'=>$key,'ip_address'=>$ip,'access_date'=>date('Y-m-d H:m:i'));
        $this->db->insert($this->config_vars['api_log'],$data);
    }

    /**
     * Get dashboard content for user | provided user with various other parameters
     * @param null $type | required
     * @param null $inId | optional
     * @param null $uid | optional
     * @param null $term | optional
     * @return object
     */
    public function GetContent($type = null, $inId = null, $uid = null, $term = null)
    {
        $inId = (string)$inId;
        $order = !empty($_COOKIE['order']) ? $_COOKIE['order'] : null;
        $order_in = !empty($_COOKIE['order']) ? $_COOKIE['order_in'] : null;
        switch ($type) {
            case 'folder':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                if ($inId != null && $term == null):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', $inId);
                endif;
                if ($term == 'shared'):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', '0');
                endif;
                $this->db->where($this->config_vars['folders'] . '.trashed', 0);
                if ($uid != null):
                    $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                endif;
                $this->db->where($this->config_vars['relations'] . '.content_type', 'folder');
                switch ($term) {
                    case 'starred':
                        $this->db->where($this->config_vars['relations'] . '.starred', 1);
                        break;
                    case 'shared':
                        $this->db->where($this->config_vars['relations'] . '.shared', 1);
                        break;
                    case 'recent':
                        $this->db->where($this->config_vars['relations'] . '.shared', 0);
                        $this->db->order_by($this->config_vars['folders'] . '.date', 'DESC');
                        break;
                    default:
                        $this->db->like($this->config_vars['folders'] . '.folder_name', $term);

                }
                $details = $this->db->get($this->config_vars['folders']);
                $fetchedFolders = $details->result();
                $folders = array();
                foreach ($fetchedFolders as $folder) {
                    $this->db->select('*');
                    $this->db->join($this->config_vars['relations'],
                        $this->config_vars['relations'] .
                        '.content_id = ' . $this->config_vars['folders'] . '.folder_id', 'left');
                    $this->db->where($this->config_vars['relations'] . '.content_parent', $folder->folder_hash);
                    $r = $this->db->get($this->config_vars['folders']);
                    $r = $r->result();
                    $folder->has_sub = !empty($r) ? 1 : 0;
                    $folder->root = $folder->content_parent == 0 ? 1 : 0;
                    $folders[] = $folder;
                }
                return (object)$folders;
                break;
            case 'files':
                $this->db->select('*');
                $this->db->join($this->config_vars['relations'], $this->config_vars['relations'] . '.content_id = ' . $this->config_vars['files'] . '.ID', 'left');
                $this->db->join($this->config_vars['files_data'], $this->config_vars['files_data'] . '.file_id = ' . $this->config_vars['files'] . '.ID', 'left');
                if ($inId != null && $term == null):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', $inId);
                endif;
                if ($term == 'shared'):
                    $this->db->where($this->config_vars['relations'] . '.content_parent', '0');
                endif;
                if ($uid != null):
                    $this->db->where($this->config_vars['relations'] . '.author_id', $uid);
                endif;
                $this->db->where($this->config_vars['files'] . '.trashed', 0);
                $this->db->where($this->config_vars['relations'] . '.content_type', 'file');
                switch ($term) {
                    case 'starred':
                        $this->db->where($this->config_vars['relations'] . '.starred', 1);
                        break;
                    case 'shared':
                        $this->db->where($this->config_vars['relations'] . '.shared', 1);
                        break;
                    case 'recent':
                        $this->db->where($this->config_vars['relations'] . '.shared', 0);
                        $this->db->order_by($this->config_vars['files_data'] . '.file_date', 'DESC');
                        break;
                    default:
                        $this->db->like($this->config_vars['files'] . '.file_name', $term);

                }
                if ($order != null) {
                    $this->db->order_by($order, $order_in);
                }
                $this->db->group_by($this->config_vars['relations'] . '.content_id');
                $details = $this->db->get($this->config_vars['files']);
                $files = $details->result();
                $files_ = array();
                foreach ($files as $file) {
                    $file->absoluteUrl = base_url() . 'userfile/' . $file->hash;
                    $file->getfileUrl = base_url() . 'useraction/get/' . $file->hash;
                    $files_[] = $file;
                }
                return (object)$files_;
                break;

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

}