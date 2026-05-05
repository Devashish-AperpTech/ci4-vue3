<?php

namespace App\Services;

use App\Repositories\UserRepository;

class CustomerService extends BaseService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function listCustomers(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $search = trim((string) $search);

        $result = $this->userRepository->getPaginatedCustomers($page, $perPage, $search);
        $total = (int) ($result['total'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $perPage));

        return $this->respondWithSuccess('Customers loaded successfully', [
            'data' => $result['data'] ?? [],
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'hasPrev' => $page > 1,
                'hasNext' => $page < $totalPages,
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function createCustomer(array $data, int $currentUserId): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        //$password = $name.'@123';
        $vatCode = trim((string) ($data['vat_code'] ?? ''));

        if (strlen($name) < 2) {
            return $this->respondWithError('Name must be at least 2 characters');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondWithError('Valid email is required');
        }

        // if (strlen($password) < 4) {
        //     return $this->respondWithError('Password must be at least 4 characters');
        // }

        if ($this->userRepository->findByEmail($email)) {
            return $this->respondWithError('Email is already registered');
        }

        $customerId = $this->userRepository->createUser([
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_password' => $email,
            'customer_vat_code' => $vatCode !== '' ? $vatCode : null,
            'customer_identifier_code' => null,
            'parent_id' => $currentUserId,
        ]);

        if (!$customerId) {
            return $this->respondWithError('Failed to create customer');
        }

        return $this->respondWithSuccess('Customer created successfully', ['customer_id' => $customerId]);
    }

    public function updateCustomer(int $customerId, array $data): array
    {
        $existing = $this->userRepository->findById($customerId);
        if (!$existing) {
            return $this->respondWithError('Customer not found');
        }

        $updateData = [];

        if (array_key_exists('name', $data)) {
            $name = trim((string)$data['name']);

            if (strlen($name) < 2) {
                return $this->respondWithError('Name must be at least 2 characters');
            }

            if ($name !== $existing['customer_name']) {
                $updateData['customer_name'] = $name;
            }
        }

        if (array_key_exists('email', $data)) {
            $email = trim((string)$data['email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->respondWithError('Valid email is required');
            }

            $emailOwner = $this->userRepository->findByEmail($email);
            if ($emailOwner && (int)$emailOwner['customer_id'] !== $customerId) {
                return $this->respondWithError('Email is already registered');
            }

            if ($email !== $existing['customer_email']) {
                $updateData['customer_email'] = $email;
            }
        }

        if (array_key_exists('vat_code', $data)) {
            $vatCode = trim((string)$data['vat_code']);
            $vatCode = $vatCode !== '' ? $vatCode : null;

            if ($vatCode !== ($existing['customer_vat_code'] ?? null)) {
                $updateData['customer_vat_code'] = $vatCode;
            }
        }

        if (array_key_exists('customer_identifier_code', $data)) {
            $identifier = trim((string)$data['customer_identifier_code']);
            $identifier = $identifier !== '' ? $identifier : null;

            if ($identifier !== ($existing['customer_identifier_code'] ?? null)) {
                $updateData['customer_identifier_code'] = $identifier;
            }
        }

        if (!empty($data['password'])) {
            $password = (string)$data['password'];

            if (strlen($password) < 4) {
                return $this->respondWithError('Password must be at least 4 characters');
            }

            $updateData['customer_password'] = $password;
        }

        if (empty($updateData)) {
            return $this->respondWithSuccess('No changes detected');
        }

        // ---- Update ----
        $updated = $this->userRepository->updateUser($customerId, $updateData);

        if (!$updated) {
            return $this->respondWithError('Failed to update customer');
        }

        return $this->respondWithSuccess('Customer updated successfully');
    }

    public function deleteCustomer(int $customerId, int $currentUserId): array
    {
        if ($customerId === $currentUserId) {
            return $this->respondWithError('You cannot delete your own logged-in account');
        }

        $existing = $this->userRepository->findById($customerId);
        if (!$existing) {
            return $this->respondWithError('Customer not found');
        }

        $deleted = $this->userRepository->deleteUser($customerId);
        if (!$deleted) {
            return $this->respondWithError('Failed to delete customer');
        }

        return $this->respondWithSuccess('Customer deleted successfully');
    }
}
