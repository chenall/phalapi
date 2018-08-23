# PhalApi 自用功能扩展 by chenall

## 安装方法

```shell
composer require chenall/phalapi
#如果需要使用mustache视图需继续安装mustache模块
composer require mustache/mustache
```

## 请求参数规则扩展(直接继承了`PhalApi\Api`类)

1. 在config目录中增加`rules.php`配置文件(所有需要的参数规则).

    ```php
    return array(
        //所有参数规则列表,require参数在调用时可以额外指定.
        'param1' => array('type' => 'string', 'require' => 1, 'max' => 64, 'desc' => '参数1的说明'),
        'param2' => array('type' => 'int', 'require' => 0, 'default' => '', 'desc' => '参数2的说明'),
        'param3' => array('type' => 'array','require' => 0, 'desc' => '参数3的说明'),
    )
    ```

    注: 如果需要配置多APP,可以使用`rules@appname.php` 作为配置文件.

2. 在`api`中不再需要重写`getRules`函数了,直接使用`$Rules`指定需要的参数列表即可.

    ```php
    namespace App\Api;
    class Site extends \Chenall\PhalApi\Api
    {
        protected $Rules = array(
            //site下所有API都需要的参数
            'common' => 'param1,param2',
            //特定API的参数,参数后面可以使用:x 或指定是否必填,如下指定在index的API中params3是必填的.
            'index' => 'param3:1',
        }

        public function index(){
        $domain = new \App\Domain\Site();
        return $domain->index($this);
        }
    );
    ```

3. 编辑器友好的例子(自动完成)

    ```php
    //App\Domain\Site.php

    namespace App\Domain;
    class Site {
        /**
        * @param \App\Data\index $param
        */
        function index($param){
            //通过前面的注释告诉编器,这个param参数的类型.现在通过$param->来查看传过来的参数了.
        }
    }
    ```

## 视图扩展

### 介绍

简易的视图扩展支持直接使用PHP语法模板(`Base`)和[Mustache](http://mustache.github.io/)两种模板.

### 使用方法

模板目录: {`API_ROOT`}\View\{`Service`}\{`Action`}.html.

例子: 在App\Site 的View方法中使用视图则对应的模板文件是.

`src\App\View\Site\Action.html`


在需要显示视图的方法中直接开启视图扩展功能

```php
$view = new \Chenall\PhalApi\View\Base();
//$view = new \Chenall\PhalApi\View\Mustache();
$view->setVars(...);//需要传入模板的变量.
$view->show();
```