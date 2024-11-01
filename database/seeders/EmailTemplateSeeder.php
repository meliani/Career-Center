<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Visualbuilder\EmailTemplates\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $language = $this->command->choice(
            'Which language do you want to seed?',
            ['EN', 'FR'],
            0
        );

        $emailTemplatesEN = [
            [
                'key' => 'user-welcome',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Welcome Email',
                'title' => 'Welcome to ##config.app.name##',
                'subject' => 'Welcome to ##config.app.name##',
                'preheader' => 'Lets get you started',
                'content' => '<p>Dear ##user.name##,</p>
                                <p>Thanks for registering with ##config.app.name##.</p>
                                <p>If you need any assistance please contact our customer services team ##config.email-templates.enterprise-relations.email## who will be happy to help.</p>
                                <p>Kind Regards<br>
                                ##config.app.name##</p>',
            ],
            [
                'key' => 'user-request-reset',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Request Password Reset',
                'title' => 'Reset your password',
                'subject' => '##config.app.name## Password Reset',
                'preheader' => 'Reset Password',
                'content' => "<p>Hello ##user.name##,</p>
                                <p>You are receiving this email because we received a password reset request for your account.</p>
                                <div>##button url='##tokenURL##' title='Change My Password'##</div>
                                <p>If you didn't request this password reset, no further action is needed. However if this has happened more than once in a short space of time, please let us know.</p>
                                <p>We'll never ask for your credentials over the phone or by email and you should never share your credentials</p>
                                <p>If you’re having trouble clicking the 'Change My Password' button, copy and paste the URL below into your web browser:</p>
                                <p><a href='##tokenURL##'>##tokenURL##</a></p>
                                <p>Kind Regards,<br>##config.app.name##</p>",
            ],
            [
                'key' => 'user-password-reset-success',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Password Reset',
                'title' => 'Password Reset Success',
                'subject' => '##config.app.name## password has been reset',
                'preheader' => 'Success',
                'content' => '<p>Dear ##user.name##,</p>
                                <p>Your password has been reset.</p>
                                <p>Kind Regards,<br>##config.app.name##</p>',
            ],
            [
                'key' => 'user-locked-out',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],

                'name' => 'User Account Locked Out',
                'title' => 'Account Locked',
                'subject' => '##config.app.name## account has been locked',
                'preheader' => 'Oops!',
                'content' => '<p>Dear ##user.name##,</p>
                                <p>Sorry your account has been locked out due to too many bad password attempts.</p>
                                <p>Please contact our customer services team on ##config.email-templates.enterprise-relations.email## who will be able to help</p>
                                 <p>Kind Regards,<br>##config.app.name##</p>',

            ],
            [
                'key' => 'user-verify-email',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],

                'name' => 'User Verify Email',
                'title' => 'Verify your email',
                'subject' => 'Verify your email with ##config.app.name##',
                'preheader' => 'Gain Access Now',
                'content' => "<p>Dear ##user.name##,</p>
                                <p>Your receiving this email because your email address has been registered on ##config.app.name##.</p>
                                <p>To activate your account please click the button below.</p>
                                <div>##button url='##verificationUrl##' title='Verify Email Address'##</div>
                                <p>If you’re having trouble clicking the 'Verify Email Address' button, copy and paste the URL below into your web browser:</p>
                                <p><a href='##verificationUrl##'>##verificationUrl##</a></p>
                                <p>Kind Regards,<br>##config.app.name##</p>",
            ],
            [
                'key' => 'user-verified',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Verified',
                'title' => 'Verification Success',
                'subject' => 'Verification success for ##config.app.name##',
                'preheader' => 'Verification success for ##config.app.name##',
                'content' => '<p>Hi ##user.name##,</p>
                                <p>Your email address ##user.email## has been verified on ##config.app.name##</p>
                                <p>Kind Regards,<br>##config.app.name##</p>',
            ],
            [
                'key' => 'user-login',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'User Logged In',
                'title' => 'Login Success',
                'subject' => 'Login Success for ##config.app.name##',
                'preheader' => 'Login Success for ##config.app.name##',
                'content' => '<p>Hi ##user.name##,</p>
                                <p>You have been logged into ##config.app.name##.</p>
                                <p>If this was not you please contact: </p>
                                <p>You can disable this email in your account notification preferences.</p>
                                <p>Kind Regards,<br>##config.app.name##</p>',
            ],
        ];

        $emailTemplatesFR = [
            [
                'key' => 'user-welcome',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email de Bienvenue Utilisateur',
                'title' => 'Bienvenue sur ##config.app.name##',
                'subject' => 'Bienvenue sur ##config.app.name##',
                'preheader' => 'Commencez ici',
                'content' => "<p>Cher/Chère ##user.name##,</p>
                                <p>Merci de vous être inscrit(e) sur ##config.app.name##.</p>
                                <p>Si vous avez besoin d'aide, veuillez contacter notre service client à ##config.email-templates.enterprise-relations.email## qui se fera un plaisir de vous aider.</p>
                                <p>Cordialement,<br>
                                ##config.app.name##</p>",
            ],
            [
                'key' => 'user-request-reset',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Demande de réinitialisation de mot de passe utilisateur',
                'title' => 'Réinitialisez votre mot de passe',
                'subject' => 'Réinitialisation de mot de passe ##config.app.name##',
                'preheader' => 'Réinitialisation de mot de passe',
                'content' => "<p>Bonjour ##user.name##,</p>
                                <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
                                <div>##button url='##tokenURL##' title='Changer mon mot de passe'##</div>
                                <p>Si vous n'avez pas demandé cette réinitialisation de mot de passe, aucune action supplémentaire n'est nécessaire. Cependant, si cela s'est produit plus d'une fois en peu de temps, veuillez nous en informer.</p>
                                <p>Nous ne demanderons jamais vos informations d'identification par téléphone ou par e-mail et vous ne devez jamais partager vos informations d'identification.</p>
                                <p>Si vous avez des difficultés à cliquer sur le bouton 'Changer mon mot de passe', copiez et collez l'URL ci-dessous dans votre navigateur Web :</p>
                                <p><a href='##tokenURL##'>##tokenURL##</a></p>
                                <p>Cordialement,<br>##config.app.name##</p>",
            ],
            [
                'key' => 'user-password-reset-success',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Réinitialisation de mot de passe utilisateur',
                'title' => 'Réinitialisation de mot de passe réussie',
                'subject' => 'Le mot de passe ##config.app.name## a été réinitialisé',
                'preheader' => 'Succès',
                'content' => '<p>Cher/Chère ##user.name##,</p>
                                <p>Votre mot de passe a été réinitialisé.</p>
                                <p>Cordialement,<br>##config.app.name##</p>',
            ],
            [
                'key' => 'user-locked-out',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Compte utilisateur verrouillé',
                'title' => 'Compte verrouillé',
                'subject' => 'Le compte ##config.app.name## a été verrouillé',
                'preheader' => 'Oups!',
                'content' => '<p>Cher/Chère ##user.name##,</p>
                                <p>Désolé, votre compte a été verrouillé en raison de trop de tentatives de mot de passe incorrectes.</p>
                                <p>Veuillez contacter notre service client au ##config.email-templates.enterprise-relations.email## qui pourra vous aider</p>
                                <p>Cordialement,<br>##config.app.name##</p>',
            ],
            [
                'key' => 'user-verify-email',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Vérifier l\'email utilisateur',
                'title' => 'Vérifiez votre email',
                'subject' => 'Vérifiez votre email avec ##config.app.name##',
                'preheader' => 'Accédez maintenant',
                'content' => "<p>Cher/Chère ##user.name##,</p>
                                <p>Vous recevez cet e-mail car votre adresse e-mail a été enregistrée sur ##config.app.name##.</p>
                                <p>Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous.</p>
                                <div>##button url='##verificationUrl##' title='Vérifier l'adresse e-mail'##</div>
                                <p>Si vous avez des difficultés à cliquer sur le bouton 'Vérifier l'adresse e-mail', copiez et collez l'URL ci-dessous dans votre navigateur Web :</p>
                                <p><a href='##verificationUrl##'>##verificationUrl##</a></p>
                                <p>Cordialement,<br>##config.app.name##</p>",
            ],
            [
                'key' => 'user-verified',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Email vérifié utilisateur',
                'title' => 'Succès de la vérification',
                'subject' => 'Succès de la vérification pour ##config.app.name##',
                'preheader' => 'Succès de la vérification pour ##config.app.name##',
                'content' => '<p>Bonjour ##user.name##,</p>
                                <p>Votre adresse e-mail ##user.email## a été vérifiée sur ##config.app.name##</p>
                                <p>Cordialement,<br>##config.app.name##</p>',
            ],
            [
                'key' => 'user-login',
                'from' => ['email' => config('mail.from.address'), 'name' => config('mail.from.name')],
                'name' => 'Utilisateur connecté',
                'title' => 'Connexion réussie',
                'subject' => 'Connexion réussie pour ##config.app.name##',
                'preheader' => 'Connexion réussie pour ##config.app.name##',
                'content' => '<p>Bonjour ##user.name##,</p>
                                <p>Vous avez été connecté à ##config.app.name##.</p>
                                <p>Si ce n\'était pas vous, veuillez contacter :</p>
                                <p>Vous pouvez désactiver cet e-mail dans vos préférences de notification de compte.</p>
                                <p>Cordialement,<br>##config.app.name##</p>',
            ],

        ];

        $emailTemplates = $language === 'FR' ? $emailTemplatesFR : $emailTemplatesEN;

        EmailTemplate::factory()
            ->createMany($emailTemplates);
    }
}
