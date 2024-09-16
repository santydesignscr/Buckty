<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BUCKTY_User extends BUCKTY_Controller
{

    private $resize;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        $this->load->model('BucktyContent');
        $this->load->model('BucktyApi');
    }

    public function login()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->input->is_ajax_request()):
            $this->load->library('form_validation');
            $this->form_validation->set_rules('identity', 'Email Address', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');

            if ($this->form_validation->run() !== false):
                if ($this->aauth->login($this->input->post('identity'), $this->input->post('password'), $this->input->post('remember'))):
                    $message = array('message' => 'Logged In', 'code' => 0, 'refresh' => 1);
                    echo json_encode($message);
                else:
                    $auth_err = $this->aauth->print_errors();
                    $message = array('message' => $auth_err, 'code' => 1, 'type' => 'log');
                    echo json_encode($message);
                endif;
            else:
                $message = array('message' => validation_errors(), 'code' => 1, 'type' => 'log');
                echo json_encode($message);
            endif;
        else:
            show_404();
        endif;
    }

    public function create_user()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->input->is_ajax_request()):
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username', 'Username', 'trim|required');
            $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
            $this->form_validation->set_rules('password_confirm', 'Password Confirm', 'required|min_length[5]');

            if ($this->input->post('password') != $this->input->post('password_confirm')):
                $message = array('message' => tran($this->trans->Password_and_confirm_password_does_not_match), 'code' => 1, 'type' => 'reg');
                echo json_encode($message);
            elseif ($this->form_validation->run() !== false):
                $user_created = $this->aauth->create_user($this->input->post('email'), $this->input->post('password'), $this->input->post('username'),NULL,NULL,$this->input->post('user_role'),$this->input->post('verification'));
                $user_id = $this->aauth->get_user_id($this->input->post('email'));
                $this->BucktyContent->makeUserFolder($user_id);
                if ($user_created == 1):
                    if ($this->aauth->is_admin()):
                        $message = array('message' => 'User Created', 'code' => 0, 'refresh' => 0);
                        echo json_encode($message);
                    else:
                        if ($this->aauth->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'))):
                            $message = array('message' => 'Logged In', 'code' => 0, 'refresh' => 1);
                            echo json_encode($message);
                        else:
                            $auth_err = $this->aauth->print_errors();
                            $message = array('message' => $auth_err, 'code' => 1, 'type' => 'log');
                            echo json_encode($message);
                        endif;
                    endif;
                elseif ($user_created == 2):
                    $message = array('message' => tran($this->trans->We_have_sent_an_email_with_confirmation_link), 'code' => 2, 'type' => 'reg');
                    echo json_encode($message);
                else:
                    $auth_err = $this->aauth->print_errors();
                    $message = array('message' => $auth_err, 'code' => 1, 'type' => 'reg');
                    echo json_encode($message);
                endif;
            else:
                $message = array('message' => validation_errors(), 'code' => 1, 'type' => 'reg');
                echo json_encode($message);
            endif;
        else:
            show_404();
        endif;

    }

    public function activate()
    {
        if ($this->is_loggedin()):
            redirect($this->site->site_url);
            exit();
        endif;

        $user_id = $this->uri->segment(3, 0);
        $code = $this->uri->segment(4, 0);
        $res = $this->aauth->verify_user($user_id, $code);
        $data['res'] = $res;
        $data['pageTitle'] = 'Activation';
        $this->load->view('pages/activation', $data);
    }

    public function is_loggedin()
    {
        if ($this->aauth->is_loggedin()):
            return true;
        else:
            return false;
        endif;
    }

    public function logged_data()
    {
        return $this->aauth->get_user();
    }

    public function logout()
    {
        $this->aauth->logout();
        redirect($this->site->site_url);
    }

    public function userAction()
    {
        switch ($this->input->get('action')) {
            case 'profile_pic':
                $this->load->view('popups/profile_pic');
                break;
        }
    }

    public function getUsers()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $key = $this->input->get('key', TRUE);
        echo json_encode($this->aauth->get_user_by_key($key));
    }

    public function getImage()
    {
        $userhash = $this->uri->segment(2, 0);
        $size = $this->input->get('s');
        $user_id = $this->aauth->get_user_id_by_hash($userhash);
        $pic = $this->aauth->get_user_var('profile_pic', $user_id);
        if($pic != NULL || !empty($pic)) {
            $image = unserialize($pic);
            if ($size == 'medium' && file_exists(FCPATH . $image['medium'])) {
                $generatedimg = FCPATH . $image['medium'];
            } elseif ($size != 'medium' && file_exists(FCPATH . $image['large'])) {
                $generatedimg = FCPATH . $image['large'];
            } elseif (!file_exists(FCPATH . $image['medium'])) {
                if ($this->is_url_exist($image['medium'])) {
                    $generatedimg = $image['medium'];
                } else {
                    $generatedimg = FCPATH . 'application/views/uploads/site_uploads/default.jpg';
                }
            }
        } else {
            $generatedimg = FCPATH . 'application/views/uploads/site_uploads/default.jpg';
        }
        $type = image_type_to_mime_type(exif_imagetype($generatedimg));
        header('Content-Type: ' . $type); //<-- send mime-type header
        header('Content-Disposition: inline; filename="' . $generatedimg . '";'); //<-- sends filename header
        readfile($generatedimg); //<--reads and outputs the file onto the output buffer
        die(); //<--cleanup
        exit; //and exit

    }

    public function updateUser()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->input->is_ajax_request()):
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username', 'Username', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|min_length[5]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');

            if ($this->input->post('password') != '' && $this->input->post('password') != $this->input->post('confirm_password')):
                $message = array('message' => tran($this->trans->Password_Confirm_Error), 'error_code' => 1);
                echo json_encode($message);
                exit();
            endif;

            if ($this->form_validation->run() !== false):
                $user_id = (int)$this->logged_data()->id;
                $password = NULL;
                $email = NULL;
                $username = NULL;
                if ($this->input->post('user_id') != '')
                    $user_id = (int)$this->input->post('user_id');
                if ($this->input->post('password') != '')
                    $password = $this->input->post('password');

                if ($this->input->post('username') != $this->logged_data()->name && !$this->aauth->user_exist_by_name($this->input->post('username')))
                    $username = $this->input->post('username');

                if ($this->input->post('email') != $this->logged_data()->email && !$this->aauth->user_exist_by_email($this->input->post('email')))
                    $email = $this->input->post('email');

                $roles = $this->input->post('role');
                if ($this->aauth->is_admin()):
                    if ($roles != '' || $roles != NULL) {
                        $this->aauth->remove_member((int)$user_id);
                        foreach ($roles as $role) {
                            $this->aauth->add_member((int)$user_id, (int)$role);
                        }
                    }
                endif;

                if ($password || $email || $username != NULL):
                    $res = $this->aauth->update_user($user_id, $email, $password, $username);
                    if ($res != false):
                        $message = array('message' => tran($this->trans->User_was_updated), 'error_code' => 0);
                        echo json_encode($message);
                    else:
                        $errors = $this->aauth->get_errors_array();
                        $error = '';
                        foreach ($errors as $error) {
                            $error .= $error . '<br> ';
                        }
                        $message = array('message' => $error, 'error_code' => 1);
                        echo json_encode($message);
                    endif;
                else:
                    $message = array('message' => tran($this->trans->User_already_updated), 'error_code' => 0);
                    echo json_encode($message);
                endif;
            else:
                $message = array('message' => validation_errors(), 'error_code' => 1);
                echo json_encode($message);
            endif;
        else:


        endif;
    }

    public function update_pic()
    {
        $status = "";
        $msg = "";
        $file_element_name = 'user_image';

        if ($status != "error") {
            $config['upload_path'] = './application/views/uploads/profile_pics/large/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 1024 * 8;
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload($file_element_name)) {
                $status = 'error';
                $msg = $this->upload->display_errors('', '');
                echo json_encode(array('status' => $status, 'msg' => $msg));
            } else {
                $data = $this->upload->data();
                $main_image_path = $data['full_path'];
                if (file_exists($main_image_path)) {
                    $large = '/application/views/uploads/profile_pics/large/' . $data['file_name'];
                    $medium = $this->medium_resize($data['file_name']);
                    $final_ = serialize(array('large' => $large, 'medium' => $medium));
                    $this->aauth->set_user_var('profile_pic', $final_, $this->logged_data()->id);
                    echo json_encode(array('status' => 'Uploaded', 'msg' => tran($this->trans->Uploaded_Successfully), 'image' => $this->logged_data()->profile_pic->medium));
                } else {
                    $status = "error";
                    $msg = tran($this->trans->Semething_went_wrong);
                    echo json_encode(array('status' => $status, 'msg' => $msg));
                }
            }
            @unlink($_FILES[$file_element_name]);
        }
    }

    private function is_url_exist($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public function ajaxCall()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if ($this->input->is_ajax_request()) {
            $method = $this->input->get('action') != NULL ? $this->input->get('action'): $this->input->post('action') ;

            if (method_exists($this, $method)):
                call_user_func(array($this, $method));
            else:
                $message = array('message' => 'Method ' . $method . ' Does Not Exsits', 'error_code' => 1);
                echo json_encode($message);
            endif;
        } else {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
        }
    }

    public function medium_resize($filename)
    {
        $source_path = './application/views/uploads/profile_pics/large/' . $filename;
        $target_path = './application/views/uploads/profile_pics/medium/';
        $config_manip = array(
            'image_library' => 'gd2',
            'source_image' => $source_path,
            'new_image' => $target_path,
            'maintain_ratio' => TRUE,
            'thumb_marker' => '',
            'create_thumb' => TRUE,
            'width' => 300,
            'height' => 300
        );
        $this->load->library('image_lib', $config_manip);
        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
        }
        $this->image_lib->clear();
        return 'application/views/uploads/profile_pics/medium/' . $filename;
    }

    public function userCheck()
    {
        if ($this->aauth->is_loggedin()) {
            $message = array('csrf' => $this->security->get_csrf_hash(), 'message' => 'Loggedin', 'error_code' => 0, 'login' => 'true');
            echo json_encode($message);
        } else {
            $message = array('message' => tran($this->trans->Please_login_again), 'error_code' => 1, 'login' => 'false');
            echo json_encode($message);
        }
    }

    public function ResetPassword()
    {
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()) {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
            exit();
        }
        $email = $this->input->post('email');
        if ($this->aauth->user_exist_by_email($email)):
            $res = $this->aauth->remind_password($email);
            if ($res):
                $message = array('message' => tran($this->trans->Reset_link_sent), 'code' => 2);
                echo json_encode($message);
                exit();
            else:
                $message = array('message' => tran($this->trans->Something_went_wrong), 'code' => 1);
                echo json_encode($message);
                exit();
            endif;
        else:
            $message = array('message' => tran($this->trans->User_email_not_exist), 'code' => 1);
            echo json_encode($message);
            exit();
        endif;


    }

    public function loadResetPass()
    {

        $id = $this->uri->segment(3, 0);
        $hash = $this->uri->segment(4, 0);
        if ($this->aauth->check_recovery($id, $hash)) {
            $data['id'] = $id;
            $data['ver'] = $hash;
            $data['pageTitle'] = 'Recover';
            $this->load->view('pages/password', $data);
        } else {
            redirect($this->site->site_url);
        }
    }

    public function resetPass()
    {
        if (!$this->input->is_ajax_request()) {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
            exit();
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|min_length[5]');
        if ($this->input->post('password') != $this->input->post('confirm_password')):
            $message = array('message' => 'Your Password ans Confirm Password Does not match',
                'code' => 1,
                'type' => 'log');
            echo json_encode($message);
            exit();
        endif;
        if ($this->form_validation->run() !== false):
            $password = $this->input->post('password');
            $email = $this->input->post('email');
            $id = $this->input->post('id');
            $hash = $this->input->post('ver');
            if ($this->aauth->check_recovery($id, $hash)) {
                $res = $this->aauth->reset_password_by_email($email, $hash, $password);
                if ($res):
                    $message = array('message' => tran($this->trans->Your_password_was_changed),
                        'code' => 2,
                        'type' => 'log'
                    );
                    echo json_encode($message);
                    exit();
                else:
                    $message = array('message' => tran($this->trans->Wrong_details_provided),
                        'code' => 1,
                        'type' => 'log');
                    echo json_encode($message);
                    exit();
                endif;
            } else {
                $message = array('message' => tran($this->trans->Verification_code_error_or_expired),
                    'code' => 1,
                    'type' => 'log');
                echo json_encode($message);
                exit();
            }
        else:
            $message = array('message' => validation_errors(), 'code' => 1);
            echo json_encode($message);
        endif;

    }

    public function userBan(){
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()) {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
            exit();
        }
        $user = $this->input->post('hash');
        $what = $this->input->post('what');
        $user_id = $this->aauth->get_user_id_by_hash($user);
        if($this->aauth->is_admin()) {
            if ($user_id):
                if($what == 'u'):
                $this->aauth->unban_user($user_id);
                $message = array('message' => 'User was activated', 'error_code' => 0);
                echo json_encode($message);

                elseif($what == 'b'):
                    $this->aauth->ban_user($user_id);
                    $message = array('message' => 'User was banned', 'error_code' => 0);
                    echo json_encode($message);
                endif;
            else:
                $message = array('message' => 'Something wrong happend', 'error_code' => 1);
                echo json_encode($message);
            endif;
        } else {
            $message = array('message' => 'Invalid guest request', 'error_code' => 1);
            echo json_encode($message);
        }
    }

    public function UserApiDetails(){
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()) {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
            exit();
        }
        $userHash = $this->aauth->get_user_var('user_hash');
        $data['api_key'] = $this->BucktyApi->GetUserApi($userHash);
        $this->load->view('popups/api',$data);
    }
    public function GenerateUserApi(){
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()) {
            $message = array('message' => 'Invalid Request', 'error_code' => 1);
            echo json_encode($message);
            exit();
        }
        $userHash = $this->aauth->get_user_var('user_hash');
        $apicheck = $this->BucktyApi->GetUserApi($userHash);
        if($apicheck != FALSE){
            $message = array('message'=>'Api key is already generated','key'=>$apicheck);
            GenerateMsg($message,0);
            exit;
        }
        $key = generateRandomString(20);
        $this->BucktyApi->InsertUserApi($key,$userHash);
        $message = array('message'=>'Api key generated','key'=>$key.$userHash);
        GenerateMsg($message,0);
    }

}