# 📝 Próxima sesión — Agenda Pro PHP

---

## 🐛 Bugs a corregir (prioritarios)

| # | Dónde | Problema |
|---|---|---|
| 1 | `views/contacts/edit.php` | El campo **Descripción no existe** en el formulario de edición. Si editas un contacto, la descripción se borra en BD porque el campo no se envía. |
| 2 | `views/contacts/edit.php` | La vista comprueba `$errors` (array) pero el controlador pasa `$error` (string). Los errores de edición **nunca se muestran**. |
| 3 | `views/contacts/edit.php` | HTML roto: `<hr">` en lugar de `<hr>`. |
| 4 | `src/Models/Contact.php` | `findById()` accede a `$_SESSION['user_id']` directamente dentro del Modelo. El Modelo no debería conocer la sesión — el `$userId` debería llegar como parámetro desde el Controlador. |

---

## 🔒 Seguridad

| # | Problema | Notas |
|---|---|---|
| 5 | **Sin protección CSRF** | Todos los formularios (login, registro, crear/editar contacto) son vulnerables. Hay que generar un token en sesión y verificarlo en cada POST. Buen ejercicio de seguridad web. |
| 6 | **Login sin persistencia** | Si el login falla, el campo email se vacía y el usuario tiene que escribirlo de nuevo. Aplicar el mismo patrón `$_POST` que ya usamos en create/edit. |

---

## ✨ Mejoras de funcionalidad

| # | Idea | Notas |
|---|---|---|
| 7 | **Cambio de contraseña** | El perfil permite cambiar nombre y email, pero no la contraseña. Añadir sección con campos: contraseña actual, nueva contraseña, confirmar nueva. |
| 8 | **Ordenación de contactos** | Ahora siempre se ordenan por nombre A-Z. Permitir ordenar por fecha de creación o cambiar dirección (A-Z / Z-A) con un selector en la vista. |
| 9 | **Paginación: mostrar total** | Mostrar algo como "Mostrando 7-12 de 34 contactos" para dar contexto al usuario sobre cuántos resultados hay. |
