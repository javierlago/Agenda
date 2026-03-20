<?php

namespace App\Controllers;

use App\Models\Contact;

/**
 * Class ContactController
 * Handles the business logic for managing the contacts of the users.
 */
class ContactController
{
    /** @var Contact $contactModel Instance of the Contact Model */
    private Contact $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
    }

    /**
     * Fetches all contacts for the current user.
     * Provides a fallback example if no contacts exist.
     * * @return array
     */
    public function index(): array
    {
        $contacts = $this->contactModel->getAllByUserId((int)$_SESSION['user_id']);

        if (empty($contacts)) {
            $contacts = [
                [
                    'name' => 'User Example Example',
                    'phone' => '999999999',
                    'email' => 'userexample@gmail.com',
                    'description' => 'This is an example contact',
                    'is_example' => true
                ]
            ];
        }
        return $contacts;
    }

    /**
     * Handles the creation of a new contact.
     * @param array $data Input data from the form.
     * @return array Status and potential errors.
     */
    public function store(array $data): array
    {
        if (empty(trim($data['name'] ?? ''))) {
            return ['success' => false, 'errors' => ['The name is required.']];
        }

        $data['user_id'] = $_SESSION['user_id'];
        $success = $this->contactModel->create($data);

        return $success 
            ? ['success' => true] 
            : ['success' => false, 'errors' => ['Error saving the contact.']];
    }

    /**
     * Fetches a single contact for editing.
     * @param int $id Contact ID.
     * @return array|null
     */
    public function show(int $id): ?array
    {
        return $this->contactModel->findById($id, (int)$_SESSION['user_id']);
    }

    /**
     * Handles the update of an existing contact.
     * @param int $id Contact ID.
     * @param array $data New data.
     * @return array
     */
    public function update(int $id, array $data): array
    {
        $data['user_id'] = $_SESSION['user_id'];
        $success = $this->contactModel->update($id, $data);

        return $success 
            ? ['success' => true] 
            : ['success' => false, 'errors' => ['Error updating the contact.']];
    }

    /**
     * Handles the deletion of a contact.
     * * @param int $id Contact ID.
     * @return bool
     */
    public function destroy(int $id): bool
    {
        return $this->contactModel->delete($id, (int)$_SESSION['user_id']);
    }
}