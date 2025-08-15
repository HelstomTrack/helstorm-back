<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAiService
{
    public function __construct(private HttpClientInterface $httpClient) {}

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function askAssistantWithRawJson(string $assistantId, string $rawJson): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
            'Content-Type'  => 'application/json',
            'OpenAI-Beta'   => 'assistants=v2',
        ];

        // (Optionnel) Valider l’assistant
        $check = $this->httpClient->request('GET', "https://api.openai.com/v1/assistants/{$assistantId}", [
            'headers' => $headers,
        ]);
        if ($check->getStatusCode() >= 400) {
            return ['error' => 'Assistant introuvable: '.$check->getContent(false)];
        }

        // Thread
        $resp = $this->httpClient->request('POST', 'https://api.openai.com/v1/threads', [
            'headers' => $headers,
            'json'    => [],
        ]);
        if ($resp->getStatusCode() >= 400) {
            return ['error' => 'Création thread échouée (HTTP '.$resp->getStatusCode().'): '.$resp->getContent(false)];
        }
        $thread = $resp->toArray(false);
        $threadId = $thread['id'] ?? null;
        if (!$threadId) return ['error' => 'Réponse threads sans id: '.json_encode($thread)];

        // Message (TON JSON brut)
        $resp = $this->httpClient->request('POST', "https://api.openai.com/v1/threads/{$threadId}/messages", [
            'headers' => $headers,
            'json'    => [
                'role'    => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $rawJson]
                ],
            ],
        ]);
        if ($resp->getStatusCode() >= 400) {
            return ['error' => 'Ajout message échouée (HTTP '.$resp->getStatusCode().'): '.$resp->getContent(false)];
        }

        // Run
        $resp = $this->httpClient->request('POST', "https://api.openai.com/v1/threads/{$threadId}/runs", [
            'headers' => $headers,
            'json'    => [
                'assistant_id' => $assistantId,
                // 'model' => 'gpt-4o', // si tu veux forcer
            ],
        ]);
        if ($resp->getStatusCode() >= 400) {
            return ['error' => 'Lancement run échoué (HTTP '.$resp->getStatusCode().'): '.$resp->getContent(false)];
        }
        $run = $resp->toArray(false);
        $runId = $run['id'] ?? null;
        if (!$runId) return ['error' => 'Réponse run sans id: '.json_encode($run)];

        // Poll
        $deadlineMs = (int)(microtime(true) * 1000) + 60_000;
        do {
            usleep(500_000);
            $resp = $this->httpClient->request('GET', "https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}", [
                'headers' => $headers,
            ]);
            if ($resp->getStatusCode() >= 400) {
                return ['error' => 'Statut run échoué (HTTP '.$resp->getStatusCode().'): '.$resp->getContent(false)];
            }
            $runStatus = $resp->toArray(false);

            if (($runStatus['status'] ?? null) === 'requires_action') {
                return ['error' => 'Le run requiert une action (tools non gérés ici).'];
            }

            if ((int)(microtime(true) * 1000) > $deadlineMs) {
                return ['error' => 'Timeout en attendant la complétion du run.'];
            }
        } while (in_array($runStatus['status'] ?? '', ['queued','in_progress','cancelling'], true));

        if (($runStatus['status'] ?? null) !== 'completed') {
            $msg = $runStatus['last_error']['message'] ?? json_encode($runStatus);
            return ['error' => "Run non complété: $msg"];
        }

        // Dernier message assistant
        $resp = $this->httpClient->request('GET', "https://api.openai.com/v1/threads/{$threadId}/messages?limit=20", [
            'headers' => $headers,
        ]);
        if ($resp->getStatusCode() >= 400) {
            return ['error' => 'Lecture messages échouée (HTTP '.$resp->getStatusCode().'): '.$resp->getContent(false)];
        }
        $msgs = $resp->toArray(false);

        $assistantText = null;
        foreach ($msgs['data'] as $msg) {
            if (($msg['role'] ?? '') === 'assistant') {
                $buf = [];
                foreach ($msg['content'] ?? [] as $part) {
                    if (($part['type'] ?? '') === 'text') {
                        $buf[] = $part['text']['value'] ?? '';
                    }
                }
                $assistantText = trim(implode("\n", array_filter($buf)));
                if ($assistantText !== '') break;
            }
        }

        return [
            'thread_id' => $threadId,
            'run_id'    => $runId,
            'text'      => $assistantText ?? '',
        ];
    }
}
