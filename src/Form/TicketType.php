<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Ticket;
use App\Entity\Application;
use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use App\Repository\ApplicationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var UserAccount $user */
        $user = $this->security->getUser();
        $builder
            ->add('application', EntityType::class, [
                'class' => Application::class,
                'choice_label' => 'name',
                'query_builder' => function (ApplicationRepository $repo) use ($user) {
                    if (in_array('ROLE_ADMIN', $user->getRoles())) {
                        return $repo->createQueryBuilder('a');
                    }
                    return $repo->createQueryBuilder('a')
                        ->where('a.userAccount = :user')
                        ->setParameter('user', $user);
                }
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'choice_label' => 'name'
            ])
            ->add('subject', options: [
                'label' => 'Sujet',
            ])
            ->add('content', options: [
                'label' => 'Description',
                'attr' => ['rows' => '7'],
            ])
            ->add('Envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
