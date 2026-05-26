<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function send_reset_email(string $to, string $resetLink): bool
{
    $mail = new PHPMailer(true);

    try {

        // UTF-8
        $mail->CharSet = 'UTF-8';

        // SMTP
        $mail->isSMTP();

        // Serveur Brevo
        $mail->Host = $_ENV['BREVO_HOST'];

        // Authentification
        $mail->SMTPAuth = true;

        $mail->Username = $_ENV['BREVO_USERNAME'];

        $mail->Password = $_ENV['BREVO_PASSWORD'];

        // Sécurité
        $mail->SMTPSecure = 'tls';

        // Port
        $mail->Port = 587;

        // Expéditeur
        $mail->setFrom(
            $_ENV['BREVO_FROM'],
            'SherWheels Festival'
        );

        // Destinataire
        $mail->addAddress($to);

        // HTML
        $mail->isHTML(true);

        // Sujet
        $mail->Subject = 'Réinitialisation du mot de passe';

        // Corps du mail
        $mail->Body = '
            <div style="
            background:#070b14;
            padding:40px;
            font-family:Arial,sans-serif;
            color:#ffffff;
        ">

            <div style="
                max-width:600px;
                margin:auto;
                background:#0d1526;
                border:1px solid #1d2b45;
                padding:40px;
                border-radius:12px;
            ">

                <h1 style="
                    color:#ffffff;
                    margin-bottom:10px;
                ">
                    SherWheels Festival
                </h1>

                <p style="
                    color:#cfcfcf;
                    font-size:16px;
                    line-height:1.7;
                ">
                    Nous sommes ravis de vous revoir
                </p>

                <p style="
                    color:#cfcfcf;
                    font-size:16px;
                    line-height:1.7;
                ">
                    Apparemment votre mot de passe a décidé
                    de partir faire un tour au festival sans vous.
                </p>

                <p style="
                    color:#cfcfcf;
                    font-size:16px;
                    line-height:1.7;
                ">
                    Pas de panique, cliquez simplement sur le bouton
                    ci-dessous pour reprendre le contrôle de votre compte.
                </p>

                <div style="margin:35px 0;">

                    <a href="' . $resetLink . '" style="
                        background:#c8a96b;
                        color:#070b14;
                        text-decoration:none;
                        padding:14px 28px;
                        border-radius:8px;
                        font-weight:bold;
                        display:inline-block;
                    ">
                        Réinitialiser mon mot de passe
                    </a>

                </div>

                <p style="
                    color:#8f9bb3;
                    font-size:14px;
                    line-height:1.6;
                ">
                    Ce lien expire dans 1 heure.
                </p>

                <hr style="
                    border:none;
                    border-top:1px solid #24324f;
                    margin:30px 0;
                ">

                <p style="
                    color:#6f7d98;
                    font-size:13px;
                ">
                    Si vous n’êtes pas à l’origine de cette demande,
                    vous pouvez ignorer cet email.
                </p>

            </div>

        </div>

        ';

        return $mail->send();

    } catch (Exception $e) {

        error_log($mail->ErrorInfo);

        return false;
    }
}