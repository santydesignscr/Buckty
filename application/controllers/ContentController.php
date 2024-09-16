<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class ContentController
 */
class ContentController extends BUCKTY_Content
  {
    /**
     * @var array
     */
    private $content = array();
    /**
     * @var array
     */
    private $folders = array();
    /**
     * @var array
     */
    private $files = array();
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
    public $csrf_token;

    /**
     * ContentController constructor.
     */
    public function __construct()
      {
        parent::__construct();
        $this->csrf_token = $this->input->get('csrf_Buckty') != '' ? 
                            $this->input->get('csrf_Buckty') :
                            $this->input->post('csrf_Buckty');
        
        if ($this->aauth->is_loggedin())
          {
            $this->current_user = $this->logged_data();
          }
        else
          {
            if ($this->input->is_ajax_request()):
                $message = array(
                    'message' => 'Please Login Again',
                    'error_code' => 1,
                    'login' => 'false'
                );
                echo json_encode($message);
                exit();
            else:
                redirect($this->site->site_url);
            endif;
          }
      }

    /**
     *  Get main dashboard content based on logged in user.
     */
    public function getContent()
      {
        if ($this->aauth->is_loggedin()) {
            $this->content['folders'] = array();
            $this->folder = $this->uri->segment(2, 0);
            $isFolder = $this->getFolder($this->folder, $this->current_user->id);
            if (empty($isFolder) && ($this->folder != '0')):
                redirect($this->site->site_url);
            endif;
            $this->folders = $this->getFolders($this->current_user->id, $this->folder);
            $this->treeFolders = $this->getTreeFolders($this->current_user->id, 0);
            $this->getFiles = $this->getFiles($this->current_user->id, $this->folder);
            $this->content['folderCrumb'] = GenerateCrumbs($this->folder);
            $content_folder = $this->folders;
            $this->content['folders'] = $content_folder;
            $this->content['treeFolders'] = $this->treeFolders;
            $this->content['in'] = 'n';
            $this->content['files'] = $this->getFiles;
            $this->content['main_content'] = 'content';
            $this->load->view('main', $this->content);
        } else {
            redirect($this->site->site_url);
        }
      }

    /**
     * Get main dashboard starred content.
     */
    public function getStarredContent()
      {
        if ($this->aauth->is_loggedin()):
            $this->content['folders']      = array();
            $this->folder                  = $this->uri->segment(2, 0);
            $this->folders                 = $this->getFolders($this->current_user->id, $this->folder,'starred');
            $this->treeFolders             = $this->getTreeFolders($this->current_user->id, 0);
            $this->getFiles                = $this->getFiles($this->current_user->id, $this->folder,'starred');
            $this->content['folderCrumb']  = GenerateCrumbs($this->folder);
            $content_folder                = $this->folders;
            $this->content['folders']      = $content_folder;
            $this->content['treeFolders']  = $this->treeFolders;
            $this->content['in']           = 's';
            $this->content['files']        = $this->getFiles;
            $this->content['main_content'] = 'content';
            $this->load->view('main', $this->content);
        else:
            redirect($this->site->site_url);
        endif;
    }

    /**
     * Get main dashboard recent content
     */
    public function getRecentContent()
    {
        if ($this->aauth->is_loggedin()):
            $this->content['folders']      = array();
            $this->folder                  = $this->uri->segment(2, 0);
            $this->folders                 = array();
            $this->treeFolders             = $this->getTreeFolders($this->current_user->id, 0);
            $this->getFiles                = $this->getFiles($this->current_user->id, $this->folder,'recent');
            $this->content['folderCrumb']  = GenerateCrumbs($this->folder);
            $content_folder                = $this->folders;
            $this->content['folders']      = $content_folder;
            $this->content['treeFolders']  = $this->treeFolders;
            $this->content['in']           = 's';
            $this->content['files']        = $this->getFiles;
            $this->content['main_content'] = 'content';
            $this->load->view('main', $this->content);
        else:
            redirect($this->site->site_url);
        endif;
    }

    /**
     * Get main dashboard shared content.
     */
    public function getSharedContent()
      {
        if ($this->aauth->is_loggedin()):
            $this->content['folders']      = array();
            $this->folder                  = $this->uri->segment(2, 0);
            $this->folders                 = $this->getFolders($this->current_user->id, $this->folder,'shared');
            $this->treeFolders             = $this->getTreeFolders($this->current_user->id, 0);
            $this->getFiles                = $this->getFiles($this->current_user->id, $this->folder,'shared');
            $this->content['folderCrumb']  = GenerateCrumbs($this->folder);
            $content_folder                = $this->folders;
            $this->content['folders']      = $content_folder;
            $this->content['treeFolders']  = $this->treeFolders;
            $this->content['in']           = 's';
            $this->content['files']        = $this->getFiles;
            $this->content['main_content'] = 'content';
            $this->load->view('main', $this->content);
        else:
            redirect($this->site->site_url);
        endif;
    }

    /**
     * Get main dashboard deleted content.
     */
    public function GetTrash()
      {
        if ($this->aauth->is_loggedin()):
            $this->content['folders']      = array();
            $this->folder                  = $this->uri->segment(2, 0);
            $this->folders                 = $this->getTrashFolders($this->current_user->id);
            $this->treeFolders             = $this->getTreeFolders($this->current_user->id, 0);
            $this->getFiles                = $this->getTrashFiles($this->current_user->id);
            $this->content['folders']      = $this->folders;
            $this->content['treeFolders']  = $this->treeFolders;
            $this->content['in']           = 't';
            $this->content['files']        = $this->getFiles;
            $this->content['main_content'] = 'content';
            $this->load->view('main', $this->content);
        else:
            redirect($this->site->site_url);
        endif;
      }

    /**
     * Fetch sub folder's for requested folder.
     */
    public function fetchSub()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if ($this->input->is_ajax_request())
          {
            $data = $this->BucktyContent->GetSub($this->input->get('hash'));
            echo json_encode($data);
          }
        else
          {
            $message = array(
                'message' => 'Direct access denied',
                'error_code' => 1
            );
            echo json_encode($message);
          }
      }

    /**
     * Create folder method.
     */
    public function createFolder()
      {
        //check if csrf token is valid or not.
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }


        // Check if it's ajax request else show access denied.
        if ($this->input->is_ajax_request())
          {
            // Validate the posted value for form.
            $this->load->library('form_validation');
            $this->form_validation->set_rules('folder_name', 'Folder Name', 'trim|required');

             // check if form is valid or not if yes then proceed with creation else show errors.
            if ($this->form_validation->run() !== false):

                /*
                 * Pass the values to model for folder creation and get response
                 */
                $folder  = $this->makeFolder(
                    $this->current_user->id, 
                    $this->input->post('folder_name'),
                    $this->input->post('folder_in'));


                $message = array(
                    'message' => tran($this->trans->Folder_created),
                    'error_code' => 0,
                    'folder_hash' => $folder['hash'],
                    'date'=>date('Y-m-d H:i:s'),
                    'folder_name' => $this->input->post('folder_name', TRUE));
                echo json_encode($message);
            else:
                /*
                * Show validation errors.
                */
                $message = array(
                    'message' => validation_errors(),
                    'error_code' => 1,
                    'type' => 'log'
                );
                echo json_encode($message);
            endif;
          } else {
            $message = array(
                'msg' => 'Direct Access Denied',
                'error_code' => 1
            );
            echo json_encode($message);
          }
      }

    /**
     * Get folders list for popup view.
     */
    public function folderList()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if ($this->input->is_ajax_request())
          {
            $items  = $this->input->post('items');
            $ignore = array();
            foreach ($items as $item)
              {
                $item     = explode('/', $item);
                $ignore[] = $item[0];
              }
            $this->treeFolders        = $this->getFolders($this->current_user->id, 0);
            $this->content['folders'] = $this->treeFolders;
            $this->content['ignore']  = $ignore;
            $this->load->view('popups/folders', $this->content);
          }
        else
          {
            $message = array(
                'msg' => 'Direct Access Denied',
                'error_code' => 1
            );
            echo json_encode($message);
          }
      }

    /**
     * Delete item completely fromd database and from directory
     */
    public function DeleteItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if ($this->input->is_ajax_request()):
            $items = $this->input->post('items');
            $total = count($items);
            if (empty($items))
              {
                $message = array(
                    'message' => 'Invalid Item',
                    'error_code' => 1
                );
                echo json_encode($message);
                exit();
              }
            foreach($items as $item) {
                $item = explode('/', $item);
                $type = $item[1];
                $item = $item[0];
                $errors = array();
                switch ($type) {
                    case 'file':
                        if (!$this->actionFile($item, 'remove')) {
                            $error[] = '1';
                        }
                        break;
                        case 'folder':
                        if (!$this->actionFolder($item, 'remove')) {
                            $error[] = '1';
                        }
                        break;
                }
            }

            if(empty($errors)){
                $message = array(
                    'message' => $total.' '.tran($this->trans->items_were_removed),
                    'error_code' => 0
                );
            } else {
                $message = array(
                    'message' => tran($this->trans->Something_went_wrong),
                    'error_code' => 1
                );
            }
            echo json_encode($message);
        else:
            $message = array(
                'message' => 'Direct access denied',
                'error_code' => 1
            );
            echo json_encode($message);
        endif;
    }

    /**
     * Restore item from trash into normal dashboard
     */
    public function RestoreItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if ($this->input->is_ajax_request()):
            $items = $this->input->post('hash');
            $items_count = count($items);
            foreach($items as $item) {
                $item = explode('/', $item);
                if ($item[1] == '') {
                    $message = array(
                        'message' => 'Invalid Item',
                        'error_code' => 1
                    );
                    echo json_encode($message);
                    exit();
                }
                switch ($item[1]) {
                    case 'file':
                        if ($this->actionFile($item[0], 'restore')) {
                            $message = array(
                                'message' => $items_count.' '.tran($this->trans->item_Was_Restored),
                                'error_code' => 0
                            );
                        } else {
                            $message = array(
                                'message' => tran($this->trans->Something_went_wrong),
                                'error_code' => 1
                            );
                        }
                        break;

                    case 'folder':
                        if ($this->actionFolder($item[0], 'restore')) {
                            $message = array(
                                'message' => $items_count.' '.tran($this->trans->item_Was_Restored),
                                'error_code' => 0
                            );
                        } else {
                            $message = array(
                                'message' => tran($this->trans->Something_went_wrong),
                                'error_code' => 1
                            );
                        }
                        break;
                }
            }
            echo json_encode($message);
        else:
            $message = array(
                'message' => 'Direct access denied',
                'error_code' => 1
            );
            echo json_encode($message);
        endif;
      }

    /**
     * Move item from one folder to another.
     */
    public function moveItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if ($this->input->is_ajax_request()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('hash', 'Folder Code', 'trim|required');
            if ($this->form_validation->run() !== false):
                $response = $this->BucktyContent->moveItem(
                    $this->current_user->id, 
                    $this->input->post('hash'), 
                    $this->input->post('files_hash'));
                if($response->error_code == 0):
                    $count = count($this->input->post('files_hash'));
                    $msg = $count > 1 ? tran($this->trans->items_were_moved): tran($this->trans->item_was_moved);
                    $message = array(
                        'message' => $count . ' | '.$msg,
                        'error_code' => 0
                    );
                    echo json_encode($message);
                elseif($response->error_code === 2):
                    $message = array(
                        'message' => tran($this->trans->Not_enough_space),
                        'error_code' => 1
                    );
                    echo json_encode($message);
                endif;
            else:
                $message = array(
                    'message' => validation_errors(),
                    'error_code' => 1,
                    'type' => 'log'
                );
                echo json_encode($message);
            endif;
          } else {
            $message = array(
                'msg' => 'Direct Access Denied',
                'error_code' => 1
            );
            echo json_encode($message);
          }
      }

    /**
     * Renme file or folder.
     */
    public function RenameItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $data         = array();
        $data['hash'] = $this->input->post('hash');
        $data['type'] = $this->input->post('type');
        $data['name'] = $this->input->post('item_name');
        if ($this->BucktyContent->RenameItem($data))
          {
            $data    = $data['type'] == 'file' ? 
                       $this->getFile($data['hash'], $this->current_user->id) : 
                       $this->getFolder($data['hash'], $this->current_user->id);
            
            $message = array(
                'message' => tran($this->trans->item_was_renamed),
                'error_code' => 0,
                'data' => $data
            );
            echo json_encode($message);
          }
        else
          {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong),
                'error_code' => 1
            );
            echo json_encode($message);
          }
      }

    /**
     * Remove file or folder permanently
     */
    public function RemovePerma()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
          if (!$this->input->is_ajax_request())
          {
              $message = array(
                  'message' => 'Direct Access Denied',
                  'error_code' => 1
              );
              echo json_encode($message);
              exit();
          }
        $data['hash'] = $this->input->post('hash');
        $total = count($data['hash']);
        $action = $this->input->post('who');
        $data['user'] = $this->aauth->is_admin() && $action == 2 ? 'admin' :$this->current_user->id;
        if ($this->BucktyContent->RemovePerma($data))
          {
            $msg = $total > 1 ?   tran($this->trans->items_were_removed_permanently): tran($this->trans->item_was_removed_permanently);
            $message = array(
                'message' => $total.' '.$msg
            );
            GenerateMsg($message, 0);
          }
        else
          {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong)
            );
            GenerateMsg($message, 0);
          }
      }

    /**
     * Get request and show a popup based on what requested
     */
    public function create_popup()
    {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if (!$this->input->is_ajax_request())
          {
            $message = array(
                'message' => 'Direct Access Denied',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        switch ($this->input->get('pop'))
        {
            case 'profile_pic':
                $this->load->view('popups/profile_pic');
                break;
            
            case 'create_folder':
                $this->load->view('popups/folder');
                break;
        }
      }

    /**
     * Share file or folder to other users
     */
    public function Share()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $hash          = $this->input->get('hash');
        $item          = explode('/', $hash);
        $item_data     = $item[1] == 'file' ? 
                         $this->BucktyContent->getFile($item[0],$this->current_user->id) :
                         $this->BucktyContent->getFolder($item[0],$this->current_user->id);
        $users_data    = $item[1] == 'file' ? 
                         $this->BucktyContent->getFileUsers($item[0]) : 
                         $this->BucktyContent->getFolderUsers($item[0]);
        
        $data['type']  = $item[1];
        $data['link']  = $item[1] == 'file' ? 
                         base_url() . 'shared/file/' . $item_data->hash : 
                         base_url() . 'shared/folder/' . $item_data->folder_hash;
        $data['hash']  = $item[1] == 'file' ?
                         $item_data->hash :
                         $item_data->folder_hash;
        
        $data['item']  = $item_data;
        $data['users'] = $users_data;
        $this->load->view('popups/share', $data);
      }

    /**
     * Add items to favorite.
     */
    public function StarItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $hash     = $this->input->post('hash');
        $item     = explode('/', $hash);
        $response = $item[1] == 'file' ? 
                    $this->BucktyContent->starFile($item[0], $this->current_user->id) : 
                    $this->BucktyContent->starFolder($item[0], $this->current_user->id);
        if ($response)
          {
            $message = array(
                'message' => tran($this->trans->Item_was_starred)
            );
            GenerateMsg($message, 0);
          }
        else
          {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong)
            );
            GenerateMsg($message, 1);
          }
      }

    /**
     * Remove items from favorite.
     */
    public function RemoveStar()
      {
        if (!(_check_token($this->csrf_token))):
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
         endif;
        
        $hash     = $this->input->post('hash');
        $item     = explode('/', $hash);
        $response = $item[1] == 'file' ? 
                    $this->BucktyContent->starFile($item[0], $this->current_user->id, 1) : 
                    $this->BucktyContent->starFolder($item[0], $this->current_user->id, 1);
        if ($response):
            $message = array(
                'message' => tran($this->trans->Item_was_un_starred)
            );
            GenerateMsg($message, 0);
        else:
            $message = array(
                'message' => tran($this->trans->Item_was_un_starred)
            );
            GenerateMsg($message, 1);
        endif;
        
      }

    /**
     * Share item with other users.
     */
    public function ItemShare()
      {
          if (!(_check_token($this->csrf_token))):
              $message = array(
                  'message' => 'Invalid csrf Token',
                  'error_code' => 1
              );
              echo json_encode($message);
              exit();
          endif;
        $hash      = $this->input->post('hash');
        $user_hash = $this->input->post('user');
        $permission = $this->input->post('permission');
        $item      = explode('/', $hash);
        foreach($user_hash as $uhash){
        $user      = $this->aauth->get_user_id_by_hash($uhash);
        $response  = $item[1] == 'file' ? 
                    $this->BucktyContent->shareFile($item[0],$user,$this->current_user->id,$permission) :
                     $this->BucktyContent->shareFolder($item[0], $user,$this->current_user->id,$permission);
        }
          if ($response):
            $message = array(
                'message' => tran($this->trans->Item_was_shared),
                'data' => array('type'=>$item[1],'item'=>$item[0])
            );
            GenerateMsg($message, 0);
        else:
            $message = array(
                'message' => 'Something went wrong',
                'data' => array('type'=>$item[1],'item'=>$item[0])
            );
            GenerateMsg($message, 1);
        endif;
      }
    public function unshareUser()
    {
        if (!(_check_token($this->csrf_token))):
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
        $hash      = $this->input->post('item');
        $user      = $this->input->post('user');
        $user      = $this->aauth->get_user_id_by_hash($user);
        $item      = explode('/', $hash);
        $response  = $item[1] == 'file' ?
            $this->BucktyContent->unshareFile($item[0],$user,$this->current_user->id) :
            $this->BucktyContent->unshareFolder($item[0], $user,$this->current_user->id);

        if ($response):
            $message = array(
                'message' => tran($this->trans->User_was_unlinked_from_item)
            );
            GenerateMsg($message, 0);
        else:
            $message = array(
                'message' => 'Something went wrong'
            );
            GenerateMsg($message, 1);
        endif;
    }

    /**
     * Generate random string.
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 15)
      {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++)
          {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
          }
        return $randomString;
      }

    /**
     * Search for files / folders
     */
    public function SearchItems()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $term             = $this->input->get('s');
        $items['files']   = $this->BucktyContent->GetContent('files', null, $this->current_user->id, $term);
        $items['folders'] = $this->BucktyContent->GetContent('folder', null, $this->current_user->id, $term);
        echo json_encode($items);
      }

    /**
     * Get item details and show them in sidebar
     */
    public function detailsItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $item          = $this->input->get('hash');
        $data          = array();
        $item_         = explode('/', $item);
        $item          = $item_[1] == 'file' ? $this->BucktyContent->getFile($item_[0]) : $this->BucktyContent->getFolder($item_[0]);
        $users_data    = $item_[1] == 'file' ? $this->BucktyContent->getFileUsers($item_[0]) : $this->BucktyContent->getFolderUsers($item_[0]);
        $item_author   = $item_[1] == 'file' ? $item->file_author : $item->folder_author;
        $data['item_parent']   = $this->getFolder($item->content_parent);
        $data['item']  = $item;
        $data['type']  = $item_[1];
        $data['users'] = $users_data;
        $data['item_owner'] = $this->aauth->get_user($item_author);
        $this->load->view('contents/filepreview', $data);
      }

    /**
     * Preview item in popup.
     */
    public function previewItem()
      {
        if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        $item         = $this->input->get('hash');
        $data         = array();
        $item_        = explode('/', $item);
        $item         = $item_[1] == 'file' ? $this->BucktyContent->getFile($item_[0]) : $this->BucktyContent->getFolder($item_[0]);
        $users_data   = $item_[1] == 'file' ? $this->BucktyContent->getFileUsers($item_[0]) : $this->BucktyContent->getFolderUsers($item_[0]);
        $data['item'] = $item;
        $textTypes = "application/pdf application/vnd.oasis.opendocument.text application/vnd.oasis.opendocument.text-flat-xml application/vnd.oasis.opendocument.text-template application/vnd.oasis.opendocument.presentation application/vnd.oasis.opendocument.presentation-flat-xml application/vnd.oasis.opendocument.presentation-template application/vnd.oasis.opendocument.spreadsheet application/vnd.oasis.opendocument.spreadsheet-flat-xml application/vnd.oasis.opendocument.spreadsheet-template application/vnd.ms-office application/msword application/vnd.ms-excel";
        $data['texttypes'] = explode(' ',$textTypes);
        $data['type'] = $item_[1];
        $data['hash'] = $data['type'] == 'file' ? $item->hash : $item->folder_hash;
        if ($data['type'])
          {
            $data['full_path'] = $this->Filepath($item);
          }
        $data['users'] = $users_data;
        $this->load->view('popups/view', $data);
      }

    /**
     * Check if file exsits or not.
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
     * Get notifications list.
     */
    public function getNotifications(){
         if (!(_check_token($this->csrf_token)))
          {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
          }
        if (!$this->input->is_ajax_request()):
            $message = array(
                'message' => 'Direct Access denied',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
        
        $type = $this->input->get('t');
        $notes = $this->BucktyContent->getNotes($this->current_user->id,$type);
        $this->BucktyContent->markRead($this->current_user->id);
        echo json_encode($notes);
    }

    /**
     * Check notifications , get count of pending notifications.
     */
    public function checkNotes(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()):
            $message = array(
                'message' => 'Direct Access denied',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
        $notes = $this->BucktyContent->getNotes($this->current_user->id,'get','n');
        $un_read_notes = $this->BucktyContent->getNotes($this->current_user->id,'get','u');
        $count_n = count($notes);
        $count_u = count($un_read_notes);
        $message = tran($this->trans->No_unread_notifications);
        if($count_n > 0) {
            $message = str_replace('%notes%',$count_n,tran($this->trans->you_have_unread_notes));
        }
        $data = array('notes_count'=>$count_n,'notes_unread'=>$count_u,'message'=>$message);
        echo json_encode($data);
    }

    /**
     * Delete notificaion
     */
    public function deleteNote(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()):
            $message = array(
                'message' => 'Direct Access denied',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
        $noteid = $this->input->post('id');
        $noteTerm = $this->input->post('term');
        if($noteTerm == 'multi'){
            $noteid = $this->current_user->id;
        }
        $response = $this->BucktyContent->deleteNote($noteid,$noteTerm);
        if($response){
            $message = array(
                'message' => tran($this->trans->Notification_removed),
                'error_code' => 0
            );
            echo json_encode($message);
            exit();
        } else {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong),
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
    }

    /**
     * Get folders tree
     */
    public function foldersTree(){
        if (!$this->input->is_ajax_request()):
            redirect($this->site->site_url);
        endif;
        echo GetTree('0','side');
    }

    /**
     * Get details about file when right clicked on it.
     */
    public function contextDetails(){
        if (!(_check_token($this->csrf_token))) {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if (!$this->input->is_ajax_request()):
            $message = array(
                'message' => 'Direct Access denied',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        endif;
        $hash = $this->input->get('item');
        $item = explode('/',$hash);
        $response = $item[1] == 'file' ? $this->getFile($item[0],$this->current_user->id): $this->getFolder($item[0],$this->current_user->id);
        echo json_encode($response);
    }

    /**
     * Make another copy of file.
     */
    public function copyItem(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $fileid = $this->input->post('hash');
        $folder = $this->input->post('parent');
        $file = $this->getFile($fileid);
        if(!empty($file)) {
            $occu = $this->userUsedSpace(null, 0) + $file->file_size;
            if ($occu > $this->site->upload_limit):
                $message = array('message' => tran($this->trans->Not_enough_space));
                GenerateMsg($message, 0);
                exit();
            endif;
        }
        $path = FCPATH.'application/views/uploads/content'.$file->file_path.$file->file_preview;
        $copyname = $this->generateRandomString(15).'.'.$file->file_ext;
        $path_new = FCPATH.'application/views/uploads/content'.$file->file_path.$copyname;
        copy($path,$path_new);
        $hash = $this->generateRandomString(10);
        $file_ = array('file_name'=>$file->file_name.'copy',
            'hash'=>$hash
        );
        $file_id = $this->BucktyContent->insertFile($this->config_vars['files'],$file_);
        if(is_numeric($file_id)):
            $file_data = array('file_id'=>$file_id,
                'file_author'=>$this->current_user->id,
                'file_mime'=>$file->file_mime,
                'file_date'=> date('Y-m-d H:i:s'),
                'file_size'=>$file->file_size,
                'image_width'=>$file->image_width,
                'image_height'=>$file->image_height,
                'file_preview'=>$copyname,
                'file_path' => $file->file_path,
                'file_type'=>$file->file_ext,
                'file_ext'=>$file->file_ext
            );
            $relation = array('author_id'=>$this->current_user->id,
                'content_id'=>$file_id,
                'content_parent'=>$folder,
                'content_type'=>'file',
                'permission'=>1,
                'owner'=>1);
            $this->BucktyContent->insertFile($this->config_vars['files_data'],$file_data);
            $this->BucktyContent->insertFile($this->config_vars['relations'],$relation);

            $file_data = $this->getFile($hash,$this->logged_data()->id);
            GenerateMsg(array('message'=>tran($this->trans->Item_was_copied),'file'=>$file_data),0);
            endif;
    }

    /**
     * Share item by sending into email.
     */
    public function emailShare(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        $hash = $this->input->post('hash');
        $item = explode('/',$hash);
        $users = $this->input->post('users');
        if($users == '' || $users == null){
            GenerateMsg(array('message'=>tran($this->trans->Email_addresses_cant_be_empty)),1);
            exit();
        }
        $data['link']   = $this->site->site_url.'shared/'.$item[1].'/'.$item[0];
        $data['message'] = $this->input->post('message');
        $this->batch_email($users,tran($this->trans->email_share_subject),$this->load->view('email/email_share',$data,true));
        GenerateMsg(array('message'=>$item[1].' '.tran($this->trans->link_was_sent)),0);
    }

    /**
     * Set password to a specific file.
     */
    public function protectItem(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if(!$this->input->is_ajax_request()){
            redirect($this->site->site_url);
            exit();
        }
        $password = $this->input->post('password');
        $item = $this->input->post('item');
        if($this->BucktyContent->protectItem($item,$password)){
            $message = array(
                'message' => tran($this->trans->The_password_was_updated),
                'error_code' => 0
            );
            echo json_encode($message);
        } else {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong),
                'error_code' => 1
            );
            echo json_encode($message);
        }
    }

    /**
     * Change permission of file or folder if it's shared.
     */
    public function changePermission(){
        if (!(_check_token($this->csrf_token)))
        {
            $message = array(
                'message' => 'Invalid csrf Token',
                'error_code' => 1
            );
            echo json_encode($message);
            exit();
        }
        if(!$this->input->is_ajax_request()){
            redirect($this->site->site_url);
            exit();
        }
        $user = $this->input->post('u');
        $file = $this->input->post('i');
        $type = $this->input->post('t');
        $permission = $this->input->post('p');
        if($this->BucktyContent->changePer($user,$file,$type,$permission)){
            $message = array(
                'message' => tran($this->trans->Permission_was_changed),
                'error_code' => 0
            );
            echo json_encode($message);
        } else {
            $message = array(
                'message' => tran($this->trans->Something_went_wrong),
                'error_code' => 0
            );
            echo json_encode($message);
        }
    }

  }