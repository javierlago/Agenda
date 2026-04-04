<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Utils\AuthHelper;
use App\Utils\Csrf;
use App\Utils\View;

/**
 * Handles all contact management actions: listing, creation, editing, and deletion.
 * All methods require an authenticated session; authentication is enforced either
 * via the route map or by an explicit AuthHelper::verifyLogin() call.
 */
class ContactController
{
    private Contact $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
    }

    /**
     * Displays a paginated, searchable, and sortable list of the logged-in user's contacts.
     *
     * Reads 'search', 'sort', and 'page' from the query string. The sort parameter is
     * validated against a whitelist before being passed to the model. Passes pagination
     * metadata and the total contact count to the view for display.
     *
     * @return void
     */
    public function index(): void
    {
        AuthHelper::verifyLogin();
        $userId = $_SESSION['user_id'];

        $search = trim($_GET['search'] ?? '');

        $allowedSorts = ['name_asc', 'name_desc', 'date_asc', 'date_desc'];
        $sort = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'name_asc';

        $limit = 6;
        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $contacts      = $this->contactModel->getPaginated($userId, $limit, $offset, $search, $sort);
        $totalContacts = $this->contactModel->getTotalCount($userId, $search);
        $totalPages    = (int) ceil($totalContacts / $limit);

        View::render('contacts/index', [
            'pageTitle'     => 'Mis Contactos',
            'contacts'      => $contacts,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'totalContacts' => $totalContacts,
            'limit'         => $limit,
            'offset'        => $offset,
            'search'        => $search,
            'sort'          => $sort,
        ]);
    }

    /**
     * Displays the new contact form (GET) and saves the contact on submission (POST).
     *
     * Validates the CSRF token and requires at least the contact's name to be present.
     * On success, redirects to the home page with a success flash message.
     *
     * @return void
     */
    public function create(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $error = "Token de seguridad inválido. Intenta de nuevo.";
            } else {
                $data = $_POST;
                $data['user_id'] = $_SESSION['user_id'];

                if ($this->contactModel->create($data)) {
                    header("Location: index.php?action=home&success=1");
                    exit;
                } else {
                    $error = "No se pudo guardar el contacto.";
                }
            }
        }

        View::render('contacts/create', [
            'pageTitle' => 'Nuevo Contacto - Agenda Pro',
            'error'     => $error,
            'csrfToken' => Csrf::generateToken(),
        ]);
    }

    /**
     * Displays the edit form for an existing contact (GET) and saves changes (POST).
     *
     * Reads the contact ID from the query string and verifies ownership before rendering.
     * Validates the CSRF token on POST. On success, redirects to the home page.
     * Redirects to home with an error flag if the contact is not found or not owned
     * by the current user.
     *
     * @return void
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?action=home");
            exit;
        }

        $contact = $this->contactModel->findById((int) $id, (int) $_SESSION['user_id']);

        if (!$contact) {
            header("Location: index.php?action=home&error=notfound");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $error = "Token de seguridad inválido. Intenta de nuevo.";
            } else {
                $data = $_POST;
                $data['user_id'] = $_SESSION['user_id'];

                if ($this->contactModel->update((int) $id, $data)) {
                    header("Location: index.php?action=home&success=updated");
                    exit;
                } else {
                    $error = "No se pudo actualizar el contacto.";
                }
            }
        }

        View::render('contacts/edit', [
            'pageTitle' => 'Edición de contacto - Agenda Pro',
            'contact'   => $contact,
            'error'     => $error,
            'csrfToken' => Csrf::generateToken(),
        ]);
    }

    /**
     * Deletes a contact owned by the logged-in user.
     *
     * Reads the contact ID from the query string. Verifies ownership before deleting.
     * Redirects to the home page with an appropriate success or error flash message.
     *
     * @return void
     */
    public function destroy(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?action=home");
            exit;
        }

        $contact = $this->contactModel->findById((int) $id, (int) $_SESSION['user_id']);

        if (!$contact) {
            header("Location: index.php?action=home&error=notfound");
            exit;
        }

        $result = $this->contactModel->delete((int) $id, (int) $_SESSION['user_id']);

        header($result
            ? "Location: index.php?action=home&success=deleted"
            : "Location: index.php?action=home&error=deletefailed"
        );
        exit;
    }
}
