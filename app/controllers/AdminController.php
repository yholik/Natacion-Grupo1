<?php
// app/controllers/AdminController.php

// Maneja el panel de administración.

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Coach.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Swimmer.php';

class AdminController extends BaseController
{
    private $adminModel;
    private $coachModel;
    private $swimmerModel;
    private $lessonModel;

    // Carga los modelos que usa el admin.
    public function __construct()
    {
        global $pdo;

        $this->adminModel = new Admin($pdo);
        $this->coachModel = new Coach($pdo);
        $this->swimmerModel = new Swimmer($pdo);
        $this->lessonModel = new Lesson($pdo);
    }

    // El home admin quedó absorbido por profesores.
    public function showAdminHome()
    {
        $this->checkAuth();
        $this->checkRole(1);

        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-coaches');
        exit;
    }

    // Lista profesores activos y dados de baja, con búsqueda por nombre.
    public function showAdminManageCoaches()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $searchTerm = trim($_GET['search'] ?? '');
        $coaches = $searchTerm !== ''
            ? $this->coachModel->search($searchTerm, false)
            : $this->coachModel->getAll(false);

        $this->render('users/admin/admin-manage-coaches.view', [
            'title' => 'Gestionar Profesores',
            'coaches' => $coaches,
            'searchTerm' => $searchTerm
        ]);
    }

    // Abre el formulario de alta de profesores.
    public function showCreateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $specialties = $this->coachModel->getAllSpecialties();

        $this->render('users/admin/admin-create-coach.view', [
            'title' => 'Agregar Profesor',
            'specialties' => $specialties
        ]);
    }

    /**
     * Crea un profesor desde el panel administrador.
     *
     * Si la petición no es POST, muestra el formulario.
     * Si es POST, valida los datos y delega la creación al modelo Admin.
     */
    // Procesa el alta de un profesor.
    public function createCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateCoach();
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'specialty_ids'  => array_map('intval', $_POST['specialty_ids'] ?? [])
        ];

        if ($this->hasEmptyCoachFields($fields)) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        $created = $this->adminModel->createCoach($fields);

        if (!$created) {
            return $this->json(
                'error',
                'No se pudo crear el profesor. Verificá que el email no esté registrado y que el correo funcione.'
            );
        }

        return $this->json(
            'success',
            'Profesor creado correctamente. Se enviaron las credenciales por email.',
            Env::get('APP_URL') . '/?url=admin-manage-coaches'
        );
    }

    // Marca los obligatorios del formulario coach.
    private function hasEmptyCoachFields(array $fields)
    {
        return empty($fields['first_name'])
            || empty($fields['last_name'])
            || empty($fields['email'])
            || empty($fields['phone'])
            || empty($fields['specialty_ids']);
    }

    // Da de baja lógica a un profesor.
    public function deactivateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageCoaches();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageCoaches();
        }

        $profileId = $this->coachModel->getCoachProfileIdByUserId($userId);
        if ($profileId > 0) {
            $activeLessons = $this->lessonModel->countActiveLessonsByCoach($profileId);
            if ($activeLessons > 0) {
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

                if ($isAjax) {
                    return $this->json('error', 'No se puede dar de baja al profesor porque tiene ' . $activeLessons . ' clase(s) con alumnos inscriptos. Primero eliminá esas clases o cambia de profesor.');
                }

                return $this->redirectToManageCoaches();
            }
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        $this->adminModel->deactivateCoach($userId);

        if ($isAjax) {
            return $this->json('success', 'Profesor dado de baja correctamente.', Env::get('APP_URL') . '/?url=admin-manage-coaches');
        }

        return $this->redirectToManageCoaches();
    }

    // Revierte una baja de profesor.
    public function activateCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageCoaches();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageCoaches();
        }

        $this->adminModel->activateCoach($userId);

        return $this->redirectToManageCoaches();
    }

    // Vuelve al listado de profesores.
    private function redirectToManageCoaches()
    {
        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-coaches');
        exit;
    }

    // Muestra o guarda la edición de un profesor.
    public function editCoach()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $userId = (int) ($_POST['user_id'] ?? $_GET['id'] ?? 0);

        if ($userId <= 0) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->json('error', 'ID de profesor inválido.');
            }

            return $this->redirectToManageCoaches();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $coach = $this->coachModel->getCoachById($userId);

            if (!$coach) {
                return $this->redirectToManageCoaches();
            }

            return $this->render('users/admin/admin-create-coach.view', [
                'title' => 'Editar Profesor',
                'coach' => $coach,
                'specialties' => $this->coachModel->getAllSpecialties()
            ]);
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'specialty_ids'  => array_map('intval', $_POST['specialty_ids'] ?? [])
        ];

        if (
            empty($fields['first_name']) ||
            empty($fields['last_name']) ||
            empty($fields['phone']) ||
            empty($fields['specialty_ids'])
        ) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        $profileId = $this->coachModel->getCoachProfileIdByUserId($userId);
        if ($profileId > 0) {
            $currentSpecialties = $this->coachModel->getSpecialtiesByProfileId($profileId);
            $currentIds = array_column($currentSpecialties, 'id');
            $newIds = $fields['specialty_ids'];
            $removedIds = array_diff($currentIds, $newIds);

            foreach ($removedIds as $specialtyId) {
                $lessonsCount = $this->lessonModel->countLessonsByCoachAndSpecialty($profileId, $specialtyId);
                if ($lessonsCount > 0) {
                    $specialtyName = '';
                    foreach ($currentSpecialties as $s) {
                        if ((int) $s['id'] === (int) $specialtyId) {
                            $specialtyName = $s['name'];
                            break;
                        }
                    }
                    return $this->json('error', 'No se puede desvincular la especialidad "' . $specialtyName . '" porque el profesor tiene ' . $lessonsCount . ' clase(s) activa(s) con esa especialidad. Primero eliminá o cambia de especialidad esas clases.');
                }
            }
        }

        $updated = $this->adminModel->updateCoach($userId, $fields);

        if (!$updated) {
            return $this->json('error', 'No se pudo actualizar el profesor.');
        }

        return $this->json(
            'success',
            'Profesor actualizado correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-coaches'
        );
    }

    // Carga el tablero de clases.
    public function showAdminManageLessons()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $lessons = $this->lessonModel->getAllWithCoach();

        // Trae profesores activos para cargarlos en el combo del modal.
        $coaches = $this->coachModel->getAll(true);

        $this->render('users/admin/admin-manage-lessons.view', [
            'title' => 'Gestionar Clases',
            'lessons' => $lessons,
            'coaches' => $coaches,
            'levels' => $this->lessonModel->getAllLevels(),
            'specialties' => $this->lessonModel->getAllSpecialties()
        ]);
    }

    // Devuelve las especialidades de un profesor por su perfil.id (JSON).
    public function getCoachSpecialties()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $profileId = (int) ($_GET['coach_id'] ?? 0);
        if ($profileId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'data' => ['specialties' => []]]);
            exit;
        }

        $specialties = $this->coachModel->getSpecialtiesByProfileId($profileId);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => ['specialties' => $specialties]]);
        exit;
    }

    // Crea una clase desde el panel admin.
    public function createLesson()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Metodo no permitido.');
        }

        $data = $this->getLessonPayload();

        if ($data === null) {
            return $this->json('warning', 'Todos los campos son obligatorios y la capacidad debe ser mayor a 0.');
        }

        if ($data['start_time'] >= $data['end_time']) {
            return $this->json('warning', 'El horario de fin debe ser posterior al de inicio.');
        }

        if (!$this->lessonModel->create($data)) {
            return $this->json('error', 'Ya existe una clase en ese horario. No se permite la superposicion.');
        }

        return $this->json(
            'success',
            'Clase creada correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-lessons'
        );
    }

    // Guarda los cambios de una clase existente.
    public function editLesson()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json('error', 'Metodo no permitido.');
        }

        $lessonId = (int) ($_POST['lesson_id'] ?? 0);
        if ($lessonId <= 0) {
            return $this->json('error', 'ID de clase invalido.');
        }

        $current = $this->lessonModel->getById($lessonId);
        if (!$current) {
            return $this->json('error', 'La clase no existe.');
        }

        $data = $this->getLessonPayload();

        if ($data === null) {
            return $this->json('warning', 'Todos los campos son obligatorios y la capacidad debe ser mayor a 0.');
        }

        if ($data['start_time'] >= $data['end_time']) {
            return $this->json('warning', 'El horario de fin debe ser posterior al de inicio.');
        }

        $enrolled = $this->lessonModel->countEnrolled($lessonId);
        if ($enrolled > 0) {
            if ((int) $data['specialty_id'] !== (int) $current['specialty_id']) {
                return $this->json('error', 'No se puede cambiar la especialidad porque la clase tiene ' . $enrolled . ' alumno(s) inscripto(s). Primero desinscribilos.');
            }
            if ((int) $data['level_id'] !== (int) $current['level_id']) {
                return $this->json('error', 'No se puede cambiar el nivel porque la clase tiene ' . $enrolled . ' alumno(s) inscripto(s). Primero desinscribilos.');
            }
        }

        if (!$this->lessonModel->update($lessonId, $data)) {
            return $this->json('error', 'No se pudo actualizar la clase porque se superpone con otra existente.');
        }

        return $this->json(
            'success',
            'Clase actualizada correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-lessons'
        );
    }

    // Borra una clase del listado admin.
    public function deleteLesson()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageLessons();
        }

        $lessonId = (int) ($_POST['lesson_id'] ?? 0);
        if ($lessonId <= 0) {
            return $this->respondLessonDeletion('error', 'ID de clase invalido.');
        }

        $enrolled = $this->lessonModel->countEnrolled($lessonId);
        if ($enrolled > 0) {
            return $this->respondLessonDeletion(
                'warning',
                "No se puede eliminar la clase porque tiene {$enrolled} alumno(s) inscripto(s) actualmente. Desinscribí a los nadadores primero."
            );
        }

        $this->lessonModel->deleteBookingsByLesson($lessonId);

        if (!$this->lessonModel->delete($lessonId)) {
            return $this->respondLessonDeletion('error', 'No se pudo eliminar la clase.');
        }

        return $this->respondLessonDeletion('success', 'Clase eliminada correctamente.');
    }

    // Vuelve al calendario de clases del admin.
    private function redirectToManageLessons()
    {
        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-lessons');
        exit;
    }

    // Reune y normaliza los datos del formulario de clases.
    private function getLessonPayload(): ?array
    {
        $data = [
            'coach_id'    => (int) ($_POST['coach_id'] ?? 0),
            'specialty_id'=> (int) ($_POST['specialty_id'] ?? 0),
            'level_id'    => (int) ($_POST['level_id'] ?? 0),
            'day_of_week' => trim($_POST['day_of_week'] ?? ''),
            'start_time'  => trim($_POST['start_time'] ?? ''),
            'end_time'    => trim($_POST['end_time'] ?? ''),
            'capacity'    => (int) ($_POST['capacity'] ?? 0)
        ];

        if (
            $data['coach_id'] <= 0 ||
            $data['specialty_id'] <= 0 ||
            $data['level_id'] <= 0 ||
            $data['day_of_week'] === '' ||
            $data['start_time'] === '' ||
            $data['end_time'] === '' ||
            $data['capacity'] < 1
        ) {
            return null;
        }

        return $data;
    }

    // Responde segun si la baja vino por fetch o por form clasico.
    private function respondLessonDeletion(string $status, string $message)
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            return $this->json($status, $message, Env::get('APP_URL') . '/?url=admin-manage-lessons');
        }

        return $this->redirectToManageLessons();
    }

    // Lista nadadores activos y dados de baja, con búsqueda por nombre.
    public function showAdminManageSwimmers()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $searchTerm = trim($_GET['search'] ?? '');
        $swimmers = $searchTerm !== ''
            ? $this->swimmerModel->search($searchTerm, false)
            : $this->swimmerModel->getAll(false);

        $this->render('users/admin/admin-manage-swimmers.view', [
            'title' => 'Gestionar Nadadores',
            'swimmers' => $swimmers,
            'searchTerm' => $searchTerm
        ]);
    }

    // Abre el formulario de alta de nadadores.
    public function showCreateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $this->render('users/admin/admin-create-swimmer.view', [
            'title' => 'Agregar Nadador'
        ]);
    }

    // Procesa el alta de un nadador.
    public function createSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->showCreateSwimmer();
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'profile_image' => 'default-profile.png'
        ];

        if ($this->hasEmptySwimmerFields($fields)) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json('error', 'El email ingresado no es válido.');
        }

        $uploadedFile = $this->handleSwimmerProfileImageUpload($fields);

        if ($uploadedFile === false) {
            return;
        }

        $created = $this->adminModel->createSwimmer($fields);

        if (!$created) {
            if ($uploadedFile && file_exists($uploadedFile)) {
                unlink($uploadedFile);
            }

            return $this->json(
                'error',
                'No se pudo crear el nadador. Verificá que el email no esté registrado y que el correo funcione.'
            );
        }

        return $this->json(
            'success',
            'Nadador creado correctamente. Se enviaron las credenciales por email.',
            Env::get('APP_URL') . '/?url=admin-manage-swimmers'
        );
    }

    // Marca los obligatorios del formulario swimmer.
    private function hasEmptySwimmerFields(array $fields)
    {
        return empty($fields['first_name'])
            || empty($fields['last_name'])
            || empty($fields['email']);
    }

    // Da de baja lógica a un nadador.
    public function deactivateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSwimmers();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageSwimmers();
        }

        $this->adminModel->deactivateSwimmer($userId);

        return $this->redirectToManageSwimmers();
    }

    // Revierte una baja de nadador.
    public function activateSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirectToManageSwimmers();
        }

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->redirectToManageSwimmers();
        }

        $this->adminModel->activateSwimmer($userId);

        return $this->redirectToManageSwimmers();
    }

    // Vuelve al listado de nadadores.
    private function redirectToManageSwimmers()
    {
        header('Location: ' . Env::get('APP_URL') . '/?url=admin-manage-swimmers');
        exit;
    }

    // Muestra o guarda la edición de un nadador.
    public function editSwimmer()
    {
        $this->checkAuth();
        $this->checkRole(1);

        $userId = (int) ($_POST['user_id'] ?? $_GET['id'] ?? 0);

        if ($userId <= 0) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                return $this->json('error', 'ID de nadador inválido.');
            }

            return $this->redirectToManageSwimmers();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $swimmer = $this->swimmerModel->getSwimmerByUserId($userId, false);

            if (!$swimmer) {
                return $this->redirectToManageSwimmers();
            }

            return $this->render('users/admin/admin-create-swimmer.view', [
                'title' => 'Editar Nadador',
                'swimmer' => $swimmer
            ]);
        }

        $fields = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null
        ];

        if (empty($fields['first_name']) || empty($fields['last_name'])) {
            return $this->json('warning', 'Faltan datos obligatorios.');
        }

        $uploadedFile = $this->handleSwimmerProfileImageUpload($fields);

        if ($uploadedFile === false) {
            return;
        }

        $updated = $this->adminModel->updateSwimmer($userId, $fields);

        if (!$updated) {
            if ($uploadedFile && file_exists($uploadedFile)) {
                unlink($uploadedFile);
            }

            return $this->json('error', 'No se pudo actualizar el nadador.');
        }

        return $this->json(
            'success',
            'Nadador actualizado correctamente.',
            Env::get('APP_URL') . '/?url=admin-manage-swimmers'
        );
    }

    // Normaliza y guarda la foto subida desde admin.
    private function handleSwimmerProfileImageUpload(array &$fields)
    {
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/img/uploads/profiles/swimmers/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowed, true)) {
            $this->json('warning', 'Formato de imagen no válido (jpg, jpeg, png, gif).');
            return false;
        }

        $initial = strtolower(substr($fields['first_name'], 0, 1));
        $lastName = strtolower(str_replace(' ', '', $fields['last_name']));
        $randomNumber = rand(1000, 9999);
        $newFileName = 'swimmer_' . $initial . $lastName . '_' . $randomNumber . '.' . $extension;
        $absolutePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $absolutePath)) {
            $this->json('error', 'No se pudo guardar la imagen de perfil.');
            return false;
        }

        $fields['profile_image'] = $newFileName;
        $_SESSION['profile_image'] = $newFileName;

        return $absolutePath;
    }
}
