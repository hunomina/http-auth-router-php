<?php

namespace hunomina\Routing\Auth\Firewall\SecurityContext;

use hunomina\Routing\Auth\AuthRoutingException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use function in_array;

class YamlSecurityContext extends SecurityContext
{
    /**
     * @throws AuthRoutingException
     * @throws SecurityContextException
     */
    public function load(): void
    {
        try {
            $yaml_file_content = Yaml::parseFile($this->securityContextFile);
        } catch (ParseException $e) {
            throw new SecurityContextException('Invalid configuration file', 0, $e);
        }

        if (!$this->isSecurityContextValid($yaml_file_content)) {
            throw new SecurityContextException('Invalid configuration file');
        }

        ////////// ROLES //////////

        $role_hierarchy = $yaml_file_content['security']['role_hierarchy'];
        $sortedRoles = self::sortRoles($role_hierarchy);

        foreach ($sortedRoles as $role => $children) {
            $this->roles[] = new Role($role);
        }

        foreach ($sortedRoles as $role => $children) {
            /** @var Role $r */
            $r = $this->getRoleByName($role);
            $c = [];
            foreach ($children as $child) {
                if ($r->getName() !== $child) {
                    $c[] = $this->getRoleByName($child);
                }
            }
            $r->setChildren($c);
        }

        ////////// FIREWALL //////////

        $auth_firewall = $yaml_file_content['security']['auth_firewall'];
        foreach ($auth_firewall as $rule) {

            if (isset($rule['path'])) {
                if (!is_string($rule['path'])) {
                    throw new SecurityContextException('Invalid path : ' . $rule['path']);
                }
            } else {
                throw new SecurityContextException('Invalid security context. Each rule must have a `path` value');
            }
            $path = $rule['path'];

            if (is_string($rule['roles'])) {
                $rule['roles'] = [$rule['roles']];
            } elseif (!is_array($rule['roles'])) {
                throw new SecurityContextException('Invalid roles for rule : ' . $path);
            }

            $methods = [];
            if (isset($rule['methods'])) {
                $m = $rule['methods'];
                if (is_string($m)) {
                    $methods = [$m];
                } elseif (is_array($m)) {
                    foreach ($m as $value) {
                        if (!is_string($value)) {
                            throw new SecurityContextException('Methods for the rule : ' . $path . ' can only be strings');
                        }
                        $methods[] = strtoupper($value);
                    }
                } else {
                    throw new SecurityContextException('Methods for the rule : ' . $path . ' can only be a string or a string array');
                }
            }

            $roles = [];
            foreach ($rule['roles'] as $role) {
                $r = $this->getRoleByName($role);
                if (!($r instanceof Role)) {
                    throw new SecurityContextException('Invalid role `' . $role . '` for rule : ' . $path);
                }
                $roles[] = $r;
            }

            $this->firewallRules[] = new Rule($path, $methods, $roles);
        }
    }

    /**
     * @param $securityContext
     * @return bool
     * Return true if is the security context configuration file is valid
     */
    protected function isSecurityContextValid($securityContext): bool
    {
        if (!is_array($securityContext)) {
            return false;
        }

        if (!isset($securityContext['security']) || !is_array($securityContext['security'])) {
            return false;
        }

        if (!isset($securityContext['security']['auth_firewall']) || !is_array($securityContext['security']['auth_firewall'])) {
            return false;
        }

        if (!isset($securityContext['security']['role_hierarchy']) || !is_array($securityContext['security']['role_hierarchy'])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $roles
     * @return array
     */
    protected static function sortRoles(array $roles): array
    {
        $sortedRoles = [];

        foreach ($roles as $role => &$children) {

            if (!isset($sortedRoles[$role])) {
                $sortedRoles[$role] = [$role];
            }

            $children = (array)$children;
            foreach ($children as $child) {
                if (!in_array($child, $sortedRoles[$role], true)) {
                    $sortedRoles[$role][] = $child;
                }

                if (!isset($sortedRoles[$child])) {
                    $sortedRoles[$child] = [$child];
                }
            }
        }
        unset($children);

        do {
            $previousSortedRoles = $sortedRoles;
            foreach ($sortedRoles as $r_name => $r_children) {
                foreach ($r_children as $role) {
                    if ($r_name !== $role && isset($sortedRoles[$role])) {
                        foreach ($sortedRoles[$role] as $r) {
                            if (!in_array($r, $sortedRoles[$r_name], true)) {
                                $sortedRoles[$r_name][] = $r;
                            }
                        }
                    }
                }
            }
        } while ($previousSortedRoles !== $sortedRoles);

        return $sortedRoles;
    }
}
