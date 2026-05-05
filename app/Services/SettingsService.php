<?php

namespace App\Services;

use App\Repositories\UserRepository;

class SettingsService extends BaseService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getProfile(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return $this->respondWithError('User not found');
        }

        return $this->respondWithSuccess('Profile loaded successfully', [
            'data' => [
                'customer_id' => $user['customer_id'],
                'customer_name' => $user['customer_name'],
                'customer_email' => $user['customer_email'],
                'customer_vat_code' => $user['customer_vat_code'] ?? null,
                'customer_identifier_code' => $user['customer_identifier_code'] ?? null,
                'parent_id' => $user['parent_id'] ?? null,
            ],
        ]);
    }

    public function updateProfile(int $userId, array $data): array
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return $this->respondWithError('User not found');
        }

        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $vatCode = trim((string) ($data['vat_code'] ?? ''));

        if (strlen($name) < 2) {
            return $this->respondWithError('Name must be at least 2 characters');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondWithError('Valid email is required');
        }

        $emailOwner = $this->userRepository->findByEmail($email);
        if ($emailOwner && (int) $emailOwner['customer_id'] !== $userId) {
            return $this->respondWithError('Email is already registered');
        }

        $updated = $this->userRepository->updateUser($userId, [
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_vat_code' => $vatCode !== '' ? $vatCode : null,
        ]);

        if (!$updated) {
            return $this->respondWithError('Failed to update profile');
        }

        return $this->respondWithSuccess('Profile updated successfully');
    }

    public function updatePassword(int $userId, array $data): array
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return $this->respondWithError('User not found');
        }

        $currentPassword = (string) ($data['current_password'] ?? '');
        $newPassword = (string) ($data['new_password'] ?? '');
        $confirmPassword = (string) ($data['confirm_password'] ?? '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            return $this->respondWithError('All password fields are required');
        }

        if (!$this->userRepository->verifyPassword($currentPassword, $user['customer_password'])) {
            return $this->respondWithError('Current password is incorrect');
        }

        if (strlen($newPassword) < 4) {
            return $this->respondWithError('New password must be at least 4 characters');
        }

        if ($newPassword !== $confirmPassword) {
            return $this->respondWithError('New password and confirm password do not match');
        }

        if ($currentPassword === $newPassword) {
            return $this->respondWithError('New password must be different from current password');
        }

        $updated = $this->userRepository->updateUser($userId, [
            'customer_password' => $newPassword,
        ]);

        if (!$updated) {
            return $this->respondWithError('Failed to update password');
        }

        return $this->respondWithSuccess('Password updated successfully');
    }
}

