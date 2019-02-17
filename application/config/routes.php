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
|	https://codeigniter.com/user_guide/general/routing.html
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
| would be loaded.
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
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*Api*/
//common
$route['_api/common/(:any)'] = 'common_api/$1';
//user
$route['_api/user/(:any)'] = 'user_api/$1';
//category
$route['_api/category/(:any)'] = 'category_api/$1';
$route['_api/category/(:any)/(:any)'] = 'category_api/$1/$2';

//news
$route['_api/news/(:any)'] = 'news_api/$1';
$route['_api/news/(:any)/(:any)'] = 'news_api/$1/$2';
//record
$route['_api/record/(:any)'] = 'record_api/$1';
$route['_api/record/(:any)/(:any)'] = 'record_api/$1/$2';
//order
$route['_api/order/(:any)'] = 'order_api/$1';
$route['_api/order/(:any)/(:any)'] = 'order_api/$1/$2';
//banner
$route['_api/banner/(:any)'] = 'banner_api/$1';
$route['_api/banner/(:any)/(:any)'] = 'banner_api/$1/$2';
//webview
$route['_api/webview/(:any)'] = 'webview_api/$1';
$route['_api/webview/(:any)/(:any)'] = 'webview_api/$1/$2';

//admin_record_api
$route['_api/admin/record/(:any)'] = 'admin_record_api/$1';
$route['_api/admin/record/(:any)/(:any)'] = 'admin_record_api/$1/$2';

//admin_category_api
$route['_api/admin/category/(:any)'] = 'admin_category_api/$1';
$route['_api/admin/category/(:any)/(:any)'] = 'admin_category_api/$1/$2';

//admin_user_api
$route['_api/admin/user/(:any)'] = 'admin_user_api/$1';
$route['_api/admin/user/(:any)/(:any)'] = 'admin_user_api/$1/$2';

//admin_news_api
$route['_api/admin/news/(:any)'] = 'admin_news_api/$1';
$route['_api/admin/news/(:any)/(:any)'] = 'admin_news_api/$1/$2';

//admin_banner_api
$route['_api/admin/banner/(:any)'] = 'admin_banner_api/$1';
$route['_api/admin/banner/(:any)/(:any)'] = 'admin_banner_api/$1/$2';

//admin_order_api
$route['_api/admin/order/(:any)'] = 'admin_order_api/$1';
$route['_api/admin/order/(:any)/(:any)'] = 'admin_order_api/$1/$2';

//admin_voucher_api
$route['_api/admin/voucher/(:any)'] = 'admin_voucher_api/$1';
$route['_api/admin/voucher/(:any)/(:any)'] = 'admin_voucher_api/$1/$2';

//admin_translation_api
$route['_api/admin/translation/(:any)'] = 'admin_translation_api/$1';
$route['_api/admin/translation/(:any)/(:any)'] = 'admin_translation_api/$1/$2';

//admin_notification_api
$route['_api/admin/notification/(:any)'] = 'admin_notification_api/$1';
$route['_api/admin/notification/(:any)/(:any)'] = 'admin_notification_api/$1/$2';

/*Admin*/
$route['_admin/(:any)'] = 'admin/admin/$1';

//User_view
$route['_admin/user/(:any)'] = 'admin/user/$1';
$route['_admin/user/(:any)/(:any)'] = 'admin/user/$1/$2';

//Record_view
$route['_admin/record/(:any)'] = 'admin/record/$1';
$route['_admin/record/(:any)/(:any)'] = 'admin/record/$1/$2';

//Category_view
$route['_admin/category/(:any)'] = 'admin/category/$1';
$route['_admin/category/(:any)/(:any)'] = 'admin/category/$1/$2';

//News_view
$route['_admin/news/(:any)'] = 'admin/news/$1';
$route['_admin/news/(:any)/(:any)'] = 'admin/news/$1/$2';

//Banner_view
$route['_admin/banner/(:any)'] = 'admin/banner/$1';
$route['_admin/banner/(:any)/(:any)'] = 'admin/banner/$1/$2';

//Order_view
$route['_admin/order/(:any)'] = 'admin/order/$1';
$route['_admin/order/(:any)/(:any)'] = 'admin/order/$1/$2';

//Voucher_view
$route['_admin/voucher/(:any)'] = 'admin/voucher/$1';
$route['_admin/voucher/(:any)/(:any)'] = 'admin/voucher/$1/$2';

//Translation_view
$route['_admin/translation/(:any)'] = 'admin/translation/$1';
$route['_admin/translation/(:any)/(:any)'] = 'admin/translation/$1/$2';

//Notification_view
$route['_admin/notification/(:any)'] = 'admin/notification/$1';
$route['_admin/notification/(:any)/(:any)'] = 'admin/notification/$1/$2';


//font end
//home cấu hình link home
$route['(:any)'] = 'home/$1';
$route['(:any)/(:any)'] = 'home/$1/$2';