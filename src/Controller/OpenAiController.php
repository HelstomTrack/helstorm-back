<?php
namespace App\Controller;

use App\Service\OpenAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAiController extends AbstractController
{
    private OpenAiService $openAiService;
    private HttpClientInterface $client;

    public function __construct(OpenAiService $openAiService, HttpClientInterface $client)
    {
        $this->openAiService = $openAiService;
        $this->client = $client;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/chat', name: 'api_chat', methods: ['POST', 'GET'])]
    public function chat(Request $request, OpenAiService $openAiService): JsonResponse
    {
        $rawJson = $request->getContent();
        if ($rawJson === '' || $rawJson === null) {
            return $this->json(['error' => 'Corps JSON manquant'], 400);
        }

        json_decode($rawJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'JSON invalide: '.json_last_error_msg()], 400);
        }

        $assistantId = $_ENV['OPENAI_ASSISTANT_ID'];

        $result = $openAiService->askAssistantWithRawJson($assistantId, $rawJson);

        if (!empty($result['error'])) {
            return $this->json(['error' => $result['error']], 500);
        }

        $message = json_decode($result['text'], true);

        return $this->json([
            'thread_id' => $result['thread_id'],
            'run_id'    => $result['run_id'],
            'message'   => $message,
        ]);
    }
}
