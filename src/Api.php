<?php
namespace Chenall\PhalApi;

use PhalApi\View\View;


class Api extends \PhalApi\Api
{

    /**
     * @var array $Rules API参数列表,具体的参数规则由配置文件rules指定.
     */
    protected $Rules = array();

    /**
     * 获取参数设置的规则
     *
     * 可由开发人员根据需要重载
     * 
     * @return array
     */
    public function getRules()
    {
        if (empty($this->Rules)) {
            return array();
        }
        $root = DI()->request->getNamespace();
        $allrules = DI()->config->get('rules@' . $root, array());
        if (empty($allrules)) {
            $allrules = DI()->config->get('rules', array());
        }
        if (empty($allrules)) {
            return array();
        }
        $path = new \ReflectionClass($this);
        $root_dir = dirname($path->getFileName());
        $rules = array();
        //通用的参数规则获取方法,并且自动生成相应的类文件(用于自动完成)
        foreach ($this->Rules as $k => $v) {
            $params = is_string($v) ? explode(',', $v) : $v;
            $data = [];
            foreach ($params as $key) {
                $keyp = explode(':', $key);
                $key = $keyp[0];
                $data[$key] = $allrules[$key];
                $data[$key]['name'] = $key;
                if (isset($keyp[1])) {
                    $data[$key]['require'] = $keyp[1];
                }
            }
            $rules[$k] = $data;
            if (!class_exists('\\' . $root . '\\Data\\' . $k)) {
                $line = [];
                foreach ($rules[$k] as $key => $value) {
                    $vType = $value['type'] == 'array' ? $value['name'] : $value['type'];
                    $line[] = "/** @var {$vType} {$value['desc']} */\npublic \${$value['name']};";
                }
                @file_put_contents($root_dir . '/../Data/' . $k . '.php', "<?php\nnamespace ${root}\Data;\nclass $k {\n" . implode("\r\n", $line) . "\n}");
            }
        }
        //公用参数合并到每一个API
        if (isset($rules['common'])) {
            foreach ($rules as $k => &$v) {
                if ($k != 'common') {
                    $v = array_merge($rules['common'], $v);
                }
            }
        }

        return $rules;
    }
}
