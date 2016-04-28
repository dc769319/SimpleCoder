<?php
defined('PASSPORT') or exit('没有访问权限!');
if(!function_exists('filtrate')){
    /**
     * 字符串简单过滤
     * 可过滤字符串以及一维数组的各个字符串类型元素
     * @param string $str 用户输入的字符串
     * @return string|array 处理过后的字符串
     */
    function filtrate($handle)
    {
        if (is_array($handle)) {
            foreach ($handle as $key => $obj) {
                if (!is_string($obj)) {
                    continue;
                }
                $handle[$key] = htmlspecialchars(addslashes(trim($obj)));
            }
            return $handle;
        }
        return htmlspecialchars(addslashes(trim($handle)));
    }
}
if(!function_exists('sc_filter')) {
    /**
     * 过滤输入字符串，过滤html、sql查询语句
     * 支持字符串、一维数组各个字符串类型的元素过滤
     * @param $handle
     * @return array|string
     */
    function sc_filter($handle)
    {
        if (is_array($handle)) {
            foreach ($handle as $key => $obj) {
                if (!is_string($obj)) {
                    continue;
                }
                $handle[$key] = addslashes(strip_tags(trim($obj)));
            }
            return $handle;
        }
        return addslashes(filter_sql(strip_tags(trim($handle))));
    }
}
if(!function_exists('filter_sql')) {
    /**
     * 过滤sql语句
     * @param $str
     * @return mixed
     */
    function filter_sql($str)
    {
        if (!is_string($str)) {
            return $str;
        }
        $str = str_replace("and", "&#97;nd", $str);
        $str = str_replace("execute", "&#101;xecute", $str);
        $str = str_replace("update", "&#117;pdate", $str);
        $str = str_replace("count", "&#99;ount", $str);
        $str = str_replace("chr", "&#99;hr", $str);
        $str = str_replace("mid", "&#109;id", $str);
        $str = str_replace("master", "&#109;aster", $str);
        $str = str_replace("truncate", "&#116;runcate", $str);
        $str = str_replace("char", "&#99;har", $str);
        $str = str_replace("declare", "&#100;eclare", $str);
        $str = str_replace("select", "&#115;elect", $str);
        $str = str_replace("create", "&#99;reate", $str);
        $str = str_replace("delete", "&#100;elete", $str);
        $str = str_replace("insert", "&#105;nsert", $str);
        $str = str_replace("'", "&#39;", $str);
        $str = str_replace('"', "&#34;", $str);
        return $str;
    }
}
if(!function_exists('page_ahead')) {
    /**
     * 页面跳转
     * 跳转到指定页面、前一页、首页
     * @param string $message 提示语
     * @param string $url 跳转路径
     */
    function page_ahead($message, $url = FALSE)
    {
        if ($url) {
            $jump = $url;
        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $jump = $_SERVER['HTTP_REFERER'];
        } else {
            $jump = 'index.php';
        }

        echo '<script>alert("' . $message . '");location="' . $jump . '"</script>';
        exit;
    }
}
if(!function_exists('del_space')) {
    /**
     * 删除字符串中的空格
     * @param $str
     * @return mixed
     */
    function del_space($str)
    {
        return str_replace(array(PHP_EOL, ' '), array('', ''), $str);
    }
}
if(!function_exists('deldir')) {
    /**
     * 删除文件夹及文件夹下所有子文件夹、文件
     *
     */
    function deldir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while (($file = readdir($dh))) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}
if(!function_exists('img_name')) {
    /**
     * 根据图片url，获得图片名称
     * @param $url
     * @return string
     */
    function img_name($url)
    {
        return substr(strrchr($url, '/'), 1);
    }
}
if(!function_exists('encrypt_cookie')) {
    /**
     * cookie加密
     * @param $string
     * @return string
     */
    function encrypt_cookie($string)
    {
        $tmp = urlencode($string) . '%E9';
        return base64_encode('K5l' . $tmp);
    }
}
if(!function_exists('decode_cookie')) {
    /**
     * cookie解密
     * @param $string
     * @return string
     */
    function decode_cookie($string)
    {
        $tmp_f = base64_decode($string);
        $tmp_s = substr(substr($tmp_f, 3), 0, -3);
        return urldecode($tmp_s);
    }
}
if(!function_exists('encrypt')) {
    /**
     * 简单的加密函数
     * @param string $password 要加密的字符
     * @return string
     */
    function encrypt($password)
    {
        $tmp = strrev(base64_encode($password)) . 'rwt';
        return md5($tmp);
    }
}
if(!function_exists('get_ip')) {
    /**
     * 获取客户端ip地址
     * @return mixed|string
     */
    function get_ip()
    {
        $unknown = 'unknown';
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }
}
if(!function_exists('dir_file')) {
    /**
     * 批量引入文件
     * @param $dir
     */
    function dir_file($dir)
    {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && preg_match('/^\w+\.php$/', $file)) {
                if (!file_exists($dir . DIRECTORY_SEPARATOR . $file)) {
                    continue;
                }
                include $dir . DIRECTORY_SEPARATOR . $file;
            }
        }
        closedir($handle);
    }
}
if(!function_exists('controller')) {
    /**
     * 加载控制器
     * @param $controller
     */
    function controller($controller)
    {
        if (!is_string($controller)) {
            __error('控制器名称不合法');
        }
        if (!file_exists(APP . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $controller . '.php')) {
            __error($controller . '控制器文件不存在');
        }
        return include APP . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $controller . '.php';
    }
}
if(!function_exists('config')) {
    /**
     * 读取、加载配置
     * @param null $name
     * @param null $value
     * @return array|null
     */
    function config($name = null, $value = null)
    {
        //配置信息保存在静态变量中
        static $config = array();
        if (empty($name)) {
            return $config;
        }
        if (is_string($name)) {
            if (!is_null($value)) {
                $config[$name] = $value;
            } else {
                return isset($config[$name]) ? $config[$name] : null;
            }
        }
        if (is_array($name)) {
            $config = array_merge($config, $name);
        }
        return null;
    }
}
if(!function_exists('lib')) {
    /**
     * 加载类库函数
     * 自动寻找APP、CORE目录下library文件夹内的类库文件
     * APP目录下类库加载的优先级更高
     * @param $lib
     * @return mixed|null
     * @throws Exception
     */
    function lib($lib)
    {
        if (!is_string($lib)) {
            __error('类库名称不合法');
        }
        if (file_exists(APP . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $lib . '.php')) {
            return require_once APP . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $lib . '.php';
        } elseif (file_exists(CORE . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $lib . '.php')) {
            return require_once CORE . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $lib . '.php';
        } else {
            __error($lib . '类库不存在');
            return null;
        }
    }
}
if(!function_exists('view')) {
    /**
     * 加载视图函数
     * @param $page
     * @throws Exception
     */
    function view($page)
    {
        if (!is_string($page)) {
            __error('视图名称不合法');
        }
        if (!file_exists(APP . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $page . '.php')) {
            __error($page . '视图文件不存在');
        }
        include APP . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $page . '.php';
    }
}
if(!function_exists('__error')) {
    /**
     * 显示错误并中断执行，防止用户操作失误，不直接抛出错误
     * @param string $message
     */
    function __error($message)
    {
        echo '<h2>发生错误：</h2><p>错误信息：' . $message . '</p>';
        exit;
    }
}
if(!function_exists('settings')) {
    /**
     * 加载配置文件，寻找APP/CORE的config目录下的settings.php文件
     * APP目录下配置加载优先级更高
     * @return bool
     */
    function settings()
    {
        if (file_exists(APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php')) {
            config(include APP . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php');
            return true;
        } elseif (file_exists(CORE . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php')) {
            config(include CORE . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.php');
            return true;
        } else {
            return false;
        }
    }
}
if(!function_exists('static_domain')) {
    /**
     * 返回配置文件中的静态站点域名设置项
     * @return array|null
     */
    function static_domain()
    {
        $set_res = settings();
        if (!$set_res) {
            return null;
        }
        return config('static');
    }
}
if(!function_exists('domain_url')) {
    /**
     * 返回配置文件中站点域名设置项
     * @return array|null
     */
    function domain_url()
    {
        $set_res = settings();
        if (!$set_res) {
            return null;
        }
        return config('domain');
    }
}
if(!function_exists('mongo_filter')) {
    /**
     * mongodb查询语句特殊字符过滤
     * @param string|array $handel
     * @return mixed
     */
    function mongo_filter($handel)
    {
        if (is_array($handel)) {
            array_map('replace_mongo_sign', $handel);
            return $handel;
        } else {
            return replace_mongo_sign($handel);
        }
    }
}
if(!function_exists('replace_mongo_sign')) {
    /**
     * 过滤mongodb特殊字符
     * @param string $string
     * @return mixed
     */
    function replace_mongo_sign($string)
    {
        if (!is_string($string)) {
            return $string;
        }
        $string = str_replace('{', '', trim($string));
        $string = str_replace('}', '', $string);
        $string = str_replace('$ne', '', $string);
        $string = str_replace('$gte', '', $string);
        $string = str_replace('$gt', '', $string);
        $string = str_replace('$lt', '', $string);
        $string = str_replace('$lte', '', $string);
        $string = str_replace('$in', '', $string);
        $string = str_replace('$nin', '', $string);
        $string = str_replace('$where', '', $string);
        $string = str_replace('$exists', '', $string);
        $string = str_replace('tojson', '', $string);
        $string = str_replace('==', '\==', $string);
        $string = str_replace('db.', 'db\.', $string);
        return strip_tags($string);
    }
}
?>

