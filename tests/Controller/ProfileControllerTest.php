<?php

namespace App\Tests\Controller;

use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Тестовое задание');
    }

    public function testShow(): void
    {
        /**
         * @var $profile Profile
         */
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['name'=>'Иван']);
        if (isset($profile)) {

            $crawler = $this->client->request('GET', '/show/' . $profile->getId());

            $this->assertResponseIsSuccessful();

            $this->assertSelectorTextContains('h1', $profile->getPatronymic());

            $countOfLi = $crawler->filter('main > ul > li')->count();
            $this->assertEquals(4, $countOfLi);
        } else {
            $this->fail();
        }
    }

    public function testAddProfile(): void
    {
        $crawler = $this->client->request('GET', '/add-profile');

        $this->assertResponseIsSuccessful();
        $countOfProfiles = $crawler->filter('#sidebarMenu a')->count();

        $this->client->submitForm('Submit', [
            'profile_form[name]' =>'name',
            'profile_form[surname]' =>'lastname',
            'profile_form[Patronymic]' =>'patronymic',
        ]);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();

        $newCountOfProfiles = $crawler->filter('#sidebarMenu a')->count();
        $this->assertEquals($newCountOfProfiles - 1, $countOfProfiles);
    }

    public function testUpdate(): void
    {
        /**
         * @var $profile Profile
         */
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['name'=>'Иван']);
        if (isset($profile)) {
            $this->client->request('GET', '/update/' . $profile->getId());

            $this->assertResponseIsSuccessful();

            $name = "asd";
            $this->client->submitForm('Submit', [
                'profile_form[name]' => $name,
                'profile_form[surname]' =>'lastname',
                'profile_form[Patronymic]' =>'Patronymic',
            ]);
            $this->assertResponseRedirects();
            $this->client->followRedirect();

            $this->assertSelectorTextContains('h1', $name);
            $newProfile  = $this->entityManager->getRepository(Profile::class)->findOneBy(['name'=>$name]);

            $this->assertEquals($profile->getId(), $newProfile->getId());

        } else {
            $this->fail();
        }
    }

    public function testDelete(): void
    {
        /**
         * @var $profile Profile
         */
        $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['name'=>'Иван']);
        if (isset($profile)) {

            $this->client->request('GET', '/show/' . $profile->getId());

            $this->assertResponseIsSuccessful();

            $this->client->submitForm('удалить');

            $profile = $this->entityManager->getRepository(Profile::class)->findOneBy(['name'=>'Иван']);
            $this->assertNull($profile);

        } else {
            $this->fail();
        }
    }
}
