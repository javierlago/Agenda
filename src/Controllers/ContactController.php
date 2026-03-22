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
     * 
     * Displays the list of contacts for the logged-in user.
     * If there are no contacts, it can show an example contact.
     */
    public function index(): void
    {
        $contacts = $this->contactModel->getAllByUserId((int)$_SESSION['user_id']);

        // Si no hay contactos, el array de ejemplo lo manejamos aquí si queremos
        if (empty($contacts)) {
            $contacts = [['name' => 'Ejemplo', 'phone' => '000', 'email' => 'a@b.com', 'is_example' => true]];
        }

        // El controlador carga la vista
        require_once __DIR__ . '/../../views/contacts/index.php';
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

        // Cargamos la vista de creación
        require_once __DIR__ . '/../../views/contacts/create.php';
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
     * No recibe parámetros por firma para ser compatible con el Router.
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
        // Usamos el método show que ya tienes
        $contact = $this->show((int)$id);

        if (!$contact) {
            header("Location: index.php?action=home&error=notfound");
            exit;
        }

        // 3. Ejecutamos el borrado en el modelo
        // Importante: No retornamos nada, ejecutamos y redirigimos
        $result = $this->contactModel->delete((int)$id, (int)$_SESSION['user_id']);

        if ($result) {
            // ÉXITO: Volvemos al home con aviso de borrado
            header("Location: index.php?action=home&success=deleted");
        } else {
            // ERROR: Volvemos al home avisando que algo falló
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

            // Llamamos a tu método de actualización
            if ($this->update((int)$id, $data)['success']) {
                header("Location: index.php?action=home&success=updated");
                exit;
            } else {
                $error = "No se pudo actualizar el contacto.";
            }
        }

        require_once __DIR__ . '/../../views/contacts/edit.php';
    }
}
