<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\UserAccount;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// @phpstan-ignore-next-line
final class UserPasswordHasher implements ProcessorInterface
{
    // @phpstan-ignore-next-line
    public function __construct(private readonly ProcessorInterface $processor, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }
    
    /**
     * @var UserAccount $data
     */
    // @phpstan-ignore-next-line
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
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
