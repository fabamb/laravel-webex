<?php

namespace Fabamb\LaravelWebex\Test;

use Fabamb\LaravelWebex\Console\Commands\WebexSendCommand;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Tester\CommandTester;

class WebexSendCommandTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new TestContainer;
        $this->container->instance('config', new TestConfigRepository([
            'webex' => [
                'url' => 'https://webex.test/messages',
                'token' => 'configured-token',
                'room_id' => 'configured-room',
                'to_person_email' => null,
            ],
        ]));
        Container::setInstance($this->container);
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_token_option_without_value_prompts_for_hidden_token(): void
    {
        $command = new TestWebexSendCommand;
        $command->setLaravel($this->container);
        $tester = new CommandTester($command);
        $tester->setInputs(['prompted-token']);

        $exitCode = $tester->execute([
            'message' => 'Test message',
            '--token' => null,
        ]);

        $this->assertSame(WebexSendCommand::SUCCESS, $exitCode);
        $this->assertSame('prompted-token', $command->sentToken);
        $this->assertStringContainsString('Webex access token', $tester->getDisplay());
        $this->assertStringNotContainsString('prompted-token', $tester->getDisplay());
    }

    public function test_empty_prompted_token_does_not_fall_back_to_configured_token(): void
    {
        $command = new TestWebexSendCommand;
        $command->setLaravel($this->container);
        $tester = new CommandTester($command);
        $tester->setInputs(['']);

        $exitCode = $tester->execute([
            'message' => 'Test message',
            '--token' => null,
        ]);

        $this->assertSame(WebexSendCommand::FAILURE, $exitCode);
        $this->assertNull($command->sentToken);
        $this->assertStringContainsString('Webex notification token is not configured.', $tester->getDisplay());
    }

    public function test_missing_token_option_uses_configured_token_without_prompting(): void
    {
        $command = new TestWebexSendCommand;
        $command->setLaravel($this->container);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute(['message' => 'Test message']);

        $this->assertSame(WebexSendCommand::SUCCESS, $exitCode);
        $this->assertSame('configured-token', $command->sentToken);
        $this->assertStringNotContainsString('Webex access token', $tester->getDisplay());
    }
}

class TestWebexSendCommand extends WebexSendCommand
{
    public ?string $sentToken = null;

    protected function sendRequest(string $token, array $request): ResponseInterface
    {
        $this->sentToken = $token;

        return new Response(200);
    }
}

class TestConfigRepository
{
    /**
     * @param  array<string, mixed>  $items
     */
    public function __construct(private array $items) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->items, $key, $default);
    }
}

class TestContainer extends Container
{
    public function runningUnitTests(): bool
    {
        return true;
    }
}
