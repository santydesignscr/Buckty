<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Aauth Config
| -------------------------------------------------------------------
| A library Basic Authorization for CodeIgniter 2+
|
| -------------------------------------------------------------------
| EXPLANATION
| -------------------------------------------------------------------
|
|
| 	['no_permission']                  	If user don't have permisssion to see the page he will be redirected the page spesificed.
|
| 	['admin_group']                    	Name of admin group
| 	['default_group']                  	Name of default group, the new user is added in it
| 	['public_group']                   	Public group , people who not logged in
|
| 	['db_profile']                     	The configuration database profile (see config/database.php)
|
| 	['users']                          	The table which contains users
| 	['groups']                         	The table which contains groups
| 	['user_to_group']                  	The table which contains join of users and groups
| 	['perms']                          	The table which contains permissions
| 	['perm_to_group']                  	The table which contains permissions for groups
| 	['perm_to_user']                   	The table which contains permissions for users
| 	['pms']                            	The table which contains private messages
| 	['system_variables']               	The table which contains Aauth system variables
| 	['user_variables']                 	The table which contains users variables
|
| 	['remember']                       	Remember time elapsed after connecting and automatic LogOut
|
| 	['max']                            	Maximum char long for Password
| 	['min']                            	Minimum char long for Password
|
| 	['valid_chars']                    	Valid chars for username. Non alphanumeric characters that are allowed by default
|
| 	['ddos_protection']                	If it is true, the user will be banned temporary when he exceed the login 'try'
|
| 	['recaptcha_active']               	Enable reCAPTCHA (see www.google.com/recaptcha/admin)
| 	['recaptcha_login_attempts']       	:
| 	['recaptcha_siteKey']              	The reCAPTCHA siteKey
| 	['recaptcha_secret']               	The reCAPTCHA secretKey
|
| 	['totp_active']                    	The Time-based One-time Password Algorithm
| 	['totp_only_on_ip_change']         	TOTP only on IP Change
| 	['totp_reset_over_reset_password'] 	TOTP reset over reset Password
|
| 	['max_login_attempt']              	Login attempts time interval (default 20 times in one hour)
|
| 	['login_with_name']                	Login Identificator, if TRUE username needed to login else email address.
|
| 	['use_cookies']                    	FALSE only on CI3
|
| 	['email']                          	Sender email address, used for remind_password, send_verification and reset_password
| 	['name']                           	Sender name, used for remind_password, send_verification and reset_password
|
| 	['verification']                   	User Verification, if TRUE sends a verification email on account creation.
| 	['verification_link']              	Link for verification without site_url or base_url
| 	['reset_password_link']            	Link for reset_password without site_url or base_url
|
|	['hash']							Name of selected hashing algorithm (e.g. "md5", "sha256", "haval160,4", etc..)
|										Please, run hash_algos() for know your all supported algorithms
|
*/
$config_buckty = array();

$config_buckty["default"] = array(
    'no_permission' => FALSE,

    'admin_group' => 'admin',
    'default_group' => 'default',
    'public_group' => 'public',

    'db_profile' => 'default',

    'users' => 'buckty_users',
    'groups' => 'buckty_groups',
    'user_to_group' => 'buckty_user_to_group',
    'perms' => 'buckty_perms',
    'perm_to_group' => 'buckty_perm_to_group',
    'perm_to_user' => 'buckty_perm_to_user',
    'pms' => 'buckty_pms',
    'system_variables' => 'buckty_settings',
    'user_variables' => 'buckty_user_variables',
    'site_settings' => 'buckty_settings',
    'folders' => 'buckty_folders',
    'relations' => 'buckty_relations',
    'notifications' => 'buckty_notifications',
    'files' => 'buckty_files',
    'files_data' => 'buckty_files_data',
    'language' => 'buckty_language',
    'language_data' => 'buckty_language_data',
    'pages' => 'buckty_pages',
    'api' => 'buckty_api',
    'api_log' => 'buckty_api_logs',
    'remember' => ' +3 days',

    'max' => 15,
    'min' => 5,

    'valid_chars' => array(),

    'ddos_protection' => true,

    'recaptcha_active' => false,
    'recaptcha_login_attempts' => 4,
    'recaptcha_siteKey' => '',
    'recaptcha_secret' => '',

    'totp_active' => false,
    'totp_only_on_ip_change' => false,
    'totp_reset_over_reset_password' => false,

    'max_login_attempt' => 30,

    'login_with_name' => true,

    'use_cookies' => true,

    'email' => 'no-reply@buckty.com',
    'name' => 'Buckty',

    'verification' => true,
    'verification_link' => '/user/verification/',
    'reset_password_link' => '/user/reset_password/',

    'hash' => 'sha256'
);

$config['buckty'] = $config_buckty['default'];