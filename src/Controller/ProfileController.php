<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    protected EntityManagerInterface  $entityManager;

    public function __construct(EntityManagerInterface  $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @throws Exception
     */
    protected function sortByMainField(Collection $collection): ArrayCollection
    {
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getIsMain() < $b->getIsMain())? 1 : -1;
        });
        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @Route("/show/{id}", name="show")
     * @throws Exception
     */
    public function show(Profile $profile): Response
    {
        return new Response($this->renderView('profile/show.html.twig',
            [
                'profile' => $profile,
                'emails' => $this->sortByMainField($profile->getEmails()),
                'phones' => $this->sortByMainField($profile->getPhones()),
            ]
        ));
    }

    /**
     * @Route("/add-profile", name="add_profile")
     */
    public function addProfile(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileFormType::class, $profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($profile);
            $this->entityManager->flush();

            return $this->redirectToRoute('show', ['id' => $profile->getId()]);
        }
        return new Response($this->renderView('profile/addprofile.html.twig',
            [
                'profile_form' => $form->createView(),
            ]
        ));
    }

    /**
     * @Route("/update/{id}", name="update")
     * @throws Exception
     */
    public function update(Profile $profile, Request $request): Response
    {
        $form = $this->createForm(ProfileFormType::class, $profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($profile);
            $this->entityManager->flush();
            return $this->redirectToRoute('show', ['id' => $profile->getId()]);
        }

        return new Response($this->renderView('profile/update.html.twig',
            [
                'profile_form' => $form->createView(),
                'profile' => $profile,
                'emails' => $this->sortByMainField($profile->getEmails()),
                'phones' => $this->sortByMainField($profile->getPhones()),
            ]
        ));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Profile $profile): Response
    {
        $this->entityManager->remove($profile);
        $this->entityManager->flush();

        return $this->redirectToRoute('index');
    }
}
