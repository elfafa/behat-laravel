<?php

namespace Behat\Repository;

interface UserRepositoryInterface
{
    const TYPE_COLLABORATOR = 'collaborator';
    const TYPE_CLIENT = 'client';
    const TYPE_ADMIN = 'admin';

    public function findOneForType($type);
}
