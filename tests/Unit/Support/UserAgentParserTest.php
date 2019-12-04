<?php

namespace Tests\Unit\Support;

use Tests\TestCase;
use UAParser\Result\Device;
use UAParser\Result\Client;
use Support\UserAgentParser;
use UAParser\Result\UserAgent;
use UAParser\Result\OperatingSystem;

class UserAgentParserTest extends TestCase
{
    private UserAgentParser $parser;

    private string $userAgentString = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36";

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new UserAgentParser(
            $this->userAgentString
        );
    }

    public function test_it_can_set_parser()
    {
        $this->assertInstanceOf(Client::class, $this->parser->parser);
    }

    public function test_it_can_set_device()
    {
        $this->assertInstanceOf(Device::class, $this->parser->device);
    }

    public function test_it_can_set_user_agent()
    {
        $this->assertInstanceOf(UserAgent::class, $this->parser->userAgent);
    }

    public function test_it_can_set_operating_system()
    {
        $this->assertInstanceOf(OperatingSystem::class, $this->parser->operatingSystem);
    }

    public function test_it_can_set_original_user_agent()
    {
        $this->assertEquals($this->userAgentString, $this->parser->originalUserAgent);
    }
}
