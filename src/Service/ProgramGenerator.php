<?php

namespace App\Service;

use App\Entity\Programs;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProgramGenerator
{
    public function __construct(
        private OpenAiService $openAi,
        private EntityManagerInterface $em,
    ) {}

    /**
     * Génère le programme via OpenAI et le sauvegarde lié à $user.
     * Retourne l'entité Program créée.
     */
    public function generateAndSave(User $user): Programs
    {
        $assistantId = $_ENV['OPENAI_ASSISTANT_ID'];

        $payload = [
            'type'     => 'program_generation_request',
            'user'     => [
                'firstname' => $user->getFirstname(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
            ],
            'metrics'  => [
                'age'    => $user->getUserMetrics()?->getAge(),
                'weight' => $user->getUserMetrics()?->getWeight(),
                'height' => $user->getUserMetrics()?->getHeight(),
                'goal'   => $user->getUserMetrics()?->getGoal(),
                'level'  => $user->getUserMetrics()?->getLevel(),
                'gender' => $user->getUserMetrics()?->getGender(),
            ],
        ];

        $rawJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $result = $this->openAi->askAssistantWithRawJson($assistantId, $rawJson);

        if (!empty($result['error'])) {
            throw new \RuntimeException('OpenAI error: ' . $result['error']);
        }

        $decoded = json_decode($result['text'] ?? '', true);
        if ($decoded === null) {
            $decoded = ['raw_text' => $result['text']];
        }

        $program = (new Programs())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser($user)
            ->setContent($decoded);

        if (!empty($result['thread_id'])) {
            $program->setThreadId($result['thread_id']);
        }
        if (!empty($result['run_id'])) {
            $program->setRunId($result['run_id']);
        }
        $this->em->persist($program);
        $this->em->flush();

        return $program;
    }
}
