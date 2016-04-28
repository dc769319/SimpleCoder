<?php

/**
 * 模型类
 * Class Model
 * @author Charles Dong
 */
class Model extends Sc
{
    protected static $modelObj;//model基类对象

    public function __construct(){
        parent::__construct();
    }
    /**
     * 获取Model类对象
     * @return Model
     */
    protected function getModel()
    {
        if (!self::$modelObj instanceof self) {
            self::$modelObj = new self();
        }
        return self::$modelObj;
    }
}
?>