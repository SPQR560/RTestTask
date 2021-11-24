<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\Profile;
use App\Form\PhoneFormType;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected PhoneRepository $phoneRepository;

    public function __construct(EntityManagerInterface $entityManager, PhoneRepository $phoneRepository)
    {
        $this->entityManager = $entityManager;
        $this->phoneRepository = $phoneRepository;
    }

    /**
     * @Route("/make-phone-main/{id}", name="make_phone_main")
     */
    public function makePhoneMain(Phone $phone, Request $request): Response
    {
        if (!$phone->getIsMain()) {
            $mainPhone = $this->phoneRepository->findOneBy(['isMain' => true, 'profile' => $phone->getProfile()]);

            if(isset($mainPhone)) {
                $mainPhone->setIsMain(false);
                $this->entityManager->persist($mainPhone);
            }

            $phone->setIsMain(true);
            $this->entityManager->persist($phone);

            $this->entityManager->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/update-phone/{id}", name="update_phone")
     */
    public function updatePhone(Phone $phone, Request $request): Response
    {
        $form = $this->createForm(PhoneFormType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($phone->getIsMain()) {
                $mainPhone = $this->phoneRepository->findOneBy(['isMain' => true, 'profile' => $phone->getProfile()]);
                if (isset($mainPhone) && $mainPhone !== $phone) {
                    $mainPhone->setIsMain(false);
                    $this->entityManager->persist($phone);
                }
            }

            $this->entityManager->persist($phone);
            $this->entityManager->flush();

            if (is_null(($request->get('redirect')))) {
                return $this->redirectToRoute('index');
            }

            return $this->redirectToRoute($request->get('redirect'), ['id' => $phone->getProfile()->getId()]);
        }

        return new Response($this->renderView('phone/addPhone.html.twig',
            [
                'phone_form' => $form->createView(),
            ]
        ));
    }

    /**
     * @Route("/delete-phone/{id}", name="delete_phone")
     */
    public function deletePhone(Phone $phone, Request $request): Response
    {
        $this->entityManager->remove($phone);
        $this->entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/add-phone/{id}", name="add_phone")
     */
    public function addPhone(Request $request, Profile $profile): Response
    {
        $phone = new Phone();
        $phone->setProfile($profile);
        $form = $this->createForm(PhoneFormType::class, $phone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($phone->getIsMain()) {
                $mainPhone = $this->phoneRepository->findOneBy(['isMain' => true, 'profile' => $phone->getProfile()]);
                if (!is_null($mainPhone) && $mainPhone !== $phone) {
                    $mainPhone->setIsMain(false);
                    $this->entityManager->persist($phone);
                }
            }

            $this->entityManager->persist($phone);
            $this->entityManager->flush();

            return $this->redirectToRoute($request->get('redirect'), ['id' => $phone->getProfile()->getId()]);
        }

        return new Response($this->renderView('phone/addPhone.html.twig',
            [
                'phone_form' => $form->createView(),
            ]
        ));
    }
}
