<?php

namespace App\Repositories;

use App\Models\User;

class PreferencesRepository
{
    public function sync(User $user, array $preferences): void
    {
        $user->sources()->sync($preferences['source_ids'] ?? []);
        $user->categories()->sync($preferences['category_ids'] ?? []);
        $user->authors()->sync($preferences['author_ids'] ?? []);
    }
}
