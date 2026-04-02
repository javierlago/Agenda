# 📝 Development Roadmap: Agenda Pro PHP

This document tracks the architectural progress and feature set of the Agenda (Contact Management) project.

---

## 🏗️ 1. Foundation & Security (Core)
| Task | Status | Priority | Notes |
| :--- | :---: | :---: | :--- |
| Full Authentication (Login/Logout/Register) | ✅ | High | System gatekeeper. |
| Route Protection (AuthHelper/Middleware) | ✅  | High | Prevent direct access to private actions. |
| Password Hashing (`password_hash`) | ✅ | Critical | Secure handling of sensitive credentials. |
| SQL Injection Prevention (PDO Prepared) | ✅ | Critical | Database shielding. |
| XSS Prevention (`htmlspecialchars`) | ✅ | High | Data sanitization before HTML rendering. |

---

## 👤 2. User Management (UserController)
| Task | Status | Priority | Notes |
| :--- | :---: | :---: | :--- |
| User Profile View (`getById`) | ✅ | Medium | Display logged-in user data. |
| Profile Editing (Update Name/Email) | ✅ | Medium | Allow users to modify their account info. |
| Robust Duplicate Validation | ✅ | High | Specific error handling for Email vs Username. |
| Logging System (`Utils\Logger`) | ✅ | Medium | Error and access traceability in `/Logs`. |

---

## 📇 3. Contact Management (ContactController)
| Task | Status | Priority | Notes |
| :--- | :---: | :---: | :--- |
| Contact CRUD (Create, Read, Update, Delete) | ✅ | High | Core application functionality. |
| Data Ownership Filter (`user_id`) | ✅ | Critical | Users MUST NOT see/edit others' contacts. |
| Result Pagination | [ ] | Medium | Efficiently handle large datasets (50+ records). |
| Search Functionality | [ ] | Low | Filter contacts by name or email. |

---

## 🛠️ 4. Architecture & Clean Code
| Task | Status | Priority | Notes |
| :--- | :---: | :---: | :--- |
| PSR-4 Autoloading (Composer) | ✅ | High | Standardized class loading (no manual requires). |
| Front Controller (Router index.php) | ✅ | High | Single entry point pattern. |
| Strict MVC Separation | ✅ | High | Model (SQL), View (HTML), Controller (Logic). |
| Global Exception Handling | ✅ | Medium | Structured `try-catch` blocks between layers. |

---

## 🎨 5. User Experience (UI/UX)
| Task | Status | Priority | Notes |
| :--- | :---: | :---: | :--- |
| Operation Feedback (Alerts) | [ ] | Medium | Success/Error flash messages (Toast/Alerts). |
| Form Persistence | [ ] | Medium | Keep input values after validation errors. |
| Layout Inheritance (Header/Footer) | [ ] | High | Avoid HTML code duplication across views. |

---

## 🚀 Next Steps
1. Refactor the Register logic with `try-catch` to handle "User already exists" gracefully.
2. Implement the `update()` method in `UserModel` for profile management.
3. Build the Pagination logic to handle the 50 injected test contacts.