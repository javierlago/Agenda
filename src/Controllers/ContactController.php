<?php

namespace App\Controllers;


use App\Models\Contact;
use App\Utils\AuthHelper;
use App\Utils\View;
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
     * Method to handle the display of the contact creation form.
     * 
     * 
     */
    public function create(): void
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['user_id'] = $_SESSION['user_id'];

            if ($this->contactModel->create($data)) {
                header("Location: index.php?action=home&success=1");
                exit;
            } else {
                $error = "No se pudo guardar el contacto.";
            }
        }

        View::render('contacts/create', [
            'pageTitle' => 'Nuevo Contacto - Agenda Pro',
            'error'     => $error,
        ]);
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
     * No recibe par�metros por firma para ser compatible con el Router.
     */
    public function destroy(): void
    {
        // 1. Extraemos el ID de la URL
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?action=home");
            exit;
        }

        // 2. Verificamos que el contacto existe y pertenece al usuario (Seguridad)
        // Usamos el m�todo show que ya tienes
        $contact = $this->show((int)$id);

        if (!$contact) {
            header("Location: index.php?action=home&error=notfound");
            exit;
        }

        // 3. Ejecutamos el borrado en el modelo
        // Importante: No retornamos nada, ejecutamos y redirigimos
        $result = $this->contactModel->delete((int)$id, (int)$_SESSION['user_id']);

        if ($result) {
            // �XITO: Volvemos al home con aviso de borrado
            header("Location: index.php?action=home&success=deleted");
        } else {
            // ERROR: Volvemos al home avisando que algo fall�
            header("Location: index.php?action=home&error=deletefailed");
        }

        exit;
    }
    public function edit(): void
    {

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?action=home");
            exit;
        }

        $contact = $this->show((int)$id);

        if (!$contact) {
            header("Location: index.php?action=home&error=notfound");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['user_id'] = $_SESSION['user_id'];

            // Llamamos a tu m�todo de actualizaci�n
            if ($this->update((int)$id, $data)['success']) {
                header("Location: index.php?action=home&success=updated");
                exit;
            } else {
                $error = "No se pudo actualizar el contacto.";
            }
        }

        View::render('contacts/edit', [
            'pageTitle' => 'Edición de contacto - Agenda Pro',
            'contact'   => $contact,
            'error'     => $error,
        ]);
    }
    /**
     * Displays the list of contacts for the logged-in user with pagination.
     * 
     */
    public function index(): void
    {
        AuthHelper::verifyLogin();
        $userId = $_SESSION['user_id'];

        // 1. Término de búsqueda (vacío si no se busca nada)
        $search = trim($_GET['search'] ?? '');

        // 2. Configuración de paginación
        $limit = 6;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // 3. Obtener datos filtrando por búsqueda si existe
        $contacts = $this->contactModel->getPaginated($userId, $limit, $offset, $search);
        $totalContacts = $this->contactModel->getTotalCount($userId, $search);
        $totalPages = ceil($totalContacts / $limit);

        View::render('contacts/index', [
            'pageTitle'  => 'Mis Contactos',
            'contacts'   => $contacts,
            'page'       => $page,
            'totalPages' => $totalPages,
            'search'     => $search,
        ]);
    }
}
