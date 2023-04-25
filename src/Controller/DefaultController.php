<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    #[Route('/', name: 'app_default')]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $data = $form->getData();

            $email = (new Email())
                ->from('marketing@marketing.com')
                ->to($data->getEmail())
                ->subject('Nouveau message de ' . $data->getFirstname() . ' ' . $data->getLastname())
                ->text('Merci de vous être inscrit sur notre site, on vous tiendra informé une fois que l\'application sera disponible !');

            $mailer->send($email);

            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'DefaultController',
        ]);
    }
}
