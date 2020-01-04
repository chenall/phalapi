<?php

namespace Chenall\PhalApi\View;

/**
 * PhalApi简易视图 by chenall 2018-08-23
 */
class Base
{
    /**
     * View 模板文件根目录
     *
     * @var string
     */
    protected $view_root = '';
    /**
     * 当前使用的接口接口方法名
     *
     * @var string
     */
    protected $action = '';
    /**
     * 模板变量数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 模板引擎参数
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options 模板引擎参数
     */
    function __construct($options = [])
    {
        $request = \PhalApi\DI()->request;
        $server = $request->getServiceApi();
        $this->action = $request->getServiceAction();
        $root = $request->getNamespace();
        $root .= '\\Api\\' . $server;
        //通过反射获取当前API对应的视图文件位置
        $path = new \ReflectionClass($root);
        $root = dirname($path->getFileName());
        $this->view_root = $root . '/../View/' . $server . '/';
        $this->options = $options;
    }

    /**
     * 设置模板变量
     *
     * @param mixed $key   变量
     * @param mixed $value 值
     * @return View
     */
    public function setVar($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 设置一组模板变量
     *
     * @param array $params
     * @return View
     */
    public function setVars($params)
    {
        $this->data = array_merge($this->data, $params);
        return $this;
    }

    /**
     * 渲染模板
     * @param string $name 模板名称(默认=当前调用方法)
     * @return void
     */
    public function show($name = null)
    {
        echo $this->load($name ?? $this->action, $this->options);
        exit();
    }

    /**
     * 装载模板
     * @param string @name 模板文件名
     * @param  array  $param 可选参数要传入的值
     * @return string
     */
    protected function load($name, $options = [])
    {
        //将数组键名作为变量名，如果有冲突，则覆盖已有的变量
        extract($this->data, EXTR_OVERWRITE);
        $view = $this->view_root . $name . '.html';
        ob_start();
        ob_implicit_flush(false);
        //检查文件是否存在
        if (file_exists($view)) {
            include_once($view);
        } else {
            echo "<!--${name}模板文件不存在-->";
        }
        return '';
        //获取当前缓冲区内容 
        //$content = ob_get_contents();
        //return $content;
    }
}
