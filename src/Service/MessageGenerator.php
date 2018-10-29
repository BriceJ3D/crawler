<?php
namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log(string $string)
    {
    	var_dump($string);
    	$this->logger->info('test');
    }
}