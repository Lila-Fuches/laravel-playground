<?php

namespace Support;

use UAParser\Parser;
use UAParser\Result\Client;
use UAParser\Result\Device;
use UAParser\Result\UserAgent;
use UAParser\Result\OperatingSystem;

class UserAgentParser
{
    public Client $parser;

    public Device $device;

    public UserAgent $userAgent;

    public OperatingSystem $operatingSystem;

    public string $originalUserAgent;

    public function __construct(string $userAgent = null)
    {
        if (! $userAgent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->parser = Parser::create()->parse($userAgent);

        $this->userAgent = $this->parser->ua;

        $this->operatingSystem = $this->parser->os;

        $this->device = $this->parser->device;

        $this->originalUserAgent = $this->parser->originalUserAgent;
    }
}
