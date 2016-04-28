<?php
defined('PASSPORT') or exit('没有访问权限!');
/*----------------------------------------------------------------------------------------------------------------
            加载mongodb操作类
-----------------------------------------------------------------------------------------------------------------*/
lib('mdb');

/**
 * Simple Coder 核心类，定义配置文件读取，定义资源mdb
 * Class Sc
 */
class Sc{
    /**
     * @var object Mdb mongodb操作类对象
     */
    protected $mdb;
    /**
     * @var array $mongodb 配置项列表数组
     */
    protected $mongodb;
    //mongodb用户名
    protected $username;
    //mongodb用户名
    protected $password;
    //mongodb ip
    protected $host;
    //mongodb 端口
    protected $port;
    /**
     * @var object Load load类实例
     */
    protected $load;

    public function __construct(){
        $this->load_init();
        $this->mongo_init();
        //初始化mongodb操作类
        $this->mdb = Mdb::db_init($this->username,$this->password,$this->host,$this->port);
    }

    /**
     * 获取加载类的实例
     */
    private function load_init(){
        $this->load = Load::get_loader();
        if(empty($this->load)){
            __error('加载类加载出错');
        }
    }

    /**
     * 初始化mongodb
     */
    private function mongo_init(){
        $config_items = $this->load->config('mongodb');
        if(empty($config_items) || !isset($config_items['mongodb'])){
            __error('mongodb配置加载不正确');
        }
        $this->set_mongo_config($config_items);
    }

    /**
     * 配置类的各个mongodb配置项
     * @param $config_items
     */
    protected function set_mongo_config($config_items){
        $this->mongodb = $config_items['mongodb'];
        if(isset($config_items['mongodb']['username'])){
            $this->username = $this->mongodb['username'];
        }
        if(isset($config_items['mongodb']['password'])){
            $this->password = $this->mongodb['password'];
        }
        if(isset($config_items['mongodb']['host'])){
            $this->host = $this->mongodb['host'];
        }
        if(isset($config_items['mongodb']['port'])){
            $this->port = $this->mongodb['port'];
        }
    }

    /**
     * 获取mongodb配置
     * @param $config_name
     * @return mixed
     */
    protected function get_mongo_config($config_name){
        if(!is_string($config_name)){
            __error('mongodb配置名称有误');
        }
        if(!isset($this->mongodb[$config_name])){
            __error('该配置项不存在');
        }
        return $this->mongodb[$config_name];
    }

    /**
     * 加载视图
     * @param $page
     */
    protected function display($page){
        $this->load->view($page);
    }

    /**
     * 给视图模板赋值
     * @param $name
     * @param $value
     */
    protected function assign($name,$value){
        $this->load->assign($name,$value);
    }
}
?>