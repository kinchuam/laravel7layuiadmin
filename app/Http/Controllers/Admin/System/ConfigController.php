<?php

namespace App\Http\Controllers\Admin\System;

use App\Models\Content\Attachment;
use App\Models\Sites;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ConfigController extends Controller
{

    public function index()
    {
        return view('admin.system.config');
    }

    public function data(Request $request)
    {
        $siteKey = trim($request->get('siteKey'));
        $config = Sites::GetPluginSet($siteKey);
        if ($siteKey == 'uploadConfig') {
            $config['image_size'] = $config['image_size'] ?? Attachment::$image_size;
            $config['image_type'] = isset($config['image_type']) ? explode(',', $config['image_type']) : Attachment::$image_type;
            $config['file_size'] = $config['file_size'] ?? Attachment::$file_size;
            $config['file_type'] = isset($config['file_type']) ? explode(',', $config['file_type']) : Attachment::$file_type;
            $config['upload_max_filesize'] = ini_get('upload_max_filesize');
            $config['post_max_size'] = ini_get('post_max_size');
        }else if ($siteKey == 'wechatMiniProgramConfig') {
            if (empty($config['token'])) {
                $config['token'] = Str::random(32);
                $config['aes_key'] = Str::random(43);
            }
        }
        return $this->adminJson($config);
    }

    public function update($siteKey, Request $request)
    {
        $data = $request->except($siteKey == 'uploadConfig' ? ['_token', '_method', 'upload_max_filesize', 'post_max_size'] : ['_token', '_method']);
        if (empty($data)) {
            return $this->adminJson([],1, '无数据更新');
        }
        return Sites::UpdatePluginSet($siteKey, $data)
            ? $this->adminJson([],0,'更新成功')
            : $this->adminJson([], -2, '系统错误');
    }

}
