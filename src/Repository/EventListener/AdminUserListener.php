<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use App\Entity\AdminUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserListener implements EventSubscriber
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof AdminUser) {
            $this->hashPassword($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof AdminUser) {
            $this->hashPassword($entity);
        }
    }

    private function hashPassword(AdminUser $adminUser)
    {

    
        $plainPassword = $adminUser->getPlainPassword();


        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($adminUser, $plainPassword);
            $adminUser->setPassword($hashedPassword);
        }
    }

    private function setDefaultRoles(AdminUser $adminUser)
    {
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
    }
}
