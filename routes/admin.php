<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| 后台路由
*/

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Admin'], function ($route) {
    //登录、注销
    $route->get('login', 'LoginController@showLoginForm')->name('admin.loginForm');
    $route->get('check_login', 'LoginController@checkLogin');
    $route->post('login', 'LoginController@login')->name('admin.login');
    $route->get('logout', 'LoginController@logout');
    //系统
    $route->group(['middleware' => 'auth:admin'], function ($route) {
        $route->get('/', 'IndexController@layout')->name('admin.layout');
        $route->get('data', 'IndexController@data');
        $route->get('index', 'IndexController@index');
        $route->get('navigation', 'IndexController@Navigation');
        $route->get('webSite', 'IndexController@WebSite');
        $route->get('count','StatisticsController@GetCount');
        $route->get('line_chart','StatisticsController@LineChart');
        //上传
        $route->post('fileUpload', 'UploadController@FileUpload');
        //管理员设置
        $route->get('basic', 'BasicController@index');
        $route->post('basic', 'BasicController@UpdateInfo');
        $route->get('basicPassword', 'BasicController@password');
        $route->post('basicPassword', 'BasicController@UpdatePassword');
        //内容管理
        $route->group(['namespace' => 'Content', 'prefix' => 'content', 'middleware' => 'permission:content.manage'], function ($route) {
            //幻灯片
            $route->group(['middleware' => 'permission:content.kv'], function ($route) {
                $route->get('kv', 'AdvController@index');
                $route->get('kv/data', 'AdvController@data');
                $route->get('kv/create', 'AdvController@create')->middleware('permission:content.kv.create');
                $route->post('kv/store', 'AdvController@store')->middleware('permission:content.kv.create');
                $route->get('kv/{id}/edit', 'AdvController@edit')->middleware('permission:content.kv.edit')->where(['id' => '[0-9]+']);
                $route->put('kv/{id}/update', 'AdvController@update')->middleware('permission:content.kv.edit')->where(['id' => '[0-9]+']);
                $route->delete('kv/destroy', 'AdvController@destroy')->middleware('permission:content.kv.destroy');
            });
            //文章
            $route->group(['middleware' => 'permission:content.articles'], function ($route) {
                $route->get('articles', 'IndexController@index');
                $route->get('articles/data', 'IndexController@data');
                $route->get('articles/category', 'IndexController@GetCategory');
                $route->get('articles/create', 'IndexController@create')->middleware('permission:content.articles.create');
                $route->post('articles/store', 'IndexController@store')->middleware('permission:content.articles.create');
                $route->get('articles/{id}/edit', 'IndexController@edit')->middleware('permission:content.articles.edit');
                $route->put('articles/{id}/update', 'IndexController@update')->middleware('permission:content.articles.edit');
                $route->delete('articles/destroy', 'IndexController@destroy')->middleware('permission:content.articles.destroy');
            });
            //文章分类
            $route->group(['middleware' => 'permission:content.article_category'], function ($route) {
                $route->get('article_category', 'CategoryController@index');
                $route->get('article_category/data', 'CategoryController@data');
                $route->get('article_category/create', 'CategoryController@create')->middleware('permission:content.article_category.create');
                $route->post('article_category/store', 'CategoryController@store')->middleware('permission:content.article_category.create');
                $route->get('article_category/{id}/edit','CategoryController@edit')->middleware('permission:content.article_category.edit')->where('id', '[0-9]+');
                $route->put('article_category/{id}/update','CategoryController@update')->middleware('permission:content.article_category.edit')->where('id', '[0-9]+');
                $route->delete('article_category/destroy','CategoryController@destroy')->middleware('permission:content.article_category.destroy');
            });
            //附件管理
            $route->group(['middleware' => ['permission:content.files']], function ($route) {
                $route->get('files', 'FilesController@index');
                $route->get('files/data', 'FilesController@data');
                $route->get('files/download', 'FilesController@download');
                $route->get('files/create', 'FilesController@create')->middleware('permission:content.files.create');
                $route->get('files/recycle', 'FilesController@recycle')->middleware('permission:content.files.recycle');
                $route->post('files/recover', 'FilesController@recover')->middleware('permission:content.files.recover');
                $route->delete('files/expurgate', 'FilesController@expurgate')->middleware('permission:content.files.expurgate');
                $route->delete('files/destroy', 'FilesController@destroy')->middleware('permission:content.files.destroy');
            });
            //附件分组管理
            $route->group(['middleware'=>['permission:content.files_group']], function ($route) {
                $route->get('files_group', 'FilesGroupController@index');
                $route->get('files_group/data', 'FilesGroupController@data');
                $route->post('files_group/moveFiles', 'FilesGroupController@moveFiles');
                $route->get('files_group/create', 'FilesGroupController@create')->middleware('permission:content.files_group.create');
                $route->post('files_group/store', 'FilesGroupController@store')->middleware('permission:content.files_group.create');
                $route->get('files_group/edit', 'FilesGroupController@edit')->middleware('permission:content.files_group.edit')->where(['id' => '[0-9]+']);
                $route->put('files_group/update','FilesGroupController@update')->middleware('permission:content.files_group.edit')->where(['id' => '[0-9]+']);
                $route->delete('files_group/destroy', 'FilesGroupController@destroy')->middleware('permission:content.files_group.destroy');
            });
        });
        //系统管理
        $route->group(['namespace' => 'System', 'prefix' => 'system', 'middleware' => 'permission:system.manage'], function ($route) {
            //账号管理
            $route->group(['middleware' => 'permission:system.user'], function ($route) {
                $route->get('user','UserController@index');
                $route->get('user/data','UserController@data');
                $route->get('user/create','UserController@create')->middleware('permission:system.user.create');
                $route->post('user/store','UserController@store')->middleware('permission:system.user.create');
                $route->get('user/{id}/edit','UserController@edit')->middleware('permission:system.user.edit')->where(['id' => '[0-9]+']);
                $route->put('user/{id}/update','UserController@update')->middleware('permission:system.user.edit')->where(['id' => '[0-9]+']);
                $route->get('user/password','UserController@password')->middleware('permission:system.user.edit');
                $route->put('user/{id}/updatePassword','UserController@updatePassword')->middleware('permission:system.user.edit')->where(['id' => '[0-9]+']);
                $route->delete('user/destroy','UserController@destroy')->middleware('permission:system.user.destroy');
                $route->get('user/{id}/role','UserController@role')->middleware('permission:system.user.role')->where(['id' => '[0-9]+']);
                $route->put('user/{id}/role','UserController@assignRole')->middleware('permission:system.user.role')->where(['id' => '[0-9]+']);
                $route->get('user/{id}/permission','UserController@permission')->middleware('permission:system.user.permission')->where(['id' => '[0-9]+']);
                $route->put('user/{id}/permission','UserController@assignPermission')->middleware('permission:system.user.permission')->where(['id' => '[0-9]+']);
            });
            //角色管理
            $route->group(['middleware' => 'permission:system.role'], function ($route) {
                $route->get('role','RoleController@index');
                $route->get('role/data','RoleController@data');
                $route->get('role/create','RoleController@create')->middleware('permission:system.role.create');
                $route->post('role/store','RoleController@store')->middleware('permission:system.role.create');
                $route->get('role/{id}/edit','RoleController@edit')->middleware('permission:system.role.edit')->where(['id' => '[0-9]+']);
                $route->put('role/{id}/update','RoleController@update')->middleware('permission:system.role.edit')->where(['id' => '[0-9]+']);
                $route->delete('role/destroy','RoleController@destroy')->middleware('permission:system.role.destroy');
                $route->get('role/{id}/permission','RoleController@permission')->middleware('permission:system.role.permission')->where(['id' => '[0-9]+']);
                $route->put('role/{id}/permission','RoleController@assignPermission')->middleware('permission:system.role.permission')->where(['id' => '[0-9]+']);
            });
            //权限管理
            $route->group(['middleware' => 'permission:system.permission'], function ($route) {
                $route->get('permission', 'PermissionController@index');
                $route->get('permission/data', 'PermissionController@data');
                $route->get('permission/create', 'PermissionController@create')->middleware('permission:system.permission.create');
                $route->post('permission/store', 'PermissionController@store')->middleware('permission:system.permission.create');
                $route->get('permission/{id}/edit', 'PermissionController@edit')->middleware('permission:system.permission.edit')->where(['id' => '[0-9]+']);
                $route->put('permission/{id}/update', 'PermissionController@update')->middleware('permission:system.permission.edit')->where(['id' => '[0-9]+']);
                $route->delete('permission/destroy', 'PermissionController@destroy')->middleware('permission:system.permission.destroy');
            });
            //系统设置
            $route->group(['middleware' => 'permission:system.config'], function ($route) {
                $route->get('config', 'ConfigController@index');
                $route->get('config/data', 'ConfigController@data');
                $route->put('config/{siteKey}/update', 'ConfigController@update')->middleware('permission:system.config.update');
            });
            //系统缓存
            $route->group(['middleware' => 'permission:system.cache'], function ($route) {
                $route->get('cache', 'CacheController@index');
                $route->put('cache/clear', 'CacheController@clearCache')->middleware('permission:system.cache.clear');
            });
        });
        //日志管理
        $route->group(['namespace' => 'Logs', 'prefix' => 'logs', 'middleware' => 'permission:logs.manage'], function ($route) {
            //访问日志
            $route->group(['middleware' => 'permission:logs.access'], function ($route) {
                $route->get('access', 'AccessLogController@index');
                $route->get('access/data', 'AccessLogController@data');
                $route->get('access/{id}/show', 'AccessLogController@show')->middleware('permission:logs.access.show')->where(['id' => '[0-9]+']);
            });
            //操作日志
            $route->group(['middleware' => 'permission:logs.operation'], function ($route) {
                $route->get('operation', 'OperationController@index');
                $route->get('operation/data', 'OperationController@data');
                $route->get('operation/{id}/show', 'OperationController@show')->middleware('permission:logs.operation.show')->where(['id' => '[0-9]+']);
            });
            //登录记录
            $route->group(['middleware' => 'permission:logs.login'], function ($route) {
                $route->get('login', 'LoginLogController@index');
                $route->get('login/data', 'LoginLogController@data');
            });
            //错误日志
            $route->group(['middleware' => 'permission:logs.system'],function ($route) {
                $route->get('system', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
            });
        });
        //运维管理
        $route->group(['namespace' => 'Maintain', 'prefix' => 'maintain', 'middleware' => 'permission:maintain.manage'], function ($route) {
            $route->get('optimize', 'IndexController@optimize')->middleware('permission:maintain.optimize');
            //数据库管理
            $route->group(['middleware' => 'permission:maintain.database'], function ($route) {
                $route->get('database', 'DatabaseController@index');
                $route->get('database/data', 'DatabaseController@data');
                $route->post('database/optimize', 'DatabaseController@optimize')->middleware('permission:maintain.database.optimize');
                $route->post('database/repair', 'DatabaseController@repair')->middleware('permission:maintain.database.repair');
                $route->delete('database/destroy', 'DatabaseController@destroy')->middleware('permission:maintain.database.destroy');
                $route->post('database/clear', 'DatabaseController@clear')->middleware('permission:maintain.database.clear');
            });
        });
    });

});

