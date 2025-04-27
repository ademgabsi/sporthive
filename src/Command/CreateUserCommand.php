<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new Utilisateur();
        $user->setNom('Gabsi');
        $user->setPrenom('Admin');
        $user->setEmail('gabsi@example.com');
        $user->setMot_de_passe('password');
        $user->setAdresse('123 Main St');
        $user->setDate_naissance(new \DateTime('1990-01-01'));
        $user->setNumero_tel('1234567890');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User created successfully with ID: ' . $user->getId());

        return Command::SUCCESS;
    }
} 