<?php
// app/models/Admin.php

// Encapsula las altas y cambios que hace el admin sobre otros usuarios.

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Booking.php';
require_once __DIR__ . '/Coach.php';
require_once __DIR__ . '/Swimmer.php';
require_once __DIR__ . '/../services/MailService.php';
require_once __DIR__ . '/../services/PasswordGeneratorService.php';

class Admin
{
    private $db;
    private $authModel;
    private $bookingModel;
    private $coachModel;
    private $swimmerModel;
    private $mailService;
    private $passwordGenerator;

    // Prepara servicios y modelos usados por el admin.
    public function __construct($pdo)
    {
        $this->db = $pdo;
        $this->authModel = new Auth($pdo);
        $this->bookingModel = new Booking($pdo);
        $this->coachModel = new Coach($pdo);
        $this->swimmerModel = new Swimmer($pdo);
        $this->mailService = new MailService();
        $this->passwordGenerator = new PasswordGeneratorService();
    }

    /**
     * Crea una cuenta de profesor desde el panel administrador.
     */
    public function createCoach(array $data)
    {
        try {
            if ($this->authModel->findByEmail($data['email'])) {
                return false;
            }

            $this->db->beginTransaction();

            $tempPassword = $this->passwordGenerator->generate(12);

            $userId = $this->authModel->create([
                'email' => $data['email'],
                'password' => $tempPassword,
                'role_id' => 2
            ]);

            if (!$userId) {
                throw new Exception('No se pudo crear el usuario.');
            }

            $coachCreated = $this->coachModel->createCoach([
                'user_id' => $userId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'specialty_ids' => $data['specialty_ids']
            ]);

            if (!$coachCreated) {
                throw new Exception('No se pudo crear el perfil del profesor.');
            }

            $coachName = trim($data['first_name'] . ' ' . $data['last_name']);

            $emailSent = $this->mailService->sendWelcomeCoach(
                $data['email'],
                $tempPassword,
                $coachName
            );

            if (!$emailSent) {
                throw new Exception('No se pudo enviar el correo.');
            }

            $this->db->commit();

            return $userId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }
    
    // Da de baja lógica a un coach.
    public function deactivateCoach(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->coachModel->deactivateByUserId($userId);
    }

    // Reactiva un coach dado de baja.
    public function activateCoach(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->coachModel->activateByUserId($userId);
    }

    // Guarda cambios del perfil coach desde admin.
    public function updateCoach(int $user_id, array $data): bool
    {
        if ($user_id <= 0) {
            return false;
        }

        return $this->coachModel->updateCoach($user_id, $data);
    }

    // Crea auth + perfil para un nadador.
    public function createSwimmer(array $data)
    {
        try {
            if ($this->authModel->findByEmail($data['email'])) {
                return false;
            }

            $this->db->beginTransaction();

            $tempPassword = $this->passwordGenerator->generate(12);

            $userId = $this->authModel->create([
                'email' => $data['email'],
                'password' => $tempPassword,
                'role_id' => 3
            ]);

            if (!$userId) {
                throw new Exception('No se pudo crear el usuario.');
            }

            $swimmerCreated = $this->swimmerModel->create([
                'user_id' => $userId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'] ?? null,
                'profile_image' => $data['profile_image'] ?? 'default-profile.png'
            ]);

            if (!$swimmerCreated) {
                throw new Exception('No se pudo crear el perfil del nadador.');
            }

            $swimmerName = trim($data['first_name'] . ' ' . $data['last_name']);

            $emailSent = $this->mailService->sendWelcomeSwimmer(
                $data['email'],
                $tempPassword,
                $swimmerName
            );

            if (!$emailSent) {
                throw new Exception('No se pudo enviar el correo.');
            }

            $this->db->commit();

            return $userId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;
        }
    }

    // Da de baja lógica a un nadador y cancela sus inscripciones activas.
    public function deactivateSwimmer(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $this->bookingModel->cancelAllByUserId($userId);

        return $this->swimmerModel->deactivateByUserId($userId);
    }

    // Reactiva un nadador dado de baja.
    public function activateSwimmer(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->swimmerModel->activateByUserId($userId);
    }

    // Guarda cambios del perfil swimmer desde admin.
    public function updateSwimmer(int $userId, array $data): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->swimmerModel->updateAdminSwimmer($userId, $data);
    }
}
