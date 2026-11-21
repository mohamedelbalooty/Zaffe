<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Assign email_verified_at explicitly for privileged admin creation without exposing it in User::$fillable.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $hasEmailVerifiedAt = array_key_exists('email_verified_at', $data);
        $emailVerifiedAt = $data['email_verified_at'] ?? null;
        unset($data['email_verified_at']);

        $record = new ($this->getModel())($data);

        if ($hasEmailVerifiedAt) {
            $record->email_verified_at = $emailVerifiedAt;
        }

        if ($parentRecord = $this->getParentRecord()) {
            return $this->associateRecordWithParent($record, $parentRecord);
        }

        $record->save();

        return $record;
    }
}
