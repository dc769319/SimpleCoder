<?php
    /**
     * mongoDB操作类
     * @author Charles Dong
     *
     */
    class Mdb{
        private $conn;
        private static $db_obj;
        private static $username;
        private static $password;
        private static $host;
        private static $port;
        public function __construct($username,$password,$host,$port){
            self::$username = $username;
            self::$password = $password;
            self::$host = $host;
            self::$port = $port;
            $this->conn = new MongoClient("mongodb://".$username.":".$password."@".$host.":".$port."/admin");
        }

        /**
         * 初始化Db类，返回Db对象
         */
        public static function db_init($username,$password,$host,$port){
            if(!self::$db_obj instanceof self){
                self::$db_obj = new self($username,$password,$host,$port);
            }
            return self::$db_obj;
        }

        public function get_conn(){
            return $this->conn;
        }

        /**
         * 插入一条数据
         * @param string $table_name 表名
         * @param string $data 数据数组
         */
        public function insert($db_name,$table_name,$data,$options=array()){
            return $this->conn->$db_name->$table_name->insert($data,$options);
        }

        /**
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $condition 查询条件
         * @param array $data 待插入的数据
         * @param array $options 附加选项
         * @return bool
         */
        public function update($db_name,$table_name,$condition,$data,$options=array()){
            return $this->conn->$db_name->$table_name->update($condition,$data,$options);
        }
        /**
         * 删除数据
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $condition 查询条件
         * @param array $options 附加选项
         */
        public function delete($db_name,$table_name,$condition,$options=array()){
            return $this->conn->$db_name->$table_name->remove($condition,$options);
        }
        /**
         * 查询满足条件的多条数据
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $condition 查询条件
         * @param array $options 附加选项，例如从第几项开始显示、排序、限制输出几条……
         * @param array $fields 显示的字段名
         * @return array
         */
        public function select_more($db_name,$table_name,$condition,$options=array(),$fields=array()){
            $db_tmp = $this->conn->$db_name->$table_name->find($condition,$fields);
            if(isset($options['start'])){
                $db_tmp->skip($options['start']);
            }
            if(isset($options['limit'])){
                $db_tmp->limit($options['limit']);
            }
            if(isset($options['sort'])){
                $db_tmp->sort($options['sort']);
            }
            $result = array();
            try{
                while($db_tmp->hasNext()){
                    $result[] = $db_tmp->getNext();
                } 
            }
            catch (MongoConnectionException $e){
                return $e->getMessage();
            }
            catch (MongoCursorTimeoutException $e){
                return $e->getMessage();
            }
            return $result;
        }
        /**
         * 查询一条数据
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $condition 查询条件
         * @param array $fields 显示的字段名
         */
        public function select_one($db_name,$table_name,$condition,$fields=array()){
            return $this->conn->$db_name->$table_name->findOne($condition,$fields);
        }
        /**
         * 为表中的某个字段创建索引
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $fields 显示的字段名
         * @param array $options 附加选项
         */
        public function create_index($db_name,$table_name,$fields,$options=array()){
            return $this->conn->$db_name->$table_name->ensureIndex($fields,$options);
        }
        /**
         * 查询出所有的索引
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         */
        public function get_indexes($db_name,$table_name){
           return $this->conn->$db_name->$table_name->getIndexInfo();
        }
        /**
         * 删除一个索引
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param 要删除的key索引，应为数组 $keys
         */
        public function delete_index($db_name,$table_name,$keys){
            return $this->conn->$db_name->$table_name->deleteIndex($keys);
        }

        /**
         * @param string $db_name  数据库名称
         * @param string $table_name  表名
         * @param array $condition 查询条件
         * @param array $options 附加选项
         * @return int
         */
        public function count($db_name,$table_name,$condition,$options=array()){
            $db_tmp = $this->conn->$db_name->$table_name->find($condition);
            if(isset($options['start'])){
                $db_tmp->skip($options['start']);
            }
            if(isset($options['limit'])){
                $db_tmp->limit($options['limit']);
            }
            if(isset($options['sort'])){
                $db_tmp->sort($options['sort']);
            }
            return $db_tmp->count();
        }
        /**
         * 获取当前的连接下的所有数据库
         */
        public function get_dbs(){
            return $this->conn->listDBs();
        }
        /**
         * 删除一张表
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         */
        public function drop_table($db_name,$table_name){
            return $this->conn->$db_name->$table_name->drop();
        }
        /**
         * 删除一个数据库
         * @param string $db_name 表名
         */
        public function drop_db($db_name){
            return $this->conn->$db_name->drop();
        }

        /**
         * 删除一个数据库
         * @param string $db_name 表名
         */
        public function show_tables($db_name){
            return $this->conn->selectDB($db_name)->getCollectionNames();
        }

        /**
         * 创建一张表
         * @param string $db_name 数据库名
         * @param string $table_name 表名
         * @return bool
         */
        public function create_table($db_name,$table_name){
            $res = $this->conn->$db_name->createCollection($table_name);
            if($res){
                return $table_name;
            }else{
                return false;
            }
        }

        /**
         * 查找并更新字段的值
         * @param string $db_name 数据库名称
         * @param string $table_name 表名
         * @param array $query 查询条件
         * @param array $update 想要更新的数据
         * @param array $fields 查询的字段
         * @param array $options 附加选项
         * @return array|bool
         */
        public function find_modify($db_name,$table_name,$query,$update,$fields,$options){
            $res = $this->conn->$db_name->$table_name->findAndModify($query,$update,$fields,$options);
            if($res){
                return $res;
            }else{
                return true;
            }
        }
    }
?>