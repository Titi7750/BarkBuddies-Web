<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $userPasswordHasher;
    private $mailer;
    private $managerRegistry;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, ManagerRegistry $managerRegistry)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->mailer = $mailer;
        $this->managerRegistry = $managerRegistry;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendEmailAction = Action::new('sendEmail', 'Send Email')
            ->linkToRoute('admin_app_user_send_email', function ($entity) {
                return [
                    'id' => $entity->getId(),
                ];
            })
            ->addCssClass('btn btn-secondary')
            ->setIcon('fas fa-envelope');

        return $actions
            ->add(Crud::PAGE_INDEX, $sendEmailAction)
            ->add(Crud::PAGE_DETAIL, $sendEmailAction);
    }

    public function sendEmail(int $id): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Impossible de trouver l\'utilisateur');
        }

        $email = $user->getEmail();
        $mailto = "mailto:$email";

        return $this->redirect($mailto);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            ChoiceField::new('roles', 'Roles')
            ->allowMultipleChoices()
            ->setChoices([
                'ROLE_MARKETING' => 'ROLE_MARKETING',
                'ROLE_USER' => 'ROLE_USER',
            ]),
        ];
    }
}
