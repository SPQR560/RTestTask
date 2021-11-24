<?php

namespace App\Tests\Entity;

use App\Entity\Email;
use App\Entity\Phone;
use App\Entity\PhoneType;
use App\Entity\Profile;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $this->validator = $container->get('validator');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAlreadyExist(): void
    {
        $this->expectException(UniqueConstraintViolationException::class);

        $profileRepository = $this->entityManager->getRepository(Profile::class);

        $profile = $profileRepository->findOneBy(['name' => 'Иван']);

        $newEmail = new Email();
        $newEmail->setProfile($profile);
        $newEmail->setIsMain(false);
        $newEmail->setEmail("ivanoff@mail.ru");

        $this->entityManager->persist($newEmail);

        $this->entityManager->flush();
    }

    public function testEmailValidation(): void
    {
        $newEmail = new Email();
        $newEmail->setIsMain(false);

        $newEmail->setEmail("asdasd@mail.com");
        $errors = $this->validator->validate($newEmail);
        $this->assertCount(0, $errors);

        $newEmail->setEmail("dajsldas");
        $errors = $this->validator->validate($newEmail);
        $this->assertCount(1, $errors);

    }
}
