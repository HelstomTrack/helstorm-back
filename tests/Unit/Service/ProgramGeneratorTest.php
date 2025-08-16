<?php

namespace App\Tests\Service;

use App\Entity\Programs;
use App\Entity\User;
use App\Entity\UserMetrics;
use App\Service\OpenAiService;
use App\Service\ProgramGenerator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProgramGeneratorTest extends TestCase
{
    private function makeUser(): User
    {
        $m = (new UserMetrics())
            ->setAge(30)->setWeight(80)->setHeight(180)
            ->setGoal('fat_loss')->setLevel('intermediate')->setGender('male');

        return (new User())
            ->setEmail('john@example.com')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setUserMetrics($m);
    }

    public function testGenerateAndSaveSuccess(): void
    {
        $openAi = $this->createMock(OpenAiService::class);
        $em     = $this->createMock(EntityManagerInterface::class);

        $user = $this->makeUser();

        $openAi->expects(self::once())
            ->method('askAssistantWithRawJson')
            ->with(self::anything(), self::callback(fn($json) => is_string($json) && str_contains($json, '"type":"program_generation_request"')))
            ->willReturn([
                'text'      => json_encode(['plan' => 'Do stuff'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'thread_id' => 'thr_123',
                'run_id'    => 'run_456',
            ]);

        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(Programs::class));
        $em->expects(self::once())->method('flush');

        $service = new ProgramGenerator($openAi, $em);

        // Simulation de variable d'env
        $_ENV['OPENAI_ASSISTANT_ID'] = 'asst_abc';

        $program = $service->generateAndSave($user);

        self::assertInstanceOf(Programs::class, $program);
        self::assertSame($user, $program->getUser());
        self::assertIsArray($program->getContent());
        self::assertSame('Do stuff', $program->getContent()['plan']);
        self::assertSame('thr_123', $program->getThreadId());
        self::assertSame('run_456', $program->getRunId());
        self::assertInstanceOf(\DateTimeImmutable::class, $program->getCreatedAt());
    }

    public function testGenerateAndSaveWithNonJsonFallsBackToRawText(): void
    {
        $openAi = $this->createMock(OpenAiService::class);
        $em     = $this->createMock(EntityManagerInterface::class);

        $user = $this->makeUser();

        $openAi->method('askAssistantWithRawJson')
            ->willReturn([
                'text' => 'plain text from model',
            ]);

        $em->expects(self::once())->method('persist')->with(self::isInstanceOf(Programs::class));
        $em->expects(self::once())->method('flush');

        $service = new ProgramGenerator($openAi, $em);
        $_ENV['OPENAI_ASSISTANT_ID'] = 'asst_abc';

        $program = $service->generateAndSave($user);

        self::assertArrayHasKey('raw_text', $program->getContent());
        self::assertSame('plain text from model', $program->getContent()['raw_text']);
    }

    public function testGenerateAndSaveThrowsOnOpenAiError(): void
    {
        $openAi = $this->createMock(OpenAiService::class);
        $em     = $this->createMock(EntityManagerInterface::class);

        $user = $this->makeUser();

        $openAi->method('askAssistantWithRawJson')
            ->willReturn(['error' => 'rate_limit']);

        $service = new ProgramGenerator($openAi, $em);
        $_ENV['OPENAI_ASSISTANT_ID'] = 'asst_abc';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAI error: rate_limit');

        $service->generateAndSave($user);
    }
}
