<?php

namespace App\Domain\Services;

use App\Domain\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        try {
            $user = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'nama_pegawai' => $data['nama_pegawai'],
                'role' => $data['role'],
            ]);

            Log::info('User created', ['user_id' => $user->id, 'username' => $user->username]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error creating user', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): User
    {
        try {
            $updateData = [
                'username' => $data['username'],
                'nama_pegawai' => $data['nama_pegawai'],
                'role' => $data['role'],
            ];

            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            Log::info('User updated', ['user_id' => $user->id, 'username' => $user->username]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error updating user', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            throw $e;
        }
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user): bool
    {
        try {
            $username = $user->username;
            $id = $user->id;

            $result = (bool) $user->delete();

            Log::info('User deleted', ['user_id' => $id, 'username' => $username]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            throw $e;
        }
    }
}
