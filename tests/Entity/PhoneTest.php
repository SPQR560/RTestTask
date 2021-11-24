<?php

namespace App\Tests\Entity;

use App\Entity\Phone;
use App\Entity\PhoneType;
use App\Entity\Profile;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PhoneTest extends KernelTestCase
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
        $phoneTypeRepository = $this->entityManager->getRepository(PhoneType::class);

        $profile = $profileRepository->findOneBy(['name' => 'Иван']);
        $phoneType = $phoneTypeRepository->findOneBy(['type' => 'Домашний']);

        $newPhone = new Phone();
        $newPhone->setProfile($profile);
        $newPhone->setType($phoneType);
        $newPhone->setIsMain(false);
        $newPhone->setNumber("89005553535");

        $this->entityManager->persist($newPhone);

        $this->entityManager->flush();
    }

    public function testPhoneValidation(): void
    {
        $newPhone = new Phone();
        $newPhone->setIsMain(false);

        $newPhone->setNumber("89005553539");

        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("8(900)5553539");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("8(900)555-35-39");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("+7(900)555-35-39");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("555-35-39");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("5553539");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(0, $errors);

        $newPhone->setNumber("555353");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(1, $errors);

        $newPhone->setNumber("asdasdasd");
        $errors = $this->validator->validate($newPhone);
        $this->assertCount(1, $errors);
    }
}
