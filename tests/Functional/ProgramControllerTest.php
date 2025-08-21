<?php

namespace App\Tests\Functional;

use App\Entity\Programs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProgramControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // nettoyer la table
        $this->em->createQuery('DELETE FROM App\Entity\Programs p')->execute();
    }

    public function testCreatePrograms(): void
    {
        $this->client->request(
            'POST',
            '/programs',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'threadId' => 't1',
                'runId' => 'r1',
                'content' => ['foo' => 'bar']
            ])
        );

        $this->assertResponseStatusCodeSame(201);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('t1', $response['threadId']);
    }

    public function testGetAllPrograms(): void
    {
        $program = new Programs();
        $program->setThreadId('t2')->setRunId('r2')->setContent(['a' => 1])->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($program);
        $this->em->flush();

        $this->client->request('GET', '/programs');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($response);
    }

    public function testShowPrograms(): void
    {
        $program = new Programs();
        $program->setThreadId('t3')->setRunId('r3')->setContent(['b' => 2])->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($program);
        $this->em->flush();

        $this->client->request('GET', '/programs/' . $program->getId());

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('t3', $response['threadId']);
    }

    public function testUpdatePrograms(): void
    {
        $program = new Programs();
        $program->setThreadId('t4')->setRunId('r4')->setContent([])->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($program);
        $this->em->flush();

        $this->client->request(
            'PUT',
            '/programs/' . $program->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['threadId' => 'updated-thread'])
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('updated-thread', $response['threadId']);
    }

    public function testDeletePrograms(): void
    {
        $program = new Programs();
        $program->setCreatedAt(new \DateTimeImmutable());
        $program->setContent(['step1' => 'warmup']);
        $program->setThreadId('threadToDelete');
        $program->setRunId('runToDelete');

        $this->em->persist($program);
        $this->em->flush();

        $id = $program->getId();

        $this->client->request('DELETE', '/programs/' . $id);

        $this->assertResponseStatusCodeSame(204);

        $programCheck = $this->em->getRepository(Programs::class)->find($id);
        $this->assertNull($programCheck);
    }

}
