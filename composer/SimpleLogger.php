<?php

require_once 'vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class SimpleLogger implements LoggerInterface {

    use LoggerTrait;

    private string $file;
    public function __construct($file) {
        $this->file = $file;
    }
    public function log($level, $message, array $context = []): void
    {
        $entry = "[".strtoupper($level)."] " . $message;
        if (!empty($context)) {
            $entry .= " " . json_encode($context);
        }
        $entry .= PHP_EOL;

        file_put_contents($this->file, $entry, FILE_APPEND);
    }
}