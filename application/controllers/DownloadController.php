<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class DownloadController
 */
class DownloadController extends BUCKTY_Content
{
    /**
     * @var
     */
    public $site;

    /**
     * @var array
     */
    public $current_user = array();

    /**
     * @var
     */
    private $csrf_token;

    /**
     * @var bool|string
     */
    private $date;

    /**
     * @var ZipArchive
     */
    private $zip_;

    /**
     * @var
     */
    private $zipunqiue;

    /**
     * DownloadController constructor.
     */
    public function __construct()
    {
        /**
         * Parent class constructor.
         */
        parent::__construct();
        $this->load->helper('download');
            $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        $this->date = date('Y-m-d');
        $this->zip_ = new ZipArchive;
        $this->zipunique = rand();
    }

    /**
     * Download single item.
     */
    public function DownloadItem(){
        $file_code = $this->uri->segment(3, 0);
        $file = $this->BucktyContent->GetFile($file_code);
        if(empty($file)):
            $message = array('message'=>tran($this->trans->invalid_file));
            GenerateMsg($message,1);
            exit();
        endif;
        $item = FCPATH.'application/views/uploads/content'.$file->file_path.$file->file_preview;
        if(file_exists($item)) {
            $fileName = basename($item);
            $data = file_get_contents($item);
            force_download($file->file_name,$data);
            if($this->input->is_ajax_request()) {
                exit();
            } else {
                exit('<script=javascript>window.close();</script>');
            }
        } else {
            $message = array('message'=>tran($this->trans->invalid_folder_or_file));
            GenerateMsg($message,1);
        }
    }

    /**
     * Download zip file.
     */
    public function DownloadZip(){
        $name = $this->uri->segment(3, 0);
        $path = FCPATH.'temp/'.$name;
        if(!file_exists($path)){
            echo '404 not found';
        } else {
            $fileName = basename($path);
            $data = file_get_contents($path);
            unlink($path);
            force_download($fileName,$data);
            exit('<script=javascript>window.close();</script>');
        }
    }

    /**
     * Start creating zip for multiple files and folder selection.
     *
     */
    public function DownloadMulti(){
        if ( ! (_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $hashes = $this->input->post('items');

        if($hashes == NULL) {
            $url = array('message' => tran($this->trans->Invalid_folder), 'zipped' => 0);
            echo json_encode($url);
            exit();
        }

            $name = 'Buckty_' . $this->date . '_' . $this->zipunique  . '.zip';
            $path = FCPATH . 'temp/' . $name;
            if ($this->zip_->open($path, ZipArchive::CREATE) === TRUE) {
                foreach ($hashes as $hash) {
                    $code = explode('/', $hash);
                    if ($code[1] == 'file') {
                        $this->addFile($code);
                    } elseif ($code[1] == 'folder' || $code[1] == 'folder_view') {
                        $this->addFolder($code);
                    }
                }
                $this->GetZip();
                $this->zip_->close();
            }
    }


    /**
     * Add file to downloadable zip
     * @param $code
     * @param null $dir
     * @return bool
     */
    private function addFile($code,$dir = null){
        if(is_array($code)) {
            $file = $this->BucktyContent->getFile($code[0]);
        } else {
            $file = $this->BucktyContent->getFile($code);
        }
        if(!empty($file)):
            $item = FCPATH.'application/views/uploads/content'.$file->file_path.$file->file_preview;
            $this->zip_->addFile($item,$dir.$file->file_name.'.'.$file->file_ext);
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Add empty folder to zip.
     * @param $code
     * @param null $infolder
     * @return bool
     */
    private function addFolder($code,$infolder = null)
    {
        $this->db->select('*');
        $this->db->where('folder_hash', $code[0]);
        $folder = $this->db->get($this->config_vars['folders']);
        $mainf = $folder->row();
        $this->db->select('content_id,content_type');
        $this->db->where('content_parent', $code[0]);
        $items = $this->db->get($this->config_vars['relations']);
        $items = $items->result();
        foreach ($items as $item) {
            if ($item->content_type == 'file') {
                $this->db->select('hash');
                $this->db->where('ID', $item->content_id);
                $item_in = $this->db->get($this->config_vars['files']);
                $item_in = $item_in->row();
                $this->addFile($item_in->hash, $infolder . $mainf->folder_name . '/');
            } elseif($item->content_type == 'folder' and $code[1] != 'folder_view'){
                $this->db->select('folder_hash');
                $this->db->where('folder_id', $item->content_id);
                $item_in = $this->db->get($this->config_vars['folders']);
                $item_in = $item_in->row();
                $this->addFolder($item_in->folder_hash, $infolder . $mainf->folder_name . '/');
            }
        }
        return true;
    }

    /**
     * echo zip's name.
     *
     */
    private function GetZip(){
        $name = 'Buckty_'.$this->date.'_'.$this->zipunique.'.zip';
        $url = array('name'=>$name,'zipped'=>1);
        echo json_encode($url);
    }
    
}