<?php
/*
* Buckty Header for meta tags and extra stuff
*/

if(!function_exists('BucktyHead'))
{
    /**
     * Generate buckty 's header details
     * User info
     * Folder info
     * Meta info
     */
      function BucktyHead(){
        $CI = get_instance();
        $CI->load->model('BucktySettings');
        $site = (object)$CI->BucktySettings->LoadSettings();
        $trans = (object) $CI->BucktySettings->getTrans();
        echo '<meta name="keywords" content="'.$site->site_keywords.'">';
        echo '<meta name="description" content="'.$site->site_description.'">';
        echo '<script id="js_main_objects" type="text/javascript">';
        if($CI->aauth->is_loggedin()):
            echo 'var userData = '. json_encode($CI->aauth->get_user()).';';
        endif;
        echo 'var site_url = "'. $site->site_url.'";';
        echo "var site_info = ".json_encode($site->site_info).';';
        echo "var csrf_token = '".$CI->security->get_csrf_hash()."';";
          if($CI->aauth->is_loggedin()):
        echo "var current_folder = '".$CI->uri->segment(2, 0)."';";
          else:
        echo "var current_folder = '404';";
          endif;
        echo "var tran = ".json_encode($trans).";";
        echo '</script>';
      }
}

if(!function_exists('BucktyAdminMenu')){

    /**
     * Create dynamic administrator dashboard menu
     */
    function BucktyAdminMenu(){
        $pages = array(
                 base_url().'admin' => array('pageTitle'=>'Dashboard','icon'=>'dashboard','attributes' => 'data-no-ajax="true"'),
            base_url().'admin/settings' => array(
                            'pageTitle' => 'Settings',
                                'icon'=>'gear',
                                'subpages' => array(
                                    base_url().'admin/settings' => 'Settings',base_url().'admin/settings/social' => 'Api\'s',base_url().'admin/settings/smtp'=>'Mail settings')),
            base_url().'admin/users' => array('pageTitle'=>'Users','icon'=>'users'),
            base_url().'admin/pages'  => array('pageTitle'=>'Pages','icon'=>'document',
                                        'subpages'=> array(base_url().'admin/pages/add'=>'Add',base_url().'admin/pages'=>'All pages')
                                        ),
            base_url().'admin/languages' => array('pageTitle'=>'Manage Languages','icon'=>'globe','attributes' => 'data-no-ajax="true"'),
            base_url().'admin/content' => array('pageTitle'=>'Content','icon'=>'file-o',
                                         'subpages'=> array(base_url().'admin/folders'=>'Folders',base_url().'admin/files'=>'Files')
                                         ),
                    ) ;
        ?>
    <ul class="nav navbar-nav">
        <?php 
                $currentPage = basename($_SERVER['REQUEST_URI']) ;
                foreach ($pages as $filename => $value):
                
                $pageTitle = is_array ($value) ? $value ['pageTitle'] : $value;   
                $attr_current = $filename == $currentPage ? ' current' : '';
                ?>
            <li class="<?php echo $attr_current; if (!empty($value['subpages'])): echo 'dropdown'; endif;?>">
                <a <?php if (empty($value['subpages'])): ?>href="<?=$filename?>" <?php endif;?>
                    <?php if (!empty($value['subpages'])): ?> class="dropdown-toggle" data-toggle="dropdown" <?php endif;?> aria-expanded="false">
                    <?=$pageTitle?>
                    <?php if (!empty($value['subpages'])): ?><span class="caret"></span><?php endif;?>
                </a>
                   <?php if (!empty($value['subpages'])): ?>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach ($value ['subpages'] as $subfilename => $subpageTitle): ?>
                            <li>
                                <a href="<?=$subfilename?>" <?php if(!empty($value[ 'attributes'])): echo $value[ 'attributes']; endif;?>>
                                    <i class="fa fa-<?php if(!empty($value['icon'])) echo $value['icon'];?>"></i>
                                    <span><?=$subpageTitle?></span></a>

                            </li>
                            <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
             </li>
            <?php endforeach; ?>
        </ul>
                <?php
    }
}

if(!function_exists('fileOrgSize')){

    /**
     * Convert normal bytes into readable format.
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    function fileOrgSize($bytes, $decimals = 2) {
    $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
}
if(!function_exists('secureFrom')){
function secureForm(){

    /**
     * Get token input field to be inserted into forms.
     */
    $CI = get_instance();
    $CI->load->model('BucktySettings');
    echo '<input type="hidden" name="'.$CI->security->get_csrf_token_name().'" value="'.$CI->security->get_csrf_hash().'">';
}
}
if(!function_exists('_check_token')){

    /**
     * Check specified token if valid or not
     * @param $token
     * @return bool
     */
    function _check_token ($token)
    {
          return ($token === $_COOKIE['csrf_Buckty']);
    }
}

if(!function_exists('generateRandomString')){
    /**
     * Generating random string
     * @param $length | default = 15
     * @return string
     */
    function generateRandomString($length = 15){
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('GetTree')){

    /**
     * Get sidebar tree folders.
     * @param string $hash
     * @param string $view
     * @param array $ignore
     */
    function GetTree($hash = '0',$view = 'side',$ignore = array()){
            $CI = get_instance();
            $user = $CI->aauth->get_user();
            $CI->load->model('BucktyContent');
              $site = (object)$CI->BucktySettings->LoadSettings();
            $folders = $CI->BucktyContent->GetTree($hash,$user->id);
            $root_href = $view == 'side' ? 'href="'.$site->site_url.'folders/"' : '';
            $class = $view == 'side' ? 'class="folder_"' : 'class="folder_ moveable"';
            if($hash == '0'){
            $sub ='<i data-move="side" class="fa sub_ico fa-caret-down"></i>';
            echo '<li>';
            echo $sub;
            echo '<a '.$root_href.' id="folder_0" '.$class.' data-id="0" data-item-type="folder">';
            echo '<i class="icon-folder-open icon_blue"></i><span class="f_name">Folders</span>';
            echo '</a>';
            echo '<ul class="root_main">';
            }
            if(!empty($folders)){
            foreach($folders as $folder){
                if(in_array($folder->folder_hash,$ignore)){
                    continue;
                }
                $sub = $folder->has_sub == 1 ? '<i data-move="side" class="fa sub_ico fa-caret-right"></i>': '';
                $href = $view == 'side' ?  'href="'.$site->site_url.'folders/'.$folder->folder_hash.'"': 'href="javascript:void(0);"';
                $class = $view == 'side' ? 'class="folder_"' : 'class="folder_ moveable"';
                echo '<li>';
                echo $sub;
                echo '<a '.$href.' id="Treefolder_'.$folder->folder_hash.'" '.$class.' data-in="n" data-item-type="folder" data-item="'.$folder->folder_hash.'" data-id="'.$folder->folder_hash.'">';
                echo '<i class="icon-folder icon_blue"></i><span class="f_name">'.$folder->folder_name.'</span>';
                echo '</a>';
                if($folder->has_sub){
                echo '<ul class="sub_folder">';
                    GetTree($folder->folder_hash,$view,$ignore);
                echo '</ul>';
                }
                echo '</li>';
            }
            } else {
                echo '<span class="no_subs">No Folders</span>';
            }
        if($hash == '0'){
            echo '</ul>';
         echo '</li>';
        }
    }
}

if(!function_exists('GenerateMsg')){

    /**
     * Generate request message
     * @param $msg
     * @param $err
     */
    function GenerateMsg($msg,$err){
         $message = array('msg' => $msg,'error_code'=>$err);
         echo json_encode($message);
    }
}

if(!function_exists('GenerateCrumbs')){

    /**
     * Generate breadcrumbs based on folder
     * @param $hash
     * @return array
     */
    function GenerateCrumbs($hash){
        if($hash == '0'){
            return array();
        }
        $CI = get_instance();
        $CI->load->model('BucktyContent');
        $CI->load->model('BucktySettings');
        $user_id = $CI->aauth->get_user()->id;
        $site = (object)$CI->BucktySettings->LoadSettings();
        $folder = $CI->BucktyContent->getFolderCrumbs($hash,$user_id);
        $BreadCrumbs = array();
            $BreadCrumbs[] = array('name'=>$folder[0]->folder_name,'link'=>$site->site_url.'folders/'.$folder[0]->folder_hash,'hash'=>$folder[0]->folder_hash);
            if(!$folder[0]->folder_parent == '0'){
            $BreadCrumbs = array_merge(GenerateCrumbs($folder[0]->folder_parent),$BreadCrumbs);
            }
        return $BreadCrumbs;
        
    }
}
if(!function_exists('_tran')){

    /**
     * Get translation key and echo it
     * @param $key
     */
    function _tran($key){
        echo $key['trans'];
    }
}
if(!function_exists('tran')){

    /**
     * Get translation key without direct echo
     * @param $key
     * @return mixed
     */
    function tran($key){
        return $key['trans'];
    }
}

if(!function_exists('ApiSettings')){

    /**
     * Get api settings from database
     * @return object
     */
    function ApiSettings(){
        $CI = get_instance();
        $CI->load->model('BucktySettings');
        $api = (object) $CI->BucktySettings->getApi();
        return $api;
    }
}
if(!function_exists('FolderSize')){
    /**
     * Get specific folder size
     * @param $hash
     */
    function FolderSize($hash){
        $CI = get_instance();
        $CI->load->model('BucktyContent');
        echo fileOrgSize($CI->BucktyContent->folderSize($hash));
    }
}

if(!function_exists('SiteAllowedSpace')){
    /**
     * Get total upload limit
     * @return mixed
     */
    function SiteAllowedSpace(){
        $CI = get_instance();
        $CI->load->model('BucktySettings');
        $site = (object)$CI->BucktySettings->LoadSettings();
        return $site->upload_limit;
    }
}

if(!function_exists('PexelsBakcground')){

    /**
     * Get pexels api and generate it's background image.
     * @return array|mixed|object
     */
    function PexelsBakcground(){
        $CI = get_instance();
        $CI->load->model('BucktySettings');
        $site = (object)$CI->BucktySettings->LoadSettings();
        $pexels = $site->site_info['pexels'];
        if(!empty($pexels['keywords']) && !empty($pexels['key'])):
        $keywords = explode(',',$pexels['keywords']);
        $keyword = $keywords[array_rand($keywords)];
        $keyword = trim($keyword);
        $key = $pexels['key'];
        if($pexels['active'] != '0'){
            $ch = curl_init();
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: '.$key

            );
            curl_setopt($ch, CURLOPT_URL, 'https://api.pexels.com/v1/search?query='.$keyword.'&per_page=3&page=1');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $body = '{}';

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Timeout in seconds
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $response = (array) json_decode($response);
            $response = $response['photos'][array_rand($response['photos'])];
            $response = (object) array('url'=>$response->url,'author'=>$response->photographer,'image'=>$response->src->large,'loaded'=>1);
            return $response;
        } else {
            return  (object) array('image'=>$site->site_url.'assets/img/background.jpg','loaded'=>0);

        }
        else:
            return (object) array('image'=>$site->site_url.'assets/img/background.jpg','loaded'=>0);
        endif;
    }
}