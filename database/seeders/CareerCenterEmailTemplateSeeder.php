<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Visualbuilder\EmailTemplates\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // English templates
            [
                'key' => 'user-welcome',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Welcome Email',
                'title' => 'Welcome to the Career Center Portal',
                'subject' => 'Welcome to ##config.app.name## Career Center',
                'preheader' => 'Your journey to career success starts here',
                'content' => "<p>Dear ##user.name##,</p>
                                <p>Welcome to the ##config.app.name## Career Center Portal! We're excited to help you on your professional journey.</p>
                                <p>Through our platform, you'll have access to:</p>
                                <ul>
                                    <li>Job and internship opportunities</li>
                                    <li>Career counseling services</li>
                                    <li>Resume building tools</li>
                                    <li>Professional development workshops</li>
                                </ul>
                                <p>If you need assistance, contact our career advisors at ##config.email-templates.enterprise-relations.email##</p>
                                <p>Best regards,<br>##config.app.name## Career Center Team</p>",
            ],
            [
                'key' => 'user-request-reset',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Password Reset Request',
                'title' => 'Reset your Career Portal password',
                'subject' => 'Password Reset Request - ##config.app.name## Career Center',
                'preheader' => 'Reset your password',
                'content' => "<p>Hello ##user.name##,</p>
                                <p>We received a password reset request for your Career Center account.</p>
                                <div>##button url='##tokenURL##' title='Reset Password'##</div>
                                <p>If you didn't request this reset, please ignore this email or contact our support team if you have concerns.</p>
                                <p>The link will expire in 60 minutes.</p>
                                <p>Best regards,<br>##config.app.name## Career Center Team</p>",
            ],
            // French templates
            [
                'key' => 'user-welcome',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email de bienvenue',
                'title' => 'Bienvenue au Centre de Carrière',
                'subject' => 'Bienvenue au Centre de Carrière ##config.app.name##',
                'preheader' => 'Votre parcours professionnel commence ici',
                'content' => "<p>Cher(e) ##user.name##,</p>
                                <p>Bienvenue sur le portail du Centre de Carrière de ##config.app.name## ! Nous sommes ravis de vous accompagner dans votre parcours professionnel.</p>
                                <p>Sur notre plateforme, vous aurez accès à :</p>
                                <ul>
                                    <li>Des offres d'emploi et de stage</li>
                                    <li>Des services d'orientation professionnelle</li>
                                    <li>Des outils de création de CV</li>
                                    <li>Des ateliers de développement professionnel</li>
                                </ul>
                                <p>Pour toute assistance, contactez nos conseillers à ##config.email-templates.enterprise-relations.email##</p>
                                <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],
            [
                'key' => 'user-request-reset',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Demande de réinitialisation du mot de passe',
                'title' => 'Réinitialisez votre mot de passe',
                'subject' => 'Demande de réinitialisation - Centre de Carrière ##config.app.name##',
                'preheader' => 'Réinitialisez votre mot de passe',
                'content' => "<p>Bonjour ##user.name##,</p>
                                <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte du Centre de Carrière.</p>
                                <div>##button url='##tokenURL##' title='Réinitialiser le mot de passe'##</div>
                                <p>Si vous n'avez pas fait cette demande, veuillez ignorer cet email ou contacter notre équipe support si vous avez des inquiétudes.</p>
                                <p>Le lien expirera dans 60 minutes.</p>
                                <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],
            // User Verify Email - English
            [
                'key' => 'user-verify-email',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email Verification',
                'title' => 'Verify Your Email Address',
                'subject' => 'Please Verify Your Email - ##config.app.name## Career Center',
                'preheader' => 'One quick step to activate your account',
                'content' => "<p>Hello ##user.name##,</p>
                             <p>Please verify your email address by clicking the button below:</p>
                             <div>##button url='##verifyURL##' title='Verify Email'##</div>
                             <p>If you did not create an account, no further action is required.</p>
                             <p>Best regards,<br>##config.app.name## Career Center Team</p>",
            ],
            // User Verify Email - French
            [
                'key' => 'user-verify-email',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Vérification Email',
                'title' => 'Vérifiez votre adresse email',
                'subject' => 'Veuillez vérifier votre email - Centre de Carrière ##config.app.name##',
                'preheader' => 'Une étape rapide pour activer votre compte',
                'content' => "<p>Bonjour ##user.name##,</p>
                             <p>Veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous :</p>
                             <div>##button url='##verifyURL##' title='Vérifier Email'##</div>
                             <p>Si vous n'avez pas créé de compte, aucune action n'est requise.</p>
                             <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],

            // User Verified Email - English
            [
                'key' => 'user-verified-email',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email Verified Success',
                'title' => 'Email Successfully Verified',
                'subject' => 'Email Verified - ##config.app.name## Career Center',
                'preheader' => 'Your email has been verified successfully',
                'content' => '<p>Hello ##user.name##,</p>
                             <p>Your email has been successfully verified. You now have full access to your account.</p>
                             <p>You can now access all features of the Career Center Portal.</p>
                             <p>Best regards,<br>##config.app.name## Career Center Team</p>',
            ],
            // User Verified Email - French
            [
                'key' => 'user-verified-email',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email Vérifié avec Succès',
                'title' => 'Email Vérifié avec Succès',
                'subject' => 'Email Vérifié - Centre de Carrière ##config.app.name##',
                'preheader' => 'Votre email a été vérifié avec succès',
                'content' => "<p>Bonjour ##user.name##,</p>
                             <p>Votre email a été vérifié avec succès. Vous avez maintenant un accès complet à votre compte.</p>
                             <p>Vous pouvez désormais accéder à toutes les fonctionnalités du Centre de Carrière.</p>
                             <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],

            // Password Reset Success - English
            [
                'key' => 'user-reset-success',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Password Reset Success',
                'title' => 'Password Successfully Reset',
                'subject' => 'Password Reset Successful - ##config.app.name## Career Center',
                'preheader' => 'Your password has been successfully changed',
                'content' => '<p>Hello ##user.name##,</p>
                             <p>Your password has been successfully reset.</p>
                             <p>If you did not make this change, please contact us immediately at ##config.email-templates.enterprise-relations.email##</p>
                             <p>Best regards,<br>##config.app.name## Career Center Team</p>',
            ],
            // Password Reset Success - French
            [
                'key' => 'user-reset-success',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Réinitialisation Réussie',
                'title' => 'Mot de passe réinitialisé avec succès',
                'subject' => 'Mot de passe réinitialisé - Centre de Carrière ##config.app.name##',
                'preheader' => 'Votre mot de passe a été changé avec succès',
                'content' => "<p>Bonjour ##user.name##,</p>
                             <p>Votre mot de passe a été réinitialisé avec succès.</p>
                             <p>Si vous n'êtes pas à l'origine de ce changement, veuillez nous contacter immédiatement à ##config.email-templates.enterprise-relations.email##</p>
                             <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],

            // User Locked Out - English
            [
                'key' => 'user-locked-out',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Account Locked',
                'title' => 'Account Security Alert',
                'subject' => 'Account Locked - ##config.app.name## Career Center',
                'preheader' => 'Important security notification about your account',
                'content' => "<p>Hello ##user.name##,</p>
                             <p>Your account has been temporarily locked due to multiple failed login attempts.</p>
                             <p>To unlock your account, please:</p>
                             <ul>
                                <li>Wait 30 minutes before trying again, or</li>
                                <li>Reset your password using the forgot password feature</li>
                             </ul>
                             <p>If you didn't attempt these logins, please contact our support team immediately.</p>
                             <p>Best regards,<br>##config.app.name## Career Center Team</p>",
            ],
            // User Locked Out - French
            [
                'key' => 'user-locked-out',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Compte Bloqué',
                'title' => 'Alerte de Sécurité du Compte',
                'subject' => 'Compte Bloqué - Centre de Carrière ##config.app.name##',
                'preheader' => 'Notification importante concernant la sécurité de votre compte',
                'content' => "<p>Bonjour ##user.name##,</p>
                             <p>Votre compte a été temporairement bloqué en raison de multiples tentatives de connexion échouées.</p>
                             <p>Pour débloquer votre compte, veuillez :</p>
                             <ul>
                                <li>Attendre 30 minutes avant de réessayer, ou</li>
                                <li>Réinitialiser votre mot de passe via la fonction mot de passe oublié</li>
                             </ul>
                             <p>Si vous n'êtes pas à l'origine de ces tentatives, veuillez contacter notre équipe support immédiatement.</p>
                             <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],

            // User Login Success - English
            [
                'key' => 'user-login-success',
                'language' => 'en_US',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Login Notification',
                'title' => 'New Login to Your Account',
                'subject' => 'New Login Detected - ##config.app.name## Career Center',
                'preheader' => 'A new login has been detected on your account',
                'content' => "<p>Hello ##user.name##,</p>
                             <p>A new login was detected on your account at ##login.time## from ##login.location##.</p>
                             <p>If this wasn't you, please secure your account immediately by:</p>
                             <ul>
                                <li>Changing your password</li>
                                <li>Contacting our support team</li>
                             </ul>
                             <p>Best regards,<br>##config.app.name## Career Center Team</p>",
            ],
            // User Login Success - French
            [
                'key' => 'user-login-success',
                'language' => 'fr',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Notification de Connexion',
                'title' => 'Nouvelle Connexion à Votre Compte',
                'subject' => 'Nouvelle Connexion Détectée - Centre de Carrière ##config.app.name##',
                'preheader' => 'Une nouvelle connexion a été détectée sur votre compte',
                'content' => "<p>Bonjour ##user.name##,</p>
                             <p>Une nouvelle connexion a été détectée sur votre compte à ##login.time## depuis ##login.location##.</p>
                             <p>Si ce n'était pas vous, veuillez sécuriser votre compte immédiatement en :</p>
                             <ul>
                                <li>Changeant votre mot de passe</li>
                                <li>Contactant notre équipe support</li>
                             </ul>
                             <p>Cordialement,<br>L'équipe du Centre de Carrière ##config.app.name##</p>",
            ],
        ];

        EmailTemplate::factory()
            ->createMany($templates);
    }
}
