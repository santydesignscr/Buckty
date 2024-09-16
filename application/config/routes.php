<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.s
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

/*
 * Administrator Routings
 *
 */

$route['admin'] = 'AdminController';
$route['admin/settings'] = 'AdminController/settings';
$route['admin/settings/social'] = 'AdminController/socialSettings';
$route['admin/ajaxcall'] = 'AdminController/ajaxCall';
$route['main/ajaxcall'] = 'MainController/ajaxCall';
$route['admin/languages'] = 'AdminController/languageManager';
$route['admin/users'] = 'AdminController/getUsers';
$route['admin/users/(:num)'] = 'AdminController/getUsers';
$route['admin/folders'] = 'AdminController/getFolders';
$route['admin/folders/(:num)'] = 'AdminController/getFolders';
$route['admin/files'] = 'AdminController/getFiles';
$route['admin/files/(:num)'] = 'AdminController/getFiles';
$route['admin/removeuser'] = 'AdminController/removeUsers';
$route['admin/edit_user'] = 'AdminController/getUser';
$route['admin/update_user'] = 'AdminController/updateUser';
$route['admin/adduser'] = 'AdminController/create_user';
$route['admin/translation'] = 'AdminController/saveTranslation';
$route['admin/searchusers'] = 'AdminController/searchUsers';
$route['admin/searchitem'] = 'AdminController/searchItems';
$route['admin/userban'] = 'AdminController/userBan';
$route['admin/pages/add'] = 'AdminController/addPage';
$route['admin/pages'] = 'AdminController/allPages';
$route['admin/pages/edit/(:num)'] = 'AdminController/editPage';
$route['admin/publishpage'] = 'AdminController/publishPage';
$route['admin/savepage'] = 'AdminController/savePage';
$route['admin/loadstats'] = 'AdminController/getStats';
$route['admin/removepage'] = 'AdminController/removePage';
$route['admin/removekey'] = 'AdminController/removeKey';
$route['admin/settings/smtp'] = 'AdminController/smtpSettings';
$route['admin/updatesmtp'] = 'AdminController/updateSmtpSettings';

/*
 *  Homepage routings
 *
 */
$route['default_controller'] = 'MainController';
$route['checklog'] = 'MainController/login';
$route['createu'] = 'MainController/create_user';
$route['logout'] = 'MainController/logout';
$route['login/(:any)'] = 'Hauth/login_social';
/*
 * Front Dashboard routings
 *
 */
$route['profile'] = 'MainController/profile';
$route['user/settings'] = 'MainController/userSettings';
$route['user/update'] = 'MainController/updateUser';
$route['user/verification/(:num)/(:any)'] = 'MainController/activate';
$route['user/reset_password/(:any)/(:any)'] = 'MainController/loadResetPass';
$route['recover/password'] = 'MainController/ResetPassword';
$route['recover/resetpassword'] = 'MainController/resetPass';
$route['buckty/login'] = 'MainController/loginAgain';
$route['loadpopup'] = 'ContentController/create_popup';
$route['useraction/update_pic'] = 'MainController/update_pic';
$route['useraction/share'] = 'ContentController/Share';
$route['useraction/itemdetails'] = 'ContentController/Details';
$route['useraction/itempreview'] = 'ContentController/previewItem';
$route['useraction/moveitem'] = 'ContentController/moveItem';
$route['useraction/renameitem'] = 'ContentController/RenameItem';
$route['useraction/removeitem'] = 'ContentController/RemovePerma';
$route['useraction/removestar'] = 'ContentController/RemoveStar';
$route['useraction/copyitem'] = 'ContentController/copyItem';
$route['useraction/staritem'] = 'ContentController/StarItem';
$route['useraction/itemShare'] = 'ContentController/ItemShare';
$route['useraction/unlinkuser'] = 'ContentController/unshareUser';
$route['useraction/itemdetails'] = 'ContentController/detailsItem';
$route['useraction/getusers'] = 'MainController/getUsers';
$route['useraction/context'] = 'ContentController/contextDetails';
$route['useraction/notes'] = 'ContentController/getNotifications';
$route['useraction/checkn'] = 'ContentController/checkNotes';
$route['useraction/deleten'] = 'ContentController/deleteNote';
$route['useraction/email'] = 'ContentController/emailShare';
$route['useraction/additempassword'] = 'ContentController/protectItem';
$route['useraction/changepermission'] = 'ContentController/changePermission';
$route['useraction/createfolder'] = 'ContentController/createFolder';
$route['useraction/uploadfiles'] = 'UploadController/uploadfiles';
$route['useraction/deleteitem'] = 'ContentController/DeleteItem';
$route['useraction/restore'] = 'ContentController/RestoreItem';
$route['useraction/tree'] = 'ContentController/foldersTree';
$route['userfile/(:any)'] = 'SharedController/viewFile';
$route['profilepic/(:any)'] = 'MainController/getImage';
$route['trash'] = 'ContentController/GetTrash';
$route['buckty/userspace'] = 'ContentController/userSpace';
$route['search'] = 'ContentController/SearchItems';
$route['useraction/get/(:any)'] = 'DownloadController/DownloadItem';
$route['useraction/getzip/(:any)'] = 'DownloadController/DownloadZip';
$route['useraction/zip'] = 'DownloadController/DownloadMulti';
$route['useraction/api'] = 'MainController/UserApiDetails';
$route['useraction/generateapi'] = 'MainController/GenerateUserApi';
$route['folders/(:any)'] = 'ContentController/getContent';
$route['folders'] = 'ContentController/getContent';
$route['starred'] = 'ContentController/getStarredContent';
$route['shared'] = 'ContentController/getSharedContent';
$route['recent'] = 'ContentController/getRecentContent';
$route['folderList'] = 'ContentController/folderList';
$route['fetchSub'] = 'ContentController/fetchSub';

/*
 * Dropbox api routing
 *
 */
$route['dropbox'] = 'DropBoxController';
$route['dropbox/end'] = 'DropBoxController/DropEnd';
$route['dropbox/push'] = 'DropBoxController/Upload';
$route['dropbox/disconnect'] = 'DropBoxController/removeAuth';
$route['dropbox_list'] = 'DropBoxController/getList';
$route['dropbox/dbget'] = 'DropBoxController/getDbFile';

/*
 * Google drive routing
 *
 */
$route['gdrive'] = 'GdriveController/Gauth';
$route['gdrive/end'] = 'GdriveController/endPoint';
$route['gdrive/push'] = 'GdriveController/UploadFile';
$route['gdrive/get'] = 'GdriveController/getFileToServer';
$route['gdrive/disconnect'] = 'GdriveController/removeGdrive';
$route['drive_list'] = 'GdriveController/getList';

/*
 * Shared link routings
 *
 */
$route['shared/(:any)/(:any)'] = 'SharedController/View';

/*
 * Paging routes
 *
 */
$route['page/(:any)'] = 'MainController/getPage';

/*
 * 404 route
 */
$route['404_override'] = 'MainController/Error_404';



/*
 *  Simple api routes
 *
 */
$route['api/request']  = 'api/ApiController/RequestUpload';

$route['api/user']     = 'api/ApiController/userInfo';

$route['api/login']    = 'api/ApiController/loginUser';

$route['api/files']    = 'api/ApiController/ApiFiles';

$route['api/folders']    = 'api/ApiController/ApiFolder';

$route['api/file']     = 'api/ApiController/ApiGetFile';

$route['api/folder']     = 'api/ApiController/ApiGetFolder';
$route['translate_uri_dashes'] = FALSE;