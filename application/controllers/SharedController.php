<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class SharedController
 */
class SharedController extends BUCKTY_Content
{

    /**
     * @var
     */
    public $site;

    /**
     * SharedController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * View file or folder into shared view by its link.
     */
    public function View(){
        $type = $this->uri->segment(2,0);
        $hash = $this->uri->segment(3,0);
        $password = $this->input->post('password');
        $correctPassword = false;
        $logged_user = $this->is_loggedin() == FALSE ? NULL: $this->current_user->id;
        if($password != NULL || ''):
            $correctPassword = $this->BucktyContent->checkPassword($hash,$type,$password);
            if(!$correctPassword){
                $message = array('error_code'=>1,'message'=>'incorrect password, try again');
                echo json_encode($message);
                exit();
            }
        endif;
        switch($type){
            case 'folder':
                $data         = array();
                $item         = $this->BucktyContent->getFolder($hash);
                if(!empty($item)):
                    $data['item'] = $item;
                    $data['files'] = $this->getFiles(null,$hash);
                    $data['type_item'] = 'folder';
                    $data['type'] = 'folder_view';
                    $data['hash'] = $hash;
                    $data['type_image'] = $this->site->site_url.'assets/img/document.jpg';
                    $data['type_content'] = 'folder';
                    //var_dump($this->current_user);
                    if(($item->password != '' || NULL) && !$correctPassword && $logged_user != $item->author_id):
                        $this->load->view('shared/protected', $data);
                    else:
                        $this->load->view('shared/view', $data);
                    endif;
                else:
                    redirect($this->site->site_url);
                endif;
            break;
            case 'file':
                $data         = array();
                $item         = $this->BucktyContent->getFile($hash);
                if(!empty($item)):
                $users_data   = $this->BucktyContent->getFileUsers($hash);
                
                $data['item'] = $item;
                $textTypes = "application/pdf application/vnd.oasis.opendocument.text application/vnd.oasis.opendocument.text-flat-xml application/vnd.oasis.opendocument.text-template application/vnd.oasis.opendocument.presentation application/vnd.oasis.opendocument.presentation-flat-xml application/vnd.oasis.opendocument.presentation-template application/vnd.oasis.opendocument.spreadsheet application/vnd.oasis.opendocument.spreadsheet-flat-xml application/vnd.oasis.opendocument.spreadsheet-template application/vnd.ms-office application/msword application/vnd.ms-excel";
                $data['texttypes'] = explode(' ',$textTypes);
                $type = explode('/',$item->file_mime);
                $data['type_content'] = $type[0];
                    switch($type[0]) {
                        case 'image':
                            $data['type_image'] = $this->site->site_url.'userfile/'.$item->hash;
                            break;
                        case 'text':
                            $data['type_image'] = $this->site->site_url . 'assets/img/text.jpg';
                            break;
                        case 'video':
                            $data['type_image'] = $this->site->site_url . 'assets/img/video.jpg';
                            break;
                        case 'application':
                            switch ($type[1]){
                                case 'zip':
                                    $data['type_image'] = $this->site->site_url . 'assets/img/zip.jpg';
                                    break;
                                default:
                                    $data['type_image'] = $this->site->site_url . 'assets/img/document.jpg';
                            }
                         break;
                        default:
                            $data['type_image'] = $this->site->site_url.'assets/img/document.jpg';
                    }

                $data['type_item'] = 'file';
                $data['type']      = 'file';
                $data['hash'] = $item->hash ;
                if ($data['type_item'])
                  {
                    $data['full_path'] = $this->Filepath($item);
                  }
                $data['users'] = $users_data;
                if(($item->password != '' || NULL)  && !$correctPassword && $logged_user != $item->author_id):
                    $this->load->view('shared/protected', $data);
                else:
                    $this->load->view('shared/view', $data);
                endif;
                else:
                    redirect($this->site->site_url);
                endif;
            break;
            default:
                redirect($this->site->site_url);
        }
    }

    /**
     * Check if file exsits if yes then return path.
     * @param $item
     * @return string
     */
    private function Filepath($item)
      {
        if (file_exists(FCPATH . 'application/views/uploads/content' . $item->file_path . $item->file_preview))
          {
            return FCPATH . 'application/views/uploads/content' . $item->file_path . $item->file_preview;
          }
      }

    /**
     * View file or folder by its link
     */
    public function viewFile()
      {
        $file_id   = $this->uri->segment(2, 0);
        $width     = $this->input->get('w');
        $height    = $this->input->get('h');
        $file_name = $this->BucktyContent->getFile($file_id);
        if (!empty($file_name)):
            $file_type = explode('/',$file_name->file_mime);
            if($file_type[0] != 'video'):
            if ($width == '' || $height == ''):
                $generatedimg = FCPATH . 'application/views/uploads/content' . $file_name->file_path . $file_name->file_preview;
            else:
                $generatedimg = $this->resize(
                    FCPATH . 'application/views/uploads/content' . $file_name->file_path . 
                    $file_name->file_preview, $width, $height);
              endif;
            if (file_exists(FCPATH . 'application/views/uploads/content' . $file_name->file_path . $file_name->file_preview)):
                if($file_type[0] == 'text'):
                    $text = file_get_contents($generatedimg);
                    echo html_escape($text);
                    exit;
                else:
                    header('Content-Description: File Transfer');
                    header('Content-Type: ' .$file_name->file_mime);
                    header('Content-Length: ' . filesize($generatedimg));
                    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    readfile($generatedimg);
                    exit;
                endif;
            endif;
            else:
                $param = array();
                $param['path'] = FCPATH . 'application/views/uploads/content' . $file_name->file_path . $file_name->file_preview;
                $param['type'] = $file_name->file_mime;
                $this->load->library('Stream',$param);
            endif;
          
        else:
            if ($width == '' || $height == ''):
                $generatedimg = FCPATH . 'application/views/uploads/site_uploads/default.jpg';
            else:
                $generatedimg = $this->resize(FCPATH . 'application/views/uploads/site_uploads/default.jpg', $width, $height);
            endif;
            if (file_exists(FCPATH . 'application/views/uploads/site_uploads/default.jpg')):
                header('Content-Length: ' . filesize($generatedimg));
                header('Content-Type: image/jpeg');
                header('Content-Disposition: inline; filename="' . $generatedimg . '";');
                readfile($generatedimg);
                die();
                exit;
              endif;
          endif;
      }
}
    