<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Attachment;
use App\Models\Sites;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    private $file_type;
    private $image_type;
    private $config;
    private $storage = 'local';
    private $group_id = 0;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function FileUpload(Request $request)
    {
        $upload_type = $request->input('upload_type','formData');
        if (!extension_loaded('fileinfo')){
            return $this->adminJson([], -1,'请开启 fileinfo 拓展');
        }
        $config = Sites::GetPluginSet('uploadConfig');
        $this->image_type = !empty($config['image_type']) ? explode(',', strtolower($config['image_type'])) : Attachment::$image_type;
        $this->file_type = !empty($config['file_type']) ? explode(',', strtolower($config['file_type'])) : Attachment::$file_type;
        if (!empty($this->config['storage'])) {
            $this->storage = $this->config['storage'];
        }
        $this->group_id = intval($request->input('group_id',0));
        $this->config = $config;
        $data = ["code" => -2, 'message' => '上传错误'];
        switch ($upload_type) {
            case 'formData':
                $data = $this->formData($request);
                break;
            case 'NetworkToLocal':
                $data = $this->Network_picture_extraction($request);
                break;
        }
        return $this->adminJson(empty($data['data'])?[]:$data['data'], $data["code"], $data["message"]);
    }

    /**
     * @param $file_path
     * @param $storage
     * @return array
     */
    public function delFile($file_path, $storage)
    {
        if (empty($file_path)) {
            return ['code' => 11, 'message' => '路径不能为空'];
        }
        $disk = $this->GetDisk($storage);
        if (!$disk->exists($file_path)) {
            return ['code' => 12, 'message' => '文件不存在'];
        }
        try {
            if ($disk->delete($file_path)) {
                return ['code' => 0, 'message' => '删除成功'];
            }
        }catch (\Exception $exception){
            logger()->error('delFile: '.$exception->getMessage().' path: '.$file_path);
        }
        return ['code' => -3, 'message' => '删除失败'];
    }

    /**
     * @param $request
     * @return array
     */
    private function formData($request)
    {
        $file = $request->file('iFile');
        //检查文件是否上传完成`
        if ($file->isValid()){
            return $this->putFiles([
                'ext' => $file->getClientOriginalExtension(),
                'filetype' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
            ], $file);
        }
        return ['code' => 0, 'message' => $file->getErrorMessage()];
    }

    /**
     * @param $request
     * @return array
     */
    private function Network_picture_extraction($request)
    {
        $url = trim($request->input('url'));
        if (empty($url)) {
            return ['code' => 24, 'message' => '文件地址不存在'];
        }
        $url_host = parse_url($url, PHP_URL_HOST);
        if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url_host)) {
            return ['code' => 25, 'message' => '网络链接不支持IP地址!'];
        }
        $client = new Client(['verify' => false]);
        $resp = $client->get($url);
        if ($resp->getStatusCode() != 200) {
            return ['code' => 26, 'message' => '提取文件失败: 未找到该资源文件'];
        }
        $f_name = pathinfo(urldecode($url));
        return $this->putFiles([
            'filetype' => $resp->getHeader('content-type')[0],
            'file_size' => $resp->getHeader('Content-Length')[0],
            'file_name' => trim($f_name['basename']),
        ], $resp->getBody()->getContents(), true);
    }

    /**
     * @param $resData
     * @param $file_content
     * @param false $is_network
     * @return array
     */
    private function putFiles($resData, $file_content, $is_network = false)
    {
        $FT = $this->FilesType($resData["filetype"]);
        $maxSize = $FT["maxSize"];
        $extensions = $FT["extensions"];
        $str = explode('/', $resData['filetype']);
        $resData['ext'] = $is_network ? $str[1] : strtolower($resData['ext']);
        if (!in_array($resData['ext'], $extensions)) {
            return ['code' => 28, 'message' => '仅支持 '.implode(',', $extensions).' 格式'];
        }
        if ($resData['file_size'] > $maxSize*1024*1024) {
            return ['code' => 29, 'message'=> '附件大小限制 '.$maxSize.'M'];
        }
        $disk = $this->GetDisk();
        $newPath = $str[0].'s/'.date('Ymd');
        if ($is_network) {
            $newPath .= '/'.str_random(40).".".$resData['ext'];
        }
        $user = auth()->guard('admin')->user();
        if($path = $disk->put($newPath, $file_content)) {
            $img_path = $is_network ? $newPath : $path;
            $resData = $this->encodeImage($disk, $img_path, $resData);
            $attachment = Attachment::CreateUploadFile([
                "filename" => $resData['file_name'],
                "path" => $img_path,
                "suffix" => $resData['ext'],
                "group_id" => $this->group_id,
                "type" => $resData['filetype'],
                "size" => $resData['file_size'],
                "uuid" => empty($user->uuid)?'':$user->uuid,
                "storage" => $this->storage,
            ]);
            return ['code' => 0, 'message' => '上传成功', 'data' => [
                'file_id' => $attachment['id'],
                'file_url' =>  $disk->url($img_path),
                'file_name' => $resData['file_name'],
                'extension' => $resData['ext'],
                'file_size' => $resData['file_size'],
                'file_path' => $is_network ? $newPath : $path,
                'file_type' => $resData['filetype'],
                'group_id' => $this->group_id,
            ]];
        }
        return ['code' => -2, 'message' => '上传失败'];
    }

    private function FilesType($filetype)
    {
        $str = explode('/', $filetype);
        if ($str[0] != 'image') {
            $file_size = !empty($this->config['file_size']) ? intval($this->config['file_size']) : Attachment::$file_size;
            return ["maxSize" => $file_size/1024, "extensions" => $this->file_type];
        }
        $image_size = !empty($this->config['image_size']) ? intval($this->config['image_size']) : Attachment::$image_size;
        return ["maxSize" => $image_size/1024, "extensions" => $this->image_type];
    }

    private function GetDisk($storage = 'local')
    {
        if ($storage == 'qiniu') {
            return Storage::disk('qiniu');
        }
        return Storage::disk('public');
    }

    private function encodeImage($disk, $img_path, $resData)
    {
        $compress = !empty($this->config['compress']);
        if ($this->storage == 'local' && $compress) {
            $w = !empty($this->config['image_witch'])?$this->config['image_witch']:null;
            $h = !empty($this->config['image_height'])?$this->config['image_height']:null;
            $q = !empty($this->config['image_quality'])?$this->config['image_quality']:null;
            if ($resData['filetype'] == 'image/jpg' || $resData['filetype'] == 'image/jpeg') {
                $imgP = $disk->path($img_path);
                $img = Image::make($imgP);
                if ($w || $h) {
                    $img->resize($w, $h);
                }
                if ($q) {
                    $img->encode($resData['ext'], $q);
                }
                $img->save();
                $resData['file_size'] = $disk->size($img_path);
            }
        }
        return $resData;
    }
}
