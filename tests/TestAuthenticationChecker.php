<?php

namespace hunomina\Routing\Auth\Test;

use hunomina\Routing\Auth\Firewall\Checker\AuthenticationCheckerInterface;
use hunomina\Routing\Auth\Firewall\SecurityContext\Role;
use hunomina\Routing\Auth\Firewall\SecurityContext\SecurityContext;
use hunomina\Routing\Auth\Firewall\SecurityContext\SecurityContextException;
use hunomina\Routing\Auth\UserInterface;

class TestAuthenticationChecker implements AuthenticationCheckerInterface
{
    /**
     * @return bool
     * Return true if an user is authenticated
     */
    public function isAuthenticated(): bool
    {
        $user = unserialize($_SESSION['auth']);
        return $user instanceof UserInterface;
    }

    /**
     * @return UserInterface
     * Return the authenticated UserInterface
     * Implement to retrieve an user from your application (cookies, sessions, ...)
     */
    public function getAuthenticatedUser(): ?UserInterface
    {
        $user = unserialize($_SESSION['auth']);
        return $user instanceof UserInterface ? $user : null;
    }

    /**
     * @param UserInterface|null $user
     * @param SecurityContext $securityContext
     * @param string $method
     * @param string $url
     * @return bool
     * @throws SecurityContextException
     */
    public function checkAuthorization(?UserInterface $user, SecurityContext $securityContext, string $method, string $url): bool
    {
        $ruleRoles = $securityContext->getRolesByRule($method, $url);

        if (count($ruleRoles) === 0) {
            return true;
        }

        if (!($user instanceof UserInterface)) {
            return false;
        }

        /** @var Role[] $userRoles */
        $userRoles = [];
        foreach ($user->getRoles() as $role) {
            $userRole = $securityContext->getRoleByName($role);
            if (!($userRole instanceof Role)) {
                throw new SecurityContextException('The `' . $role . '` role does not exist in this security context');
            }
            $userRoles[] = $userRole;
        }

        foreach ($ruleRoles as $ruleRole) {
            foreach ($userRoles as $userRole) {
                if ($ruleRole->getName() === $userRole->getName()) {
                    return true;
                }

                foreach ($userRole->getChildren() as $childUserRole) {
                    if ($ruleRole->getName() === $childUserRole->getName()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}