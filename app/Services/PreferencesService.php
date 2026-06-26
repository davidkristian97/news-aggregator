<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PreferencesRepository;

class PreferencesService
{
    public function __construct(private readonly PreferencesRepository $repository) {}

    public function get(User $user): User
    {
        return $user->load('sources', 'categories', 'authors');
    }

    public function update(User $user, array $preferences): User
    {
        $this->repository->sync($user, $preferences);
        return $user->load('sources', 'categories', 'authors');
    }
}
