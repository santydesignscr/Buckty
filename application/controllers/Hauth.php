<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class HAuth
 */
class HAuth extends BUCKTY_User
{

    /**
     * Auth to social providers
     */
    public function login_social()
    {
        $provider = $this->uri->segment(2, 0);
        log_message('debug', "controllers.HAuth.login($provider) called");
        
        try {
            log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
            $this->load->library('HybridAuthLib');
            
            if ($this->hybridauthlib->providerEnabled($provider)) {
                log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
                $service = $this->hybridauthlib->authenticate($provider);
                
                if ($service->isUserConnected()) {
                    log_message('debug', 'controller.HAuth.login: user authenticated.');
                    
                    $user_profile = $service->getUserProfile();
                    log_message('info', 'controllers.HAuth.login: user profile:' . PHP_EOL . print_r($user_profile, TRUE));

                    $sp_identity = $user_profile->identifier;
                    if ($user_profile->email == NULL || $user_profile->email == '') {
                        $email =  $sp_identity . '@buckty.com';
                    } else {
                        $email = $user_profile->email;
                    }

                    $username = strtolower(str_replace(' ', '', $user_profile->displayName . rand(2, 3)));
                    $pass     = substr($user_profile->identifier, 1, 2) . 'buckty@@';
                    $image    = array(
                        'large' => $user_profile->photoURL,
                        'medium' => $user_profile->photoURL
                    );
                    $user_if = $this->aauth->user_exist_by_email($email);
                    if($user_if){
                        if($this->aauth->login_fast($user_if->id)){
                            echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
                            exit();
                        }
                    }
                        $user = $this->aauth->create_user($email, $pass, $username, $sp_identity, $image);
                        if ($user == 2 || $user == 1) {
                            $log_ = $this->aauth->login($sp_identity, $pass,true, NULL,true);
                            var_dump($log_);
                            if ($log_) {
                                echo 'logged';
                                echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
                            }
                        } elseif ($user == false) {
                            echo 'error';
                            echo $this->aauth->print_errors();
                        }
                } else // Cannot authenticate user
                    {
                    show_error('Cannot authenticate user');
                }
            } else // This service is not enabled.
                {
                log_message('error', 'controllers.HAuth.login: This provider is not enabled (' . $provider . ')');
                show_404($_SERVER['REQUEST_URI']);
            }
        }
        catch (Exception $e) {
            $error = 'Unexpected error';
            switch ($e->getCode()) {
                case 0:
                    $error = 'Unspecified error.';
                    break;
                case 1:
                    $error = 'Hybriauth configuration error.';
                    break;
                case 2:
                    $error = 'Provider not properly configured.';
                    break;
                case 3:
                    $error = 'Unknown or disabled provider.';
                    break;
                case 4:
                    $error = 'Missing provider application credentials.';
                    break;
                case 5:
                    log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
                    //redirect();
                    if (isset($service)) {
                        log_message('debug', 'controllers.HAuth.login: logging out from service.');
                        $service->logout();
                    }
                    echo "<script type=\"text/javascript\" charset=\"utf-8\">window.self.close()</script>";
                    break;
                case 6:
                    $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
                    break;
                case 7:
                    $error = 'User not connected to the provider.';
                    break;
            }
            
            if (isset($service)) {
                $service->logout();
            }
            
            log_message('error', 'controllers.HAuth.login: ' . $error);
            show_error('Error authenticating user.');
        }
    }

    /**
     * Hauth endpoint from social login
     */
    public function endpoint()
    {
        
        log_message('debug', 'controllers.HAuth.endpoint called.');
        log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: ' . print_r($_REQUEST, TRUE));
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
            $_GET = $_REQUEST;
        }
        
        log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
        require_once APPPATH . '/third_party/hybridauth/index.php';
        
    }
}