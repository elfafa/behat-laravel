<?php

namespace Behat\Repository;

class MemoryUserRepository implements UserRepositoryInterface
{
    private $users;

    public function findOneForType($type)
    {
        if (null === $this->users) {
            $this->initUsers();
        }

        if (false === array_key_exists($type, $this->users)) {
            throw new \LogicException(sprintf('"%s" user type is not defined for this Behat Context', $type));
        }

        return $this->users[$type];
    }

    private function initUsers()
    {
        $this->users = [
            'collaborator' => [
                'username' => 'XEONTE00',
                'token'    => 'p91cae422218c4f2142f89e285fa68680ed093be',
            ],
            'client' => [
                'username' => 'jpdurail.demo@gmail.com',
                'token'    => 'c91cae422218c4f2142f89e285fa68680ed093be',
            ],
            'admin' => [
                'username' => 'contact@esystema.fr',
                'token'    => '2bd38befc636d9cfa3f8f4ad634977ac8d4372d3',
            ],
        ];
    }
}
