<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class UploadController
 */
class UploadController extends BUCKTY_Content
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
     * UploadController constructor.
     */
    public function __construct()
    {
        /**
         * BUCKTY_Content parent constructor
         */
        parent::__construct();
        $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? $this->input->get('csrf_Buckty') : $this->input->post('csrf_Buckty');
        if ($this->aauth->is_loggedin()):
            $this->current_user = $this->logged_data();
        else:
            if ($this->input->is_ajax_request()):
                $message = array('message' => 'Please Login Again', 'error_code' => 1, 'login' => 'false');
                echo json_encode($message);
                exit();
            else:
                redirect($this->site->site_url);
            endif;
        endif;
    }

    /**
     * Uploadfiles - upload files to buckty's system save them and encrypt them
     */
    public function uploadfiles()
    {
        if ($this->input->is_ajax_request()):
            $site_info = (object)$this->BucktySettings->LoadSettings()['site_info'];

            if (isset($_FILES['useractionfile'])):
                $file_type = explode('/',$_FILES['useractionfile']['type'])[1];
                if(in_array($file_type,$this->getBlacklistTypes())):
                    $msg = array('message' => 'File type not allowed');
                    GenerateMsg($msg, 1);
                    exit();
                else:
                    $file = (object)$_FILES['useractionfile'];
                endif;
            else:
                $msg = array('message' => 'File size too large');
                GenerateMsg($msg, 1);
                exit();
            endif;
            $userSpace = $this->userUsedSpace(null, 0);
            $total = $userSpace + $file->size;
            if ($total < $site_info->upload_limit):
                $today = date('Ym');
                $folder = $this->input->post('f_in');
                if ($folder != '0') {
                    $folder = $this->getFolder($folder);
                    if($folder->is_shared == 1) {
                        $users = $this->BucktyContent->getFolderUsers($folder->folder_hash);
                    } else {
                        $users = array();
                    }
                } else {
                    $folder = (object)array('folder_hash' => 0,'is_shared'=>0);
                    $users = array();
                }

                $u_hash = $this->aauth->get_user_var('user_hash', $this->logged_data()->id);
                $upload_name = 'useractionfile';
                $filename_ = $this->generateRandomString(40);
                $this->load->library('upload', $this->UploadConfigs());
                if (!$this->upload->do_upload($upload_name,$filename_)):
                    $status = 'error';
                    $msg = array('message' => $this->upload->display_errors('', ''));
                    GenerateMsg($msg, 1);
                else:
                    $data = $this->upload->data();
                    $img_w = $data['is_image'] == true ? $data['image_width'] : 0;
                    $img_h = $data['is_image'] == true ? $data['image_height'] : 0;
                    $file_name = $data['full_path'];
                    $file_size = filesize($file_name);
                    $file_ = array('file_name' => $data['orig_name'],
                        'hash' => $this->generateRandomString()

                    );
                    $file_id = $this->BucktyContent->insertFile($this->config_vars['files'], $file_);
                    if (is_numeric($file_id)):
                        $file_data = array('file_id' => $file_id,
                            'file_author' => $this->logged_data()->id,
                            'file_mime' => $data['file_type'],
                            'file_date' => date('Y-m-d H:i:s'),
                            'file_size' => $file_size,
                            'image_width' => $img_w,
                            'image_height' => $img_h,
                            'file_preview' => $data['file_name'],
                            'file_path' => '/' . $u_hash . '/' . $today . '/',
                            'file_type' => str_replace('.', '', $data['file_ext']),
                            'file_ext' => str_replace('.', '', $data['file_ext'])
                        );
                        $relation = array('author_id' => $this->logged_data()->id,
                            'content_id' => $file_id,
                            'content_parent' => $folder->folder_hash,
                            'content_type' => 'file',
                            'permission' => 1,
                            'owner' => 1);
                        $this->BucktyContent->insertFile($this->config_vars['files_data'], $file_data);
                        $this->BucktyContent->insertFile($this->config_vars['relations'], $relation);
                        if (!empty($users)):
                            $this->db->where('ID',$file_id);
                            $this->db->update($this->config_vars['files'],array('is_shared'=>1));
                            foreach ($users as $user) {
                                if ($user->user_id == $this->logged_data()->id) {
                                    continue;
                                } else {
                                    $data = array('author_id' => $user->user_id,
                                        'content_id' => $file_id,
                                        'content_parent' => $folder->folder_hash,
                                        'content_type' => 'file',
                                        'owner' => 0,
                                        'shared' => 1,
                                        'permission' => 2);
                                    $this->BucktyContent->Binsert($this->config_vars['relations'], $data);
                                }
                            }
                        endif;
                    else:
                        GenerateMsg($file_id, 1);
                    endif;
                    $file_data = $this->getFile($file_id, $this->logged_data()->id);
                    GenerateMsg(array('message' => 'Files Uploaded', 'file' => $file_data), 0);
                endif;
            else:
                GenerateMsg(array('message' => 'Not Enough Space Left'), 1);
            endif;
        else:
            redirect($this->site->site_url);
        endif;
    }

    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Create configuration
     * @return mixed
     */
    private function UploadConfigs()
    {
        $today = date('Ym');
        $u_hash = $this->aauth->get_user_var('user_hash', $this->logged_data()->id);
        $u_dir = $this->dirCheck(array('hash' => $u_hash, 'date' => $today));
        $config['upload_path'] = $u_dir;
        $config['allowed_types'] = $this->getTypes() != NULL || $this->getTypes() != ''? $this->getTypes(): '*';
        $config['max_size'] = $this->site->upload_limit;
        return $config;
    }

    /**
     * Get allowed extentions
     * @return mixed
     */
    private function getTypes()
    {
        return str_replace(',', '|', $this->site->allowed_extensions);
    }

    /**
     * @return array
     */
    public function getBlacklistTypes(){
        return explode(',',$this->site->blacklist_extensions);
    }

    /**
     * Check if user directory exsits if no then create one.
     * @param $dir
     * @return string
     */
    private function dirCheck($dir)
    {
        if (!is_dir(FCPATH . 'application/views/uploads/content/' . $dir['hash'])):
            mkdir(FCPATH . 'application/views/uploads/content/' . $dir['hash'], 0777, true);
        endif;
        if (!is_dir(FCPATH . 'application/views/uploads/content/' . $dir['hash'] . '/' . $dir['date'])):
            mkdir(FCPATH . 'application/views/uploads/content/' . $dir['hash'] . '/' . $dir['date'], 0777, true);
        endif;
        return FCPATH . 'application/views/uploads/content/' . $dir['hash'] . '/' . $dir['date'];
    }

    /**
     * Check if it's valid jpeg image if yes then get advanced informations
     * @param $imagePath
     * @param $type
     * @return array|bool
     */
    private function cameraUsed($imagePath, $type)
    {

        if ($type != 'image/jpeg') {
            $notFound = "Unavailable";
            $return = array();
            $return['make'] = $notFound;
            $return['model'] = $notFound;
            $return['exposure'] = $notFound;
            $return['aperture'] = $notFound;
            $return['date'] = $notFound;
            $return['iso'] = $notFound;
            return $return;
        }
        // Check if the variable is set and if the file itself exists before continuing
        if ((isset($imagePath)) and (file_exists($imagePath))) {

            // There are 2 arrays which contains the information we are after, so it's easier to state them both
            $exif_ifd0 = read_exif_data($imagePath, 'IFD0', 0);
            $exif_exif = read_exif_data($imagePath, 'EXIF', 0);

            //error control
            $notFound = "Unavailable";

            // Make
            if (@array_key_exists('Make', $exif_ifd0)) {
                $camMake = $exif_ifd0['Make'];
            } else {
                $camMake = $notFound;
            }

            // Model
            if (@array_key_exists('Model', $exif_ifd0)) {
                $camModel = $exif_ifd0['Model'];
            } else {
                $camModel = $notFound;
            }

            // Exposure
            if (@array_key_exists('ExposureTime', $exif_ifd0)) {
                $camExposure = $exif_ifd0['ExposureTime'];
            } else {
                $camExposure = $notFound;
            }

            // Aperture
            if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
                $camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
            } else {
                $camAperture = $notFound;
            }

            // Date
            if (@array_key_exists('DateTime', $exif_ifd0)) {
                $camDate = $exif_ifd0['DateTime'];
            } else {
                $camDate = $notFound;
            }

            // ISO
            if (@array_key_exists('ISOSpeedRatings', $exif_exif)) {
                $camIso = $exif_exif['ISOSpeedRatings'];
            } else {
                $camIso = $notFound;
            }

            $return = array();
            $return['make'] = $camMake;
            $return['model'] = $camModel;
            $return['exposure'] = $camExposure;
            $return['aperture'] = $camAperture;
            $return['date'] = $camDate;
            $return['iso'] = $camIso;
            return $return;

        } else {
            return false;
        }
    }
}