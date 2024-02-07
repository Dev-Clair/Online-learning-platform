<?php

namespace App\Test\Controller;

use App\Entity\Courses;
use App\Repository\CoursesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoursesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CoursesRepository $repository;
    private string $path = '/courses/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Courses::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'course[title]' => 'Testing',
            'course[description]' => 'Testing',
            'course[duration]' => 'Testing',
            'course[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/courses/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Courses();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDuration('My Title');
        $fixture->setValue('120.00');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Courses();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Course Title');
        $fixture->setDuration(75);
        $fixture->setValue('125.00');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'course[title]' => 'My Title',
            'course[description]' => 'My Course Title',
            'course[duration]' => 75,
            'course[value]' => '125.00',
        ]);

        self::assertResponseRedirects('/courses/');

        $fixture = $this->repository->findAll();

        self::assertSame('My Title', $fixture[0]->getTitle());
        self::assertSame('My Course Title', $fixture[0]->getDescription());
        self::assertSame(75, $fixture[0]->getDuration());
        self::assertSame('125.00', $fixture[0]->getValue());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Courses();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Course Title');
        $fixture->setDuration(75);
        $fixture->setValue('125.00');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/courses/');
    }
}
