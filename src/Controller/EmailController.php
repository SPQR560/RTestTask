<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Profile;
use App\Form\EmailFormType;
use App\Repository\EmailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected EmailRepository $emailRepository;

    public function __construct(EntityManagerInterface $entityManager, EmailRepository $emailRepository)
    {
        $this->entityManager = $entityManager;
        $this->emailRepository = $emailRepository;
    }

    /**
     * @Route("/update-email/{id}", name="update_email")
     */
    public function updateEmail(Email $email, Request $request): Response
    {
        $form = $this->createForm(EmailFormType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($email->getIsMain()) {
                $mainEmail = $this->emailRepository->findOneBy(['isMain' => true]);
                if (isset($mainEmail) && $mainEmail !== $email) {
                    $mainEmail->setIsMain(false);
                    $this->entityManager->persist($email);
                }
            }

            $this->entityManager->persist($email);
            $this->entityManager->flush();

            return $this->redirectToRoute($request->get('redirect'), ['id' => $email->getProfile()->getId()]);
        }

        return new Response($this->renderView('email/addEmail.html.twig',
            [
                'email_form' => $form->createView(),
            ]
        ));
    }

    /**
     * @Route("/delete-email/{id}", name="delete_email")
     */
    public function deleteEmail(Email $email, Request $request): Response
    {
        $this->entityManager->remove($email);
        $this->entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/make-email-main/{id}", name="make_email_main")
     */
    public function makeEmailMain(Email $email, Request $request): Response
    {
        if (!$email->getIsMain()) {
            $mainEmail = $this->emailRepository->findOneBy(['isMain' => true, 'profile' => $email->getProfile()]);

            if(isset($mainEmail)) {
                $mainEmail->setIsMain(false);
                $this->entityManager->persist($mainEmail);
            }

            $email->setIsMain(true);
            $this->entityManager->persist($email);

            $this->entityManager->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/add-email/{id}", name="add_email")
     */
    public function addEmail(Request $request, Profile $profile): Response
    {
        $email = new Email();
        $email->setProfile($profile);
        $form = $this->createForm(EmailFormType::class, $email);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($email->getIsMain()) {
                $mainEmail = $this->emailRepository->findOneBy(['isMain' => true, 'profile' => $email->getProfile()]);
                if (!is_null($mainEmail) && $mainEmail !== $email) {
                    $mainEmail->setIsMain(false);
                    $this->entityManager->persist($email);
                }
            }

            $this->entityManager->persist($email);
            $this->entityManager->flush();

            return $this->redirectToRoute($request->get('redirect'), ['id' => $email->getProfile()->getId()]);
        }

        return new Response($this->renderView('email/addEmail.html.twig',
            [
                'email_form' => $form->createView(),
            ]
        ));
    }
}
