<?php

namespace App\Doctrine;

use App\Entity\Ticket;
use App\Entity\Application;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

final class TicketsExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        /**@var Application $application */
        if (Ticket::class !== $resourceClass || $this->security->isGranted('ROLE_APPLICATION_ADMIN') || null === $application = $this->security->getUser()) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.application = :current_application', $rootAlias));
        $queryBuilder->setParameter('current_application',$application->getId());
    }
}