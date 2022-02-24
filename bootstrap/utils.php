<?php

if (!function_exists('')) {
    /**
     * @param $value
     * @param string $char
     * @return string|string[]|null
     */
    function escapeLike($value, $char = '\\')
    {
        if (!empty($value)) {
            return str_replace(
                [$char, '%', '_'],
                [$char.$char, $char.'%', $char.'_'],
                trim($value)
            );
        }
        return null;
    }
}

if (!function_exists('codeToString')) {
    /**
     * @param $str
     * @return string|string[]
     */
    function codeToString($str) {
        $a = [" " , "　" , "\t" , "\n" , "\r"];
        $b = ['&nbsp;' , '&nbsp;&nbsp;' , '&nbsp;&nbsp;&nbsp;&nbsp;' , "\n" , "\r"];
        return str_replace($a, $b, $str);
    }
}

if (!function_exists('htmlText')) {
    /**
     * @param $content
     * @param int $leg
     * @return string
     */
    function htmlText($content, $leg = 150) {
        $subject = strip_tags($content);
        $pattern = '/\n/';
        $content = preg_replace($pattern, '', $subject);
        return mb_substr($content, 0, $leg, "UTF-8");
    }
}

if (!function_exists('byteCount')) {
    /**
     * 格式化字节
     * @param $bit
     * @param string $delimiter
     * @return string
     */
    function byteCount($bit, $delimiter = '') {
        $type = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bit >= 1024 && $i < 5; $i++) $bit /= 1024;
        return round($bit, 2) . $delimiter . $type[$i];
    }
}

if (!function_exists('GetRandom')) {
    /**
     * 产生随机字符串
     * @param $length
     * @param string $chars
     * @return string
     */
    function GetRandom($length, $chars = '0123456789') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}

if (!function_exists('xmlToArray')) {
    /**
     * 将XML转为array
     * @param $xml
     * @return mixed
     */
    function xmlToArray($xml) {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}

if (!function_exists('GetClientIp')) {
    /**
     * 获取真实ip
     * @return mixed|string
     */
    function GetClientIp() {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $arr[0];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('sexists')) {
    /**
     * 查找字符串开头
     * @param $string
     * @param $find
     * @return bool
     */
    function sexists($string, $find) {
        return !(strpos($string, $find) === FALSE);
    }
}

if (!function_exists('price_format')) {
    /**
     * 价格格式化
     * @param $price
     * @return mixed|string
     */
    function price_format($price) {
        if (empty($price)) { return ''; }
        $prices = explode('.', $price);
        if (intval($prices[1]) <= 0) {
            return $prices[0];
        }
        if (isset($prices[1][1]) && $prices[1][1] <= 0) {
            $price = $prices[0] . '.' . $prices[1][0];
        }
        return $price;
    }
}

if (!function_exists('is_array2')) {
    /**
     * is_array升级版
     * @param $array
     * @return bool
     */
    function is_array2($array) {
        if (is_array($array)) {
            foreach ($array as $v) {
                return is_array($v);
            }
            return false;
        }
        return false;
    }
}

if (!function_exists('set_medias')) {
    /**
     * 补充图片连接 升级版
     * @param array $list
     * @param null $fields
     * @return array
     */
    function set_medias($list = [], $fields = null) {
        if (empty($list)) { return $list; }
        if (empty($fields)) {
            foreach ($list as &$row) {
                $row = ToMedia($row);
            }
            return $list;
        }
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        if (is_array2($list)) {
            foreach ($list as &$value) {
                foreach ($fields as $field) {
                    if (isset($list[$field])) {
                        $list[$field] = ToMedia($list[$field]);
                    }
                    if (is_array($value) && isset($value[$field])) {
                        $value[$field] = ToMedia($value[$field]);
                    }
                }
            }
            return $list;
        }
        foreach ($fields as $field) {
            if (isset($list[$field])) {
                $list[$field] = ToMedia($list[$field]);
            }
        }
        return $list;
    }
}

if (!function_exists('ToMedia')) {
    /**
     * 补充图片连接
     * @param $src
     * @param string $storage
     * @return string
     */
    function ToMedia($src, $storage = 'local') {
        $src = trim($src);
        if (empty($src)) { return $src; }
        if (substr($src, 0, 2) == '//') {
            return 'http:' . $src;
        }
        if ((substr($src, 0, 7) == 'http://') || (substr($src, 0, 8) == 'https://')) {
            return $src;
        }
        if ($storage != 'local') {
            $storage = config('custom.upload.storage');
        }
        $is_cache = config('custom.file.is_cache');
        $is_https = config('custom.file.is_https');
        switch ($storage) {
            case 'qiniu':
                $url = $src;
                break;
            default:
                $app_url = config('app.url');
                if ($is_https && substr($app_url, 0, 7) == 'http://') {
                    $app_url = 'https://'.substr($app_url, 0, 7);
                }
                $url = $app_url.'/storage/'.$src;
                break;
        }
        if ($is_cache) {
            $url .= "?v=" . time();
        }
        return $url;
    }
}

if (!function_exists('format_date')) {
    /**
     * 格式化时间
     * @param $time
     * @return mixed|string
     */
    function format_date($time) {
        $is_date = strtotime($time) ? strtotime($time) : false;
        if (!$is_date) { return $time; }
        $today = time();
        $difference = $today - $time;
        $msg = $time;
        switch ($difference) {
            case $difference <= 60 :
                $msg = '刚刚';
                break;
            case $difference > 60 && $difference <= 3600 :
                $msg = floor($difference / 60) . '分钟前';
                break;
            case $difference > 3600 && $difference <= 86400 :
                $msg = floor($difference / 3600) . '小时前';
                break;
            case $difference > 86400 && $difference <= 2592000 :
                $msg = floor($difference / 86400) . '天前';
                break;
            case $difference > 2592000 &&  $difference <= 7776000:
                $msg = floor($difference / 2592000) . '个月前';
                break;
            case $difference > 7776000:
                $msg = '很久以前';
                break;
        }
        return $msg;
    }
}

if (!function_exists('listToTree')) {
    /**
     * 将数据集转换成树结构
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0) {
        $tree = [];
        if(is_array($list)) {
            $refer = [];
            foreach ($list as $key => $val) {
                $refer[$val[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $val) {
                $parentId =  $val[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}

if (!function_exists('treeToList')) {
    /**
     * 将树还原成列表
     * @param $tree
     * @param string $child
     * @param string $order
     * @param array $list
     * @return array|false|mixed
     */
    function treeToList($tree, $child = '_child', $order = 'id', &$list = []){
        if(is_array($tree)) {
            foreach ($tree as $key => $value) {
                $refer = $value;
                if(isset($refer[$child])){
                    unset($refer[$child]);
                    treeToList($value[$child], $child, $order, $list);
                }
                $list[] = $refer;
            }
            $list = listSortBy($list, $order);
        }
        return $list;
    }
}

if (!function_exists('listSortBy')) {
    /**
     * 对集合进行排序
     * @param $list
     * @param $field
     * @param string $sort
     * @return array|false
     */
    function listSortBy($list, $field, $sort = 'asc') {
        if(is_array($list)){
            $refer = $resultSet = [];
            foreach ($list as $i => $data)
                $refer[$i] = &$data[$field];
            switch ($sort) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc':// 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ( $refer as $key=> $val)
                $resultSet[] = &$list[$key];
            return $resultSet;
        }
        return false;
    }
}