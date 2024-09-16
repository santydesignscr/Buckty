<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ovveride default upload class
 * Class BUCKTY_Upload
 */
class BUCKTY_Upload extends CI_Upload {

   function __construct(array $config)
   {
       parent::__construct($config);
   }

    /**
     * Override default upload function to encrypt files.
     * @param string $field
     * @param string $new_name
     * @return bool
     */
    function do_upload($field = 'userfile', $new_name='') {

        if ( ! isset($_FILES[$field])) {
            $this->set_error('upload_no_file_selected');
            return FALSE;
        }

        if ( ! $this->validate_upload_path()) {
            return FALSE;
        }

        if ( ! is_uploaded_file($_FILES[$field]['tmp_name'])) {
            $error = ( ! isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];

            switch($error) {
                case 1: // UPLOAD_ERR_INI_SIZE
                    $this->set_error('upload_file_exceeds_limit');
                    break;
                case 2: // UPLOAD_ERR_FORM_SIZE
                    $this->set_error('upload_file_exceeds_form_limit');
                    break;
                case 3: // UPLOAD_ERR_PARTIAL
                    $this->set_error('upload_file_partial');
                    break;
                case 4: // UPLOAD_ERR_NO_FILE
                    $this->set_error('upload_no_file_selected');
                    break;
                case 6: // UPLOAD_ERR_NO_TMP_DIR
                    $this->set_error('upload_no_temp_directory');
                    break;
                case 7: // UPLOAD_ERR_CANT_WRITE
                    $this->set_error('upload_unable_to_write_file');
                    break;
                case 8: // UPLOAD_ERR_EXTENSION
                    $this->set_error('upload_stopped_by_extension');
                    break;
                default :
                    $this->set_error('upload_no_file_selected');
                    break;
            }

            return FALSE;
        }

        $this->file_temp = $_FILES[$field]['tmp_name'];
        $this->file_size = $_FILES[$field]['size'];
        $this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES[$field]['type']);
        $this->file_type = strtolower($this->file_type);
        $this->file_ext  = $this->get_extension($_FILES[$field]['name']);
        $this->org_name_ = basename($_FILES[$field]['name']);


            $this->file_name = $this->_prep_filename($new_name);

        if ($this->file_size > 0) {
            $this->file_size = round($this->file_size/1024, 2);
        }

        if ( ! $this->is_allowed_filetype()) {
            $this->set_error('upload_invalid_filetype');
            return FALSE;
        }

        if ( ! $this->is_allowed_filesize()) {
            $this->set_error('upload_invalid_filesize');
            return FALSE;
        }

        if ( ! $this->is_allowed_dimensions()) {
            $this->set_error('upload_invalid_dimensions');
            return FALSE;
        }

        if ($this->max_filename > 0) {
            $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
        }

        if ($this->remove_spaces == TRUE) {
            $this->file_name = preg_replace("/\s+/", "_", $this->file_name);
        }

        $this->orig_name = $this->org_name_;

        if ($this->overwrite == FALSE) {
            $this->file_name = $this->set_filename($this->upload_path, $this->file_name);

            if ($this->file_name === FALSE)
            {
                return FALSE;
            }
        }

        if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name)) {
            if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name))
            {
                $this->set_error('upload_destination_error');
                return FALSE;
            }
        }

        if ($this->xss_clean == TRUE) {
            $this->do_xss_clean();
        }

        $this->set_image_properties($this->upload_path.$this->file_name);

        return TRUE;

    }
}