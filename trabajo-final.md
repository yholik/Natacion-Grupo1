# 🏊‍♂️ Trabajo Final: Sistema de Gestión "Swimming Pool"

## 1. Introducción y Objetivo
El objetivo de este trabajo consiste en extender el núcleo del proyecto desarrollado en clase, incorporando nuevas funcionalidades y corrigiendo detalles técnicos pendientes. Se busca aplicar buenas prácticas de desarrollo, arquitectura **MVC** y modularización en **JavaScript**.

### Modalidad de Trabajo
* **Grupos:** Máximo 5 estudiantes.
* **Entregable:** Repositorio (GitHub/GitLab) público o privado (sumar al docente como colaborador).

---

## 2. Estándares de Desarrollo y Calidad
Para la aprobación, se evaluará estrictamente el cumplimiento de los siguientes puntos:

1.  **Versionado (Git):** * Uso de ramas o trabajo coordinado en un repositorio.
    * **Commits:** Mantener una convención clara (ej: `feat:`, `fix:`, `refactor:`). Se permite el mensaje en español.
2.  **Código Limpio (Clean Code):**
    * El código (variables, funciones, tablas, clases) debe estar en **Inglés**.
    * **Principio DRY (Don't Repeat Yourself):** Evitar la duplicación de lógica. Reutilizar servicios y componentes.
3.  **Seguridad:** * Validación de datos en Frontend (JS) y Backend (PHP).
    * Manejo correcto de sesiones y permisos por rol.

---

## 3. Requerimientos Técnicos (Ajustes de Base)

* **Refactorización del Registro de Swimmers:** * Agregar campo obligatorio "Confirmar Contraseña" con validación de coincidencia.
    * Agregar campo "Fecha de Nacimiento" (`birth_date`).
* **Punto de Entrada:** * Implementar una **Landing Page** institucional del club.
    * Contenido: Textos, fotos y estética a elección (creatividad del grupo).
    * Debe servir como portal de acceso a las funciones de Login y Registro.

---

## 4. Requerimientos Funcionales por Rol

### A. Usuario Administrador
* **Gestión de Staff:** Creación de perfiles para Profesores/Coaches.
* **Notificaciones:** Al dar de alta un profesor, el sistema debe enviarle su contraseña provisoria por email automáticamente.
* **Gestión de Clases:** Crear, editar y eliminar clases, definiendo: **Día, Horario y Profesor a cargo**.
* **Consulta General:** Acceso total para supervisar todos los datos del sistema.

### B. Usuario Profesor / Coach
* **Gestión de Perfil:** Posibilidad de modificar sus datos personales y actualizar su contraseña.
* **Listado de Alumnos:** Visualizar la lista de nadadores inscritos en sus clases específicas, filtradas por día y horario.

### C. Usuario Swimmer (Nadador)
* **Panel Personal:** Modificación de datos personales (teléfono, dirección, etc.).
* **Inscripción a Clases:** * Visualizar oferta de clases disponibles ordenadas por cronograma.
* Realizar la inscripción (debe figurar claramente el profesor responsable).

---

## 5. Análisis del Flujo de Datos Sugerido
Para asegurar la integridad del sistema, se recomienda trabajar sobre el siguiente esquema relacional:

* **Users ↔ Roles:** Relación para determinar permisos.
* **Classes ↔ Users (Coach):** Una clase tiene asignado un profesor.
* **Classes ↔ Users (Swimmers):** Relación de muchos a muchos (tabla de inscripciones/enrollments) para gestionar quién asiste a qué clase.

---
> **⚠️ IMPORTANTE:** Lean bien los requerimientos y consulten cualquier duda funcional antes de comenzar la implementación.