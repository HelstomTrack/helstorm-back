<?php

namespace App\Tests\Functional;

use App\Entity\Diet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DietControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // Nettoyer les anciennes données
        $this->em->createQuery('DELETE FROM App\Entity\Diet d')->execute();
    }

    private function getJsonResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    public function testCreateDiet(): void
    {
        $this->client->request(
            'POST',
            '/diets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Keto'])
        );

        $this->assertResponseStatusCodeSame(201);

        $data = $this->getJsonResponse();
        $this->assertEquals('Keto', $data['name']);
    }

    public function testGetAllDiets(): void
    {
        $diet = new Diet();
        $diet->setName('Vegan');
        $this->em->persist($diet);
        $this->em->flush();

        $this->client->request('GET', '/diets');

        $this->assertResponseIsSuccessful();

        $data = $this->getJsonResponse();
        $this->assertNotEmpty($data);
        $this->assertEquals('Vegan', $data[0]['name']);
    }

    public function testShowDiet(): void
    {
        $diet = new Diet();
        $diet->setName('Paleo');
        $this->em->persist($diet);
        $this->em->flush();

        $this->client->request('GET', '/diets/' . $diet->getId());

        $this->assertResponseIsSuccessful();

        $data = $this->getJsonResponse();
        $this->assertEquals('Paleo', $data['name']);
    }

    public function testUpdateDiet(): void
    {
        $diet = new Diet();
        $diet->setName('OldName');
        $this->em->persist($diet);
        $this->em->flush();

        $this->client->request(
            'PUT',
            '/diets/' . $diet->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'NewName'])
        );

        $this->assertResponseIsSuccessful();

        $data = $this->getJsonResponse();
        $this->assertEquals('NewName', $data['name']);
    }

    public function testDeleteDiet(): void
    {
        $diet = new Diet();
        $diet->setName('DeleteMe');
        $this->em->persist($diet);
        $this->em->flush();

        $dietId = $diet->getId();

        $this->client->request('DELETE', '/diets/' . $dietId);

        $this->assertResponseStatusCodeSame(204);

        $dietCheck = $this->em->getRepository(Diet::class)->find($dietId);
        $this->assertNull($dietCheck);
    }
}
