<?php

namespace App\Tests\Service;

use App\Service\OpenAiService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenAiServiceTest extends TestCase
{
    private $httpClient;
    private $service;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->service = new OpenAiService($this->httpClient);

        // Fake API Key (sinon ton service plante sur $_ENV)
        $_ENV['OPENAI_API_KEY'] = 'fake-key';
    }

    public function testAssistantNotFound(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn('Not Found');

        $this->httpClient
            ->method('request')
            ->with('GET', 'https://api.openai.com/v1/assistants/bad-assistant', $this->anything())
            ->willReturn($response);

        $result = $this->service->askAssistantWithRawJson('bad-assistant', '{}');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Assistant introuvable', $result['error']);
    }

    public function testSuccessfulFlow(): void
    {
        // 1. Assistant exists
        $assistantResp = $this->createMock(ResponseInterface::class);
        $assistantResp->method('getStatusCode')->willReturn(200);

        // 2. Thread created
        $threadResp = $this->createMock(ResponseInterface::class);
        $threadResp->method('getStatusCode')->willReturn(200);
        $threadResp->method('toArray')->willReturn(['id' => 'thread-123']);

        // 3. Message added
        $messageResp = $this->createMock(ResponseInterface::class);
        $messageResp->method('getStatusCode')->willReturn(200);

        // 4. Run created
        $runResp = $this->createMock(ResponseInterface::class);
        $runResp->method('getStatusCode')->willReturn(200);
        $runResp->method('toArray')->willReturn(['id' => 'run-456']);

        // 5. Poll completed
        $statusResp = $this->createMock(ResponseInterface::class);
        $statusResp->method('getStatusCode')->willReturn(200);
        $statusResp->method('toArray')->willReturn(['status' => 'completed']);

        // 6. Messages from assistant
        $finalMsgResp = $this->createMock(ResponseInterface::class);
        $finalMsgResp->method('getStatusCode')->willReturn(200);
        $finalMsgResp->method('toArray')->willReturn([
            'data' => [
                [
                    'role' => 'assistant',
                    'content' => [
                        ['type' => 'text', 'text' => ['value' => 'Hello World']]
                    ]
                ]
            ]
        ]);

        // Ordonner les retours successifs de HttpClient::request()
        $this->httpClient
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $assistantResp, // check assistant
                $threadResp,    // create thread
                $messageResp,   // add message
                $runResp,       // create run
                $statusResp,    // poll run
                $finalMsgResp   // get messages
            );

        $result = $this->service->askAssistantWithRawJson('ok-assistant', '{"foo":"bar"}');

        $this->assertSame('thread-123', $result['thread_id']);
        $this->assertSame('run-456', $result['run_id']);
        $this->assertSame('Hello World', $result['text']);
    }

    public function testRunNotCompleted(): void
    {
        $assistantResp = $this->createMock(ResponseInterface::class);
        $assistantResp->method('getStatusCode')->willReturn(200);

        $threadResp = $this->createMock(ResponseInterface::class);
        $threadResp->method('getStatusCode')->willReturn(200);
        $threadResp->method('toArray')->willReturn(['id' => 'thread-123']);

        $messageResp = $this->createMock(ResponseInterface::class);
        $messageResp->method('getStatusCode')->willReturn(200);

        $runResp = $this->createMock(ResponseInterface::class);
        $runResp->method('getStatusCode')->willReturn(200);
        $runResp->method('toArray')->willReturn(['id' => 'run-456']);

        $statusResp = $this->createMock(ResponseInterface::class);
        $statusResp->method('getStatusCode')->willReturn(200);
        $statusResp->method('toArray')->willReturn(['status' => 'failed', 'last_error' => ['message' => 'Boom']]);

        $this->httpClient
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                $assistantResp,
                $threadResp,
                $messageResp,
                $runResp,
                $statusResp
            );

        $result = $this->service->askAssistantWithRawJson('ok-assistant', '{}');

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Run non complété', $result['error']);
    }
}
