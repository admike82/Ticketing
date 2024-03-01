<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\UserAccount;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @template UserAccount
 * @implements ProcessorInterface<UserAccount>
 */
final class UserPasswordHasher implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<PersistProcessor> $processor
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * @param UserAccount $data
     * @param Operation $operation
     * @param array<string,mixed> $uriVariables
     * @param array<string,mixed> $context
     * @return UserAccount
     */
    public function process(
        $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ) {
        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPlainPassword()
        );

        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
