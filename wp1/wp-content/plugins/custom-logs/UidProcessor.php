<?php
use Monolog\Processor\ProcessorInterface;

class UidProcessor implements ProcessorInterface {
    public function __invoke(array $record) {

        $sessionId = session_id();

        $userIp = $this->getClientIp();

        $record['extra']['request_id'] = $sessionId;
        $record['extra']['user_ip'] = $userIp;

        return $record;
    }

    private function getClientIp() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim(end($ipList));
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }
}
