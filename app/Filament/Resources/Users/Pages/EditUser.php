<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Assign email_verified_at explicitly for privileged admin edits without exposing it in User::$fillable.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $hasEmailVerifiedAt = array_key_exists('email_verified_at', $data);
        $emailVerifiedAt = $data['email_verified_at'] ?? null;
        unset($data['email_verified_at']);

        $record->fill($data);

        if ($hasEmailVerifiedAt) {
            $record->email_verified_at = $emailVerifiedAt;
        }

        $record->save();

        return $record;
    }
}
