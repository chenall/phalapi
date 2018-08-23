<?php
namespace Chenall\PhalApi\View;

class Mustache extends Base
{
    protected function load($name, $options = [])
    {
        $options += array(
            'loader' => new \Mustache_Loader_FilesystemLoader($this->view_root, array('extension' => 'html')),
        );
        $mutache = new \Mustache_Engine($options);
        return $mutache->render($name, $this->data);
    }
}