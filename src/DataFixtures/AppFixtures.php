<?php

namespace App\DataFixtures;

use App\Entity\Email;
use App\Entity\Phone;
use App\Entity\PhoneType;
use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setName("Иван");
        $profile->setSurname("Иванов");
        $profile->setPatronymic("Иванович");

        $manager->persist($profile);

        $email = new Email();
        $email->setEmail("ivanoff@mail.ru");
        $email->setIsMain(true);
        $email->setProfile($profile);
        $manager->persist($email);

        $email = new Email();
        $email->setEmail("ivan@mail.ru");
        $email->setIsMain(false);
        $email->setProfile($profile);
        $manager->persist($email);

        $typeHome = new PhoneType();
        $typeHome->setType("Домашний");
        $manager->persist($typeHome);

        $typeMobile = new PhoneType();
        $typeMobile->setType("Мобильный");
        $manager->persist($typeMobile);

        $typeWork = new PhoneType();
        $typeWork->setType("Рабочий");
        $manager->persist($typeWork);

        $phone = new Phone();
        $phone->setNumber("89005553535");
        $phone->setIsMain(true);
        $phone->setProfile($profile);
        $phone->setType($typeHome);
        $manager->persist($phone);

        $phone = new Phone();
        $phone->setNumber("8(800)555-35-36");
        $phone->setIsMain(false);
        $phone->setType($typeWork);
        $phone->setProfile($profile);
        $manager->persist($phone);

        $manager->flush();
    }
}
