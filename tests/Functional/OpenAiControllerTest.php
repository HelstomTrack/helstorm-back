<?php

namespace App\Tests\Functional;

use App\Service\OpenAiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OpenAiControllerTest extends WebTestCase
{
    public function testChatMissingJson(): void
    {
        $client = static::createClient();

        $client->request('POST', '/chat', content: '');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('Corps JSON manquant', $json['error'] ?? null);
    }

    public function testChatInvalidJson(): void
    {
        $client = static::createClient();

        $client->request('POST', '/chat', content: '{invalid json}');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertStringContainsString('JSON invalide', $json['error'] ?? '');
    }

    public function testChatOpenAiError(): void
    {
        $client = static::createClient();

        $fakeService = $this->createMock(OpenAiService::class);
        $fakeService->method('askAssistantWithRawJson')->willReturn([
            'error' => 'OpenAI down',
        ]);

        static::getContainer()->set(OpenAiService::class, $fakeService);

        $client->request('POST', '/chat', content: json_encode(['prompt' => 'Hello']));

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('OpenAI down', $json['error'] ?? null);
    }

    public function testChatSuccess(): void
    {
        $client = static::createClient();

        $fakeService = $this->createMock(OpenAiService::class);
        $fakeService->method('askAssistantWithRawJson')->willReturn([
            'thread_id' => 'thr_123',
            'run_id'    => 'run_456',
            'text'      => json_encode(['reply' => 'Hello world']),
        ]);

        static::getContainer()->set(OpenAiService::class, $fakeService);

        $client->request('POST', '/chat', content: json_encode(['prompt' => 'Hello']));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $json = json_decode($client->getResponse()->getContent(), true);

        self::assertSame('thr_123', $json['thread_id'] ?? null);
        self::assertSame('run_456', $json['run_id'] ?? null);
        self::assertSame(['reply' => 'Hello world'], $json['message'] ?? null);
    }
}
