<?php
namespace Chenall\PhalApi;

class SeasLog extends \PhalApi\Logger {
    private $seaslog = null;

    public function __construct($logFolder, $level)
    {
        $this->seaslog = new \SeasLog();
        $this->seaslog->setBasePath($logFolder);
        parent::__construct($level);
    }
    /**
     * 日记纪录
     *
     * 可根据不同需要，将日记写入不同的媒介
     *
     * @param string $type 日记类型，如：info/debug/error, etc
     * @param string $msg 日记关键描述
     * @param string/array $data 场景上下文信息
     * @return NULL
     */
    public function log($type, $msg, $data)
    {
        $levelFunction = strtolower($type);
        $message = '';
        if ($data !== NULL) {
            $isGreaterThan540 = version_compare(PHP_VERSION, '5.4.0' , '>=');
            $message = is_array($data) 
                ? ($isGreaterThan540 ? json_encode($data, JSON_UNESCAPED_UNICODE) : json_encode($data))
                : $data;
        }
        $this->seaslog->$levelFunction($message,[]);
    }
}