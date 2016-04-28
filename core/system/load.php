<?php
defined('PASSPORT') or exit('没有访问权限!');

/**
 * 简单的文件加载类
 * Class Loader
 */
class Load{
    //存储自身的实例化对象
    protected static $loader;
    //将要赋值给模板的变量集合
    protected $vars = array();

    /**
     * 得到load类的实例化对象
     * @return Load
     */
    public static function get_loader(){
        if(!self::$loader instanceof self){
            self::$loader = new self();
        }
        return self::$loader;
    }

    /**
     * 加载视图，并赋值
     * @param $page
     */
    public function view($page){
        if(!is_string($page)){
            __error('视图名称不合法');
        }
        if(!file_exists(APP.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$page.'.php')){
            __error($page.'视图文件不存在');
        }
        extract($this->vars,EXTR_OVERWRITE);
        include APP.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$page.'.php';
    }

    /**
     * 加载类库
     * @param string $lib 类库文件名称
     */
    public function lib($lib){
        if(!is_string($lib)){
            __error('类库名称不合法');
        }
        if(file_exists(APP.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.$lib.'.php')){
            return include APP.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.$lib.'.php';
        }elseif (file_exists(CORE.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.$lib.'.php')){
            return include CORE.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.$lib.'.php';
        }else{
            __error($lib.'类库不存在');
            return null;
        }
    }

    /**
     * 加载配置项
     * @param $config
     */
    public function config($config){
        if(!is_string($config)){
            __error('配置文件名称不合法');
        }
        if(file_exists(APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$config.'.php')){
            //返回加载配置文件的结果
            $config_items = include APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$config.'.php';
            //将配置项保存到静态数组中
            config($config_items);
            return $config_items;
        }elseif (file_exists(CORE.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$config.'.php')){
            //返回加载配置文件的结果
            $config_items = include CORE.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$config.'.php';
            //将配置项保存到静态数组中
            config($config_items);
            return $config_items;
        }else{
            __error($config.'配置文件不存在');
            return null;
        }
    }

    /**
     * 加载控制器
     * @param $controller
     */
    public function controller($controller){
        if(!is_string($controller)){
            __error('控制器文件名称不合法');
        }
        if(!file_exists(APP.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$controller.'.php')){
            __error($controller.'控制器文件不存在');
        }
        return include APP.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$controller.'.php';
    }

    /**
     * 加载模型类
     * @param $model
     * @return bool
     * @throws Exception
     */
    public function model($model){
        if(!is_string($model)){
            __error('模型文件名称不合法');
        }
        if(!file_exists(APP.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$model.'.php')){
            __error($model.'模型文件不存在');
        }
        include APP.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$model.'.php';
        $model_class = ucfirst($model);
        if(!class_exists($model_class)){
            __error($model_class.'模型类不存在');
        }
        if(!!$model_object = new $model_class()){
            return $model_object;
        }else{
            return false;
        }
    }

    /**
     * 加载文件夹下所有的php文件
     * @param $dir
     */
    public function dir_file($dir){
        $handle=opendir($dir);
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != ".." && preg_match('/^\w+\.php$/',$file)) {
                if(!file_exists($dir.DIRECTORY_SEPARATOR.$file)){
                    continue;
                }
                include $dir.DIRECTORY_SEPARATOR.$file;
            }
        }
        closedir($handle);
    }

    /**
     * 给模板赋值
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function assign($name,$value){
        if(!is_string($name)){
            __error('赋值给模板的变量名称不合法');
        }
        $this->vars[$name] = $value;
    }

    /**
     * 加载helper文件，helper文件通常是函数库，类似于core/init/functions.php文件
     * 用于用户扩展函数库
     * @param $helper
     * @return mixed
     * @throws Exception
     */
    public function helper($helper){
        if(!is_string($helper)){
            __error('helper文件名称不合法');
        }
        if(!file_exists(APP.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.$helper.'.php')){
            __error($helper.'helper文件不存在');
        }
        return include APP.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR.$helper.'.php';
    }
}
?>