<?php
// app/models/Admin.php

require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Coach.php';
require_once __DIR__ . '/../services/MailService.php';
require_once __DIR__ . '/../services/PasswordGeneratorService.php';

class Admin
{
    private $db;
    private $authModel;
    private $coachModel;
    private $mailService;
    private $passwordGenerator;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        $this->authModel = new Auth($pdo);
        $this->coachModel = new Coach($pdo);
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
                'specialty' => $data['specialty']
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
    
    public function deactivateCoach(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->coachModel->deactivateByUserId($userId);
    }

    public function activateCoach(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        return $this->coachModel->activateByUserId($userId);
    }

    public function updateCoach(int $user_id, array $data): bool
    {
        if ($user_id <= 0) {
            return false;
        }

        return $this->coachModel->updateCoach($user_id, $data);
    }
}