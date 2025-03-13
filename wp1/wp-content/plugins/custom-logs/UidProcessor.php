<?php
use Monolog\Processor\ProcessorInterface;

class UidProcessor implements ProcessorInterface {
    public function __invoke(array $record) {
        session_start();

        // 获取 Session ID
        $sessionId = session_id();
        // 生成一个唯一的请求 ID，使用 uniqid()
        $record['extra']['request_id'] = $sessionId;

        return $record;
    }
}
