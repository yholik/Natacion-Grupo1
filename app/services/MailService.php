<?php
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
 {
    public function sendEmailResetPassword( $toEmail, $token )
    {
        $colorPrincipal = '#007bff';
        $colorFondo = '#f4f7f9';

        $mail = new PHPMailer( true );
    
        try {
            $mail->isSMTP();
            $mail->Host       = Env::get( 'MAIL_HOST' );
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get( 'MAIL_USERNAME' );
            $mail->Password   = Env::get( 'MAIL_PASSWORD' );
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = Env::get( 'MAIL_PORT' );

            $mail->setFrom( Env::get( 'MAIL_FROM' ), 'Soporte Escuela de Natación' );
            $mail->addAddress( $toEmail );

            $mail->isHTML( true );
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperación de contraseña';

            $baseUrl = Env::get( 'APP_URL' );
            $resetLink = rtrim( $baseUrl, '/' ) . '/index.php?url=reset-password&token=' . $token;

            // Armamos el Body con un formato más robusto
            $mail->Body = "
                        <div style='background-color: {$colorFondo}; padding: 40px; font-family: Arial, sans-serif; line-height: 1.6;'>
                            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e1e8ed;'>
                                
                                <div style='background-color: {$colorPrincipal}; padding: 20px; text-align: center;'>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Escuela de Natación</h1>
                                </div>

                                <div style='padding: 30px; text-align: center;'>
                                    <h2 style='color: #333333;'>¿Olvidaste tu contraseña?</h2>
                                    <p style='color: #666666; font-size: 16px;'>
                                        No te preocupes, nos pasa a todos. Haz clic en el botón de abajo para elegir una nueva clave y volver al agua.
                                    </p>
                                    
                                    <div style='margin: 30px 0;'>
                                        <a href='{$resetLink}' style='background-color: {$colorPrincipal}; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                                            Restablecer Contraseña
                                        </a>
                                    </div>

                                    <p style='color: #999999; font-size: 12px; margin-top: 30px;'>
                                        Si no solicitaste este cambio, puedes ignorar este correo con seguridad. 
                                        El enlace expirará automáticamente en 1 hora.
                                    </p>
                                </div>

                                <div style='background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #eeeeee;'>
                                    <p style='color: #aaaaaa; font-size: 11px; margin: 0;'>
                                        © " . date( 'Y' ) . " Escuela de Natación - Panel Administrativo
                                    </p>
                                </div>
                            </div>
                        </div>
                        ";
           // $mail->SMTPDebug = 3;
            // Nivel 3 es más detallado
         //   $mail->Debugoutput = 'html';
            // Para que se vea bien en el navegador
            $mail->send();
            return true;
        } catch ( Exception $e ) {
            //error_log( $e->getMessage() );
            echo 'Error de PHPMailer: ' . $mail->ErrorInfo;
            return false;
        }
    }
    
    public function sendWelcomeCoach($toEmail, $tempPassword, $coachName)
    {
        $colorPrincipal = '#007bff';
        $colorFondo = '#f4f7f9';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = Env::get('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get('MAIL_USERNAME');
            $mail->Password   = Env::get('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = Env::get('MAIL_PORT');

            $mail->setFrom(Env::get('MAIL_FROM'), 'Soporte Escuela de Natación');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Bienvenido a la Escuela de Natación';

            $baseUrl = rtrim(Env::get('APP_URL'), '/');
            $loginUrl = $baseUrl . '/?url=login';

            $mail->Body = "
                <div style='background-color: {$colorFondo}; padding: 40px; font-family: Arial, sans-serif; line-height: 1.6;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e1e8ed;'>
                        <div style='background-color: {$colorPrincipal}; padding: 20px; text-align: center;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Escuela de Natación</h1>
                        </div>

                        <div style='padding: 30px;'>
                            <h2 style='color: #333333;'>Bienvenido, {$coachName}</h2>

                            <p style='color: #666666; font-size: 16px;'>
                                Se ha creado tu cuenta como profesor en el sistema.
                                Podés acceder con las siguientes credenciales:
                            </p>

                            <div style='background-color: #f8f9fa; border: 1px solid #e1e8ed; border-radius: 5px; padding: 20px; margin: 20px 0;'>
                                <p style='margin: 5px 0;'>
                                    <strong>Email:</strong> {$toEmail}
                                </p>

                                <p style='margin: 5px 0;'>
                                    <strong>Contraseña provisoria:</strong>
                                    <span style='font-family: monospace; font-size: 18px; color: {$colorPrincipal};'>{$tempPassword}</span>
                                </p>
                            </div>

                            <p style='color: #666666; font-size: 14px;'>
                                Por seguridad, te recomendamos cambiar tu contraseña después de iniciar sesión.
                            </p>

                            <div style='margin: 30px 0; text-align: center;'>
                                <a href='{$loginUrl}' style='background-color: {$colorPrincipal}; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>
                                    Iniciar Sesión
                                </a>
                            </div>
                        </div>

                        <div style='background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #eeeeee;'>
                            <p style='color: #aaaaaa; font-size: 11px; margin: 0;'>
                                © " . date('Y') . " Escuela de Natación - Panel Administrativo
                            </p>
                        </div>
                    </div>
                </div>
            ";

            $mail->send();

            return true;

        } catch (Exception $e) {
            echo 'Error de PHPMailer: ' . $mail->ErrorInfo;
            return false;
        }
    }
}