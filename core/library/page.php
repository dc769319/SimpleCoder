<?php

/**
 * 分页类
 * 外部使用示例：
 * $page = new Page();
 * 可以使用config用数组形式传入属性值，也可以使用set方法一个一个设置属性值
 * $page->config(array(
        'totalNum' => 100,
        'curPage' => 2,
        'perPage' => 20
 * ));
 * $page->pages();
 * 无刷新分页：
 * $page->staticPage();
 * Class Page
 * @author Charles Dong
 */
class Page{
    private $perPage = 20;//每一页显示的记录数
    private $curPage;//当前页面的页码
    private $totalNum;//记录总数
    private $displayNum = 5;//每页显示的页码链接数量
    private $templateUrl;//生成的url模板
    private $activeClass = 'pageactiv';//当前页码a链接的class名称
    private $staticAClass = 'pageahead';//无刷新专用，页码a链接的class名称
    private $jumpClass = 'jumppage';//页码跳转输入框input的class名称

    private $errorNum;//错误代号
    private $errorMsg = array();

    /**
     * 用于外部设置成员属性
     * @param $key
     * @param $val
     * @return $this
     */
    public function set($key,$val){
        if(array_key_exists($key, get_class_vars(get_class($this)))){
            $this->setOption($key,$val);
        }
        return $this;
    }

    /**
     * 数组形式设置成员属性
     * 接收一个数组作为配置项集合
     * 例如：array(
        'totalNum' => 100,
        'curPage' => 2,
        'perPage' => 20
        )
     * @param $configArr
     * @return $this
     */
    public function config($configArr){
        if(!is_array($configArr) || empty($configArr)){
            $this->setError(0);
            return $this;
        }
        foreach($configArr as $config => $configVal){
            if(array_key_exists($config, get_class_vars(get_class($this)))){
                $this->setOption($config,$configVal);
            }
        }
        return $this;
    }

    /**
     * 为单个成员属性设置值
     * @param $key
     * @param $val
     */
    private function setOption($key,$val){
        $this->$key = $val;
    }

    /**
     * 验证各个传入的成员属性值，设置是否正确
     * @return bool
     */
    private function verifySetting(){
        if(!is_numeric($this->totalNum) || $this->totalNum <= 0){
            $this->setError(-1);
            return false;
        }
        if(!is_numeric($this->perPage) || $this->perPage <= 0){
            $this->setError(1);
            return false;
        }
        if(!is_numeric($this->displayNum) || $this->displayNum <= 0){
            $this->setError(-2);
            return false;
        }
        if(!is_numeric($this->curPage) || $this->curPage <= 0){
            $this->setError(2);
            return false;
        }
        return true;
    }

    /**
     * 计算总页数
     * @return float
     */
    private function calculateTotalPage(){
        return ceil($this->totalNum / $this->perPage);
    }

    /**
     * 设置错误代码，并获取错误提示信息
     * @param $errorCode
     */
    private function setError($errorCode){
        $this->setOption('errorNum',$errorCode);
        $this->getError();
    }

    /**
     * 生成分页字符串
     * @return bool|string
     */
    public function pages(){
        //配置参数验证
        if(!$this->verifySetting()){
            return false;
        }
        $totalPage = $this->calculateTotalPage();
        $displayNum = $this->displayNum;
        $curPage = $this->curPage;
        if(!$totalPage){
            $this->setError(3);
            return false;
        }
        switch ($totalPage >= $displayNum) {
            case FALSE:
                $minPage = 1;
                $maxPage = $totalPage;
                break;
            case TRUE:
                if ($displayNum % 2 == 0) {
                    $minPage = $curPage - ($displayNum / 2 - 1);
                } else {
                    $minPage = $curPage - floor($displayNum / 2);
                }
                if ($minPage < 1) {
                    $minPage = 1;
                }
                if ($minPage > $totalPage - $displayNum + 1) {
                    $minPage = $totalPage - $displayNum + 1;
                }
                $maxPage = $minPage + $displayNum - 1;
                break;
        }
        $templateUrl = $this->templateUrl;
        $pageString = '<ul>';
        $pageString .= '<li><a href="javascript:void(0)">共' . $totalPage . '页</a></li>';

        // 当前页码大于1才显示第一页
        if ($curPage > 1) {
            $pageString .= '<li><a href="' . $templateUrl . '1">第一页</a></li>';
        }
        // 当前页码大于1，才显示'上一页'按钮
        if ($curPage > 1) {
            $pageString .= '<li><a href="' . $templateUrl . ($curPage - 1) . '">上一页</a></li>';
        }
        for ($i = $minPage; $i <= $maxPage; $i++) {
            $pageString .= '<li><a class="';
            // 给当前页添加active属性，以便好把样式区分开来
            if ($curPage == $i) {
                $pageString .= $this->activeClass;
            } else {
                $pageString .= '';
            }
            $pageString .= '" href="' . $templateUrl . $i . '">' . $i . '</a></li>';
        }
        // 当前页码小于最大页码时，显示'下一页'按钮
        if ($curPage < $maxPage) {
            $pageString .= '<li><a href="' . $templateUrl . ($curPage + 1) . '">下一页</a></li>';
        }
        // 当前页小于最大页才显示最后一页
        if ($curPage < $totalPage) {
            $pageString .= '<li><a href="' . $templateUrl . $totalPage . '">最后一页</a></li>';
        }
        $pageString .= '<li>&nbsp;跳转到<input type="text" class="'.$this->jumpClass.'">页</li>';
        $pageString .= '</ul>';
        return $pageString;
    }

    /**
     * 生成分页字符串，用于无刷新分页
     * @return bool|string
     */
    public function staticPage(){
        //配置参数验证
        if(!$this->verifySetting()){
            return false;
        }
        $totalPage = $this->calculateTotalPage();
        $displayNum = $this->displayNum;
        $curPage = $this->curPage;
        if(!$totalPage){
            $this->setError(3);
            return false;
        }
        switch ($totalPage >= $displayNum) {
            case FALSE:
                $minPage = 1;
                $maxPage = $totalPage;
                break;
            case TRUE:
                if ($displayNum % 2 == 0) {
                    $minPage = $curPage - ($displayNum / 2 - 1);
                } else {
                    $minPage = $curPage - floor($displayNum / 2);
                }
                if ($minPage < 1) {
                    $minPage = 1;
                }
                if ($minPage > $totalPage - $displayNum + 1) {
                    $minPage = $totalPage - $displayNum + 1;
                }
                $maxPage = $minPage + $displayNum - 1;
                break;
        }
        $pageString = '<ul>';
        $pageString .= '<li><a href="javascript:void(0)">共' . $totalPage . '页</a></li>';

        // 当前页码大于1才显示第一页
        if ($curPage > 1) {
            $pageString .= '<li><a data-page="1" class="'.$this->staticAClass.'" href="javascript:void(0)">第一页</a></li>';
        }
        // 当前页码大于1，才显示'上一页'按钮
        if ($curPage > 1) {
            $pageString .= '<li><a data-page="' . ($curPage - 1) . '" class="'.$this->staticAClass.'" href="javascript:void(0)">上一页</a></li>';
        }
        for ($i = $minPage; $i <= $maxPage; $i++) {
            $pageString .= '<li><a  data-page="' . $i . '" class="';
            // 给当前页添加active属性，以便好把样式区分开来
            if ($curPage == $i) {
                $pageString .= $this->staticAClass.' '.$this->activeClass;
            } else {
                $pageString .= $this->staticAClass;
            }
            $pageString .= '" href="javascript:void(0)">' . $i . '</a></li>';
        }
        // 当前页码小于最大页码时，显示'下一页'按钮
        if ($curPage < $maxPage) {
            $pageString .= '<li><a data-page="' . ($curPage + 1) . '" class="'.$this->staticAClass.'" href="javascript:void(0)">下一页</a></li>';
        }
        // 当前页小于最大页才显示最后一页
        if ($curPage < $totalPage) {
            $pageString .= '<li><a data-page="' . $totalPage . '" class="'.$this->staticAClass.'" href="javascript:void(0)">最后一页</a></li>';
        }
        $pageString .= '<li>&nbsp;跳转到<input type="text" class="'.$this->jumpClass.'">页</li>';
        $pageString .= '</ul>';
        return $pageString;
    }

    /**
     * 根据错误代号，获取对应的错误信息
     */
    private function getError(){
        $str = '分页出错：';
        switch($this->errorNum){
            case 3:
                $str.='总页码计算有误';
                break;
            case 2:
                $str.='curPage配置不正确，'.$this->curPage.'数值不合法';
                break;
            case 1:
                $str.='perPage配置不正确，'.$this->perPage.'数值不合法';
                break;
            case 0:
                $str.='config配置项必须为一个数组';
                break;
            case -1:
                $str.='totalNum配置不正确，'.$this->totalNum.'数值不合法';
                break;
            case -2:
                $str.='displayNum配置不正确，'.$this->displayNum.'数值不合法';
                break;
            default:
                $str.='未知错误';
        }
        $this->errorMsg = $str;
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getErrorMsg(){
        return $this->errorMsg;
    }
}
?>