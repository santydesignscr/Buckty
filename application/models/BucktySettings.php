<?php

/**
 * Class BucktySettings
 */

class BucktySettings extends CI_Model
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
     * BucktySettings constructor.
     */
    function __construct()
    {
        // get main CI object
        $this->CI = &get_instance();

        // Dependancies
        if (CI_VERSION >= 2.2) {
            $this->CI->load->library('driver');
        }
        $this->CI->load->library('session');
        $this->CI->load->library('email');
        $this->CI->load->helper('url');
        $this->CI->load->helper('string');
        $this->CI->load->helper('email');
        $this->CI->load->helper('language');
        $this->CI->load->helper('recaptchalib');
        $this->CI->load->helper('googleauthenticator_helper');
        $this->CI->lang->load('aauth');
        // config/buckty.php
        $this->CI->config->load('buckty');
        $this->config_vars = $this->CI->config->item('buckty');
    }

    /**
     * Get site settings from databse saved by administator
     * The below fucntions is used for getting site settings.
     * @return array
     */
    public function settings()
    {
        // getting the settings from database.
        $this->db->select('id,name,value');
        $details = $this->db->get($this->config_vars['site_settings']);
        $s = array();
        $det_ = $details->result();
        foreach ($det_ as $det) {
            $s [$det->name] = array(
                'id' => $det->id,
                'name' => $det->name,
                'value' => $det->value
            );
        }
        return $s;
    }

    /**
     * Get all site settings including social and general settings
     * @return array
     */

    public function LoadSettings()
    {
        $set = $this->settings();
        $site = array();
        $site['site_info'] = array();
        $site['site_url'] = $set ['site_url'] ['value']; // site url
        $site['site_name'] = $set ['site_name'] ['value']; // site name
        $site['admin_email'] = $set ['admin_email'] ['value']; // administrator email
        $site['social'] = $set ['social'] ['value']; // Social Login yes/no
        $site['site_keywords'] = $set['site_keywords'] ['value'];
        $site['site_description'] = $set['site_description']['value'];
        $site['upload_limit'] = $set['upload_limit']['value'];
        $site['site_info']['upload_limit'] = $set['upload_limit']['value'];
        $site['register_active'] = $set['register_active']['value'];
        $site['site_home_tagline'] = $set['site_home_tagline']['value'];
        $site['site_home_description'] = $set['site_home_description']['value'];
        if (!empty($set['ad_780']) && ($set['ad_780'] != NULL || $set['ad_780'] != '')) {
            $site['ad_780'] = $set['ad_780']['value'];
        } else {
            $site['ad_780'] = FALSE;
        }
        if (!empty($set['ad_320']) && ($set['ad_320'] != NULL || $set['ad_320'] != '')) {
            $site['ad_320'] = $set['ad_320']['value'];
        } else {
            $site['ad_320'] = FALSE;
        }
        if (!empty($set['dropbox']) && ($set['dropbox']['value'] != '' || $set['dropbox']['value'] != FALSE)):
            $dropbox = (array) unserialize($set['dropbox']['value']);
            if (!empty($dropbox['id'])):
                // unset the secret and api id from site info array
                $site['site_info']['dropbox'] = $dropbox;
                unset($site['site_info']['dropbox']['id'],
                    $site['site_info']['dropbox']['secret'],
                    $site['site_info']['dropbox']['appname']);
            elseif(empty($dropbox['key'])):
                $site['site_info']['dropbox'] = array('activation' => '0');
            endif;
        else:
            $site['site_info']['dropbox'] = array('activation' => '0');
        endif;
        if (!empty($set['google']) && ($set['google']['value'] != '' || $set['google']['value'] != FALSE)):
            $site['site_info']['google'] = unserialize($set['google']['value']);
            // unset the secret and api id from site info array
            unset($site['site_info']['google']['id'],
                $site['site_info']['google']['secret'],
                $site['site_info']['google']['activation']);
        else:
            $site['site_info']['google'] = array('drive_activation' => '0');
        endif;
        if (!empty($set['disqus']) && ($set['disqus']['value'] != '' || $set['disqus']['value'] != FALSE)):
            $site['site_info']['disqus'] = unserialize($set['disqus']['value'])['shortname'];
        // unset the secret and api id from site info array
        else:
            $site['site_info']['disqus'] = 'no';
        endif;
        if(!empty($set['pexels']) && ($set['pexels']['value'] != '' || $set['pexels']['value'] != NULL)):
            $pexels = unserialize($set['pexels']['value']);
            if(!empty($pexels['key']) || $pexels['key'] != ''):
                $site['site_info']['pexels'] = $pexels;
                $site['site_info']['pexels']['active'] = '1';
            else:
                $site['site_info']['pexels']['active'] = '0';
            endif;
        else:
            $site['site_info']['pexels']['active'] = '0';
        endif;

        $site['email_activation'] = $set['email_activation']['value'];
        $site['smtp_host'] = $set['smtp_host']['value'];
        $site['smtp_port'] = $set['smtp_port']['value'];
        $site['smtp_user'] = $set['smtp_user']['value'];
        $site['smtp_password'] = $set['smtp_password']['value'];
        $types = $set['allowed_extensions']['value'];
        $d_types = $set['blacklist_extensions']['value'];
        $site['site_info']['max_size'] = $set['max_file_size']['value'];
        $types_data = @unserialize($types);
        if ($types_data !== false) {
            $site['site_info']['allowed_extensions'] = unserialize($types);
            $site['allowed_extensions'] = implode(',', unserialize($types));
        } else {
            $site['site_info']['allowed_extensions'] = $types;
            $site['allowed_extensions'] = $types;
        }
        $d_types_data = @unserialize($types);
        if ($d_types_data !== false) {
            $site['site_info']['blacklist_extensions'] = unserialize($d_types);
            $site['blacklist_extensions'] = implode(',', unserialize($d_types));
        } else {
            $site['site_info']['blacklist_extensions'] = $d_types;
            $site['blacklist_extensions'] = $d_types;
        }
        return $site;
    }

    /**
     * The below function is used for updating site settings.
     * @param $data
     * @return bool
     */
    public function updateConfig($data)
    {
        $fields = array();
        foreach ($data as $field => $value) {
            $this->db->select('*');
            $this->db->where('name', $field);
            $key = $this->db->get($this->config_vars['site_settings']);
            $key = $key->row();
            if (!empty($key)) {
                $this->db->where('name', $field);
                $this->db->update($this->config_vars['site_settings'], array('value' => $value));
            } else {
                $this->db->insert($this->config_vars['site_settings'], array('name' => $field, 'value' => $value));
            }
        }
        return true;
    }

    /**
     * Checking if language with slug is already there.
     * @param array $data
     * @return bool
     */
    public function checkLang($data = array())
    {
        $this->db->select('*');
        $this->db->where('lang_slug', $data['langSlug']);
        $lang = $this->db->get($this->config_vars['language']);
        $lang = $lang->row();
        if (!empty($lang)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Inserting langauge into database.
     * @param array $data
     * @return bool
     */
    public function addLang($data = array())
    {
        $langData = array(
            'lang_title' => $data['langName'],
            'lang_slug' => $data['langSlug']
        );
        $this->db->insert($this->config_vars['language'], $langData);
        $langId = $this->db->insert_id();
        return true;
    }

    /**
     * Getting all The langauges List.
     * @return mixed
     */
    public function getLangs()
    {
        $this->db->select('*');
        $langs = $this->db->get($this->config_vars['language']);
        return $langs->result();
    }

    /**
     * Delete the language from database.
     * @param $langId
     * @return bool
     */
    public function delLanguage($langId)
    {
        $langId = (int)$langId;
        $this->db->delete($this->config_vars['language'], array('id' => $langId));
        $this->db->delete($this->config_vars['language_data'], array('lang_id' => $langId));
        return true;
    }

    /**
     * Getting all The langauge keys.
     * @param $l_id
     * @return array
     */
    public function getLangFields($l_id)
    {
        $this->db->select('*');
        $this->db->where('lang_id', $l_id);
        $langs = $this->db->get($this->config_vars['language_data']);
        $langs = $langs->result();
        $l_keys = array();
        foreach ($langs as $lang) {
            $lang->lang_id = $l_id;
            $lang->keyname = str_replace(' ', '', $lang->key_name);
            $l_keys[$lang->key_name] = $lang;
        }
        $keys = $this->getKeys();
        $l_ = array();
        foreach ($keys as $key) {
            if (array_key_exists($key['key_name'], $l_keys)):
                $values = $l_keys[$key['key_name']];
            else:
                $key['lang_id'] = $l_id;
                $key['keyname'] = str_replace(' ', '', $key['key_name']);;
                $values = $key;
            endif;
            array_push($l_, $values);
        }
        return $l_;
    }

    /**
     * Managing the language translation keys
     * @param $keys | array
     * @return bool
     */
    public function ManageKeys($keys)
    {
        $this->db->select('value');
        $this->db->where('name', 'language_keys');
        $result = $this->db->get($this->config_vars['site_settings']);
        $result = $result->row();
        if (!empty($result)) {
            $data_keys = unserialize($result->value);
            foreach ($keys as $key) {
                if (in_array($key, $data_keys)) {
                    continue;
                } else {
                    $data_keys[] = $key;
                }
            }
            $in_keys = serialize($data_keys);
            $data = array('name' => 'language_keys', 'value' => $in_keys);
            $this->db->where('name', 'language_keys');
            $this->db->update($this->config_vars['site_settings'], $data);
            return true;
        } else {
            $in_keys = serialize($keys);
            $data = array('name' => 'language_keys', 'value' => $in_keys);
            $this->db->insert($this->config_vars['site_settings'], $data);
            return true;
        }
    }



    /**
     * Setting up the selected language as default for the whole site.
     * @param $id | $id need to be id of language
     * @return bool
     */
    public function setLang($id)
    {
        $this->db->query('UPDATE ' . $this->config_vars['language'] . ' SET `default` = CASE WHEN id = ' . $id . ' THEN 1 ELSE 0 END');
        return true;
    }

    /**
     * Getting the language Keys from database to show them or use them for internal function.
     * @return array|mixed
     */
    public function getKeys()
    {
        $this->db->select('value');
        $this->db->where('name', 'language_keys');
        $result = $this->db->get($this->config_vars['site_settings']);
        $result = $result->row();
        if (!empty($result)):
            $data_keys = unserialize($result->value);
            $keys = array();
            foreach ($data_keys as $key) {
                $keys[] = array('key_name' => $key, 'value' => $key);
            }
            $data_keys = $keys;
        else:
            $data_keys = array();
        endif;
        return $data_keys;
    }


    /**
     * Remove language translation key
     * @param $key
     * @return bool
     */
    public function removeKey($key)
    {
        $keys = $this->get_system_var('language_keys');
        $Keysnew = array();
        foreach ($keys as $value) {
            if ($value == $key || $value == '') {
                continue;
            }
            $keysnew[] = $value;
        }
        $keysnew = serialize($keysnew);
        $this->set_system_var('language_keys', $keysnew);
        return true;
    }

    /**
     * Settings the keys for specified langauge id.
     * @param $langId
     * @param array $keys
     * @return bool
     */
    public function setKeys($langId, $keys = array())
    {
        $keys_ = $this->getKeys();
        foreach ($keys_ as $key) {
            $this->db->insert($this->config_vars['language_data'], array('lang_id' => $langId, 'key_name' => $key, 'value' => $key));
        }
        return true;
    }

    /**
     * Check translations
     * @param $t
     * @return bool
     */
    private function checkTrans($t)
    {
        $this->db->select('*');
        $this->db->where('key_name', $t['key']);
        $this->db->where('lang_id', $t['lang']);
        $data = $this->db->get($this->config_vars['language_data']);
        $data = $data->row();
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save translations
     * @param array $tran
     * @return bool
     */
    public function saveTrans($tran = array())
    {
        if (!empty($tran)) {
            foreach ($tran as $key => $t) {
                if ($this->checkTrans($t) == true) {
                    $data = array('value' => $t['value']);
                    $this->db->where('key_name', $t['key']);
                    $this->db->where('lang_id', (int)$t['lang']);
                    $this->db->update($this->config_vars['language_data'], $data);
                } elseif ($this->checkTrans($t) == false) {
                    $this->db->insert($this->config_vars['language_data'], array('lang_id' => (int)$t['lang'], 'key_name' => $t['key'], 'value' => $t['value']));
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get site translations
     * @return array
     */
    public function getTrans()
    {
        $this->db->select('*');
        $this->db->where('default', 1);
        $language = $this->db->get($this->config_vars['language']);
        $language = $language->row();
        $keys_saved = $this->get_system_var('language_keys');
        if (!empty($language)) {
            $l_id = $language->id;
            $this->db->select('*');
            $this->db->where('lang_id', $l_id);
            $keys = $this->db->get($this->config_vars['language_data']);
            $kys = $keys->result();
            $keys = array();
            foreach($kys as $key){
                $keys[$key->key_name] = $key;
            }
        }
        $translation = array();
        foreach ($keys_saved as $key_) {

            if(!empty($keys[$key_])) {
                $key = (object) $keys[$key_] ;
                $keyn = str_replace(' ', '_', $key->key_name);
                $translation[$keyn] = array('key' => $key->key_name, 'trans' => $key->value);
            } else {
                $keyn = str_replace(' ','_',$key_);
                $translation[$keyn] = array('key'=>$key_,'trans'=>$key_);
            }
        }
        return $translation;

    }

    /**
     * Getting api settings from database.
     * @return mixed
     */
    public function getApi()
    {
        $data['facebook'] = $this->get_system_var('facebook') != '' || NUll ? $this->get_system_var('facebook') : array();
        $data['google'] = $this->get_system_var('google') != '' || NUll ? $this->get_system_var('google') : array();
        $data['twitter'] = $this->get_system_var('twitter') != '' || NUll ? $this->get_system_var('twitter') : array();
        $data['dropbox'] = $this->get_system_var('dropbox') != '' || NUll ? $this->get_system_var('dropbox') : array();
        $data['disqus'] = $this->get_system_var('disqus') != '' || NUll ? $this->get_system_var('disqus') : array();
        $data['pexels'] = $this->get_system_var('pexels') != '' || NULL ? $this->get_system_var('pexels') : array();
        return $data;
    }

    /**
     * Save social api into database
     * @param array $data
     * @return bool
     */
    public function saveApi($data = array())
    {
        $this->set_system_var('facebook', $data['facebook']);
        $this->set_system_var('google', $data['google']);
        $this->set_system_var('twitter', $data['twitter']);
        $this->set_system_var('dropbox', $data['dropbox']);
        $this->set_system_var('disqus', $data['disqus']);
        $this->set_system_var('pexels', $data['pexels']);
        return true;
    }


    /**
     * Set Aauth System Variable as key value
     * if variable not set before, it will be set
     * if set, overwrites the value
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set_system_var($key, $value)
    {

        // if var not set, set
        if ($this->get_system_var($key) === FALSE) {

            $data = array(
                'name' => $key,
                'value' => $value,
            );

            return $this->db->insert($this->config_vars['system_variables'], $data);

        } // if var already set, overwrite
        else {

            $data = array(
                'name' => $key,
                'value' => $value,
            );

            $this->db->where('name', $key);
            return $this->db->update($this->config_vars['system_variables'], $data);
        }

    }

    /**
     * Unset Aauth System Variable as key value
     * @param string $key
     * @return bool
     */
    public function unset_system_var($key)
    {

        $this->db->where('name', $key);

        return $this->aauth_db->delete($this->config_vars['system_variables']);
    }

    //tested
    /**
     * Get Aauth System Variable by key
     * Return string of variable value or FALSE
     * @param string $key
     * @return bool|string , FALSE if var is not set, the value of var if set
     */
    public function get_system_var($key)
    {

        $query = $this->db->where('name', $key);

        $query = $this->db->get($this->config_vars['system_variables']);

        // if variable not set
        if ($query->num_rows() < 1) {
            return FALSE;
        } else {

            $row = $query->row();
            $data = @unserialize($row->value);
            return $data != false ? unserialize($row->value) : $row->value;
        }
    }

    /**
     * List System Variable Keys
     * Return array of variable keys or FALSE
     * @return bool|array , FALSE if var is not set, the value of var if set
     */

    public function list_system_var_keys()
    {
        $query = $this->db->select('name');
        $query = $this->db->get($this->config_vars['system_variables']);
        // if variable not set
        if ($query->num_rows() < 1) {
            return FALSE;
        } else {
            return $query->result();
        }
    }
}