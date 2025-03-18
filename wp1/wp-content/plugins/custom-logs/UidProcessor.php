<?php
use Monolog\Processor\ProcessorInterface;

class UidProcessor implements ProcessorInterface {
    public function __invoke(array $record) {
        // 启动或恢复 Session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 获取 Session ID 作为 request_id
        $sessionId = session_id();

        // 获取客户端 IP
        $userIp = $this->getClientIp();

        // 将 request_id 和 user_ip 添加到日志的 extra 字段
        $record['extra']['request_id'] = $sessionId;
        $record['extra']['user_ip'] = $userIp;

        return $record;
    }

    private function getClientIp() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim(end($ipList)); // 取最后一个非空 IP
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }
}
