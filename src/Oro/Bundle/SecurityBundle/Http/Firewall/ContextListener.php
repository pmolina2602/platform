<?php

namespace Oro\Bundle\SecurityBundle\Http\Firewall;

use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SecurityBundle\Authentication\Token\OrganizationContextTokenInterface;
use Oro\Bundle\SecurityBundle\Exception\OrganizationAccessDeniedException;
use Oro\Bundle\UserBundle\Entity\AbstractUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Manages the organization aware security context persistence through a session.
 */
class ContextListener
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var ManagerRegistry */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ManagerRegistry       $doctrine
     * @param LoggerInterface       $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ManagerRegistry $doctrine,
        LoggerInterface $logger
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    /**
     * Refresh organization context in token
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        if (!$token instanceof OrganizationContextTokenInterface) {
            return;
        }

        $organization = $token->getOrganizationContext();
        if (null === $organization) {
            return;
        }

        $isAccessGranted = false;
        $organization = $this->refreshOrganization($organization);
        if (null !== $organization) {
            $token->setOrganizationContext($organization);

            $user = $token->getUser();
            if (!$user instanceof AbstractUser || $user->isBelongToOrganization($organization, true)) {
                $isAccessGranted = true;
            }
        }

        if (!$isAccessGranted) {
            $this->denyAccess($event);
        }
    }

    /**
     * @param Organization $organization
     *
     * @return Organization|null
     */
    private function refreshOrganization(Organization $organization): ?Organization
    {
        $organizationId = $organization->getId();

        $organization = $this->doctrine->getManagerForClass(Organization::class)
            ->find(Organization::class, $organizationId);

        if (null === $organization) {
            $this->logger->error(sprintf('Could not find organization by id %s', $organizationId));
        }

        return $organization;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws OrganizationAccessDeniedException
     */
    private function denyAccess(GetResponseEvent $event): void
    {
        /** @var OrganizationContextTokenInterface $token */
        $token = $this->tokenStorage->getToken();

        $this->tokenStorage->setToken(null);

        $exception = new OrganizationAccessDeniedException();
        $exception->setOrganizationName($token->getOrganizationContext()->getName());
        $exception->setToken($token);

        $session = $event->getRequest()->getSession();
        if ($session) {
            $session->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        throw $exception;
    }
}
