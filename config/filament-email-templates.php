<?php

return [
    /**
     * If you wish to customise the table name change this before migration
     */
    'table_name' => 'email_templates',
    'theme_table_name' => 'email_templates_themes',

    /**
     * Mail Classes will be generated into this directory
     */
    'mailable_directory' => 'Mail/Visualbuilder/EmailTemplates',

    /**
     * If you want to use your own token helper replace this class
     *  Eg create a file like this:-
     *
     *  namespace App\Helpers
     *
     *  use Visualbuilder\EmailTemplates\Contracts\TokenReplacementInterface;
     *
     *  class MyTokenHelper implements TokenReplacementInterface
     *  {
     *      public function replaceTokens($content, $models)
     *          {
     *           // First, call the parent method if you want to retain and build upon its functionality
     *              $content = parent::replaceTokens($content, $models);
     *      }
     *  }
     */
    'tokenHelperClass' => \Visualbuilder\EmailTemplates\DefaultTokenHelper::class,

    /**
     * Some tokens don't belong to a model.  These $models->token will be checked
     */
    'known_tokens' => [
        'tokenUrl',
        'verificationUrl',
        'message',
    ],

    /**
     * Admin panel navigation options
     */
    'navigation' => [
        'templates' => [
            'sort' => 10,
            'label' => 'Email Templates',
            'icon' => 'heroicon-o-envelope',
            'group' => 'Emails',
            'cluster' => false,
            'position' => \Filament\Pages\SubNavigationPosition::Top,
        ],
        'themes' => [
            'sort' => 20,
            'label' => 'Email Template Themes',
            'icon' => 'heroicon-o-paint-brush',
            'group' => 'Emails',
            'cluster' => false,
            'position' => \Filament\Pages\SubNavigationPosition::Top,
        ],
    ],

    //Email templates will be copied to resources/views/vendor/vb-email-templates/email
    //default.blade.php is base view that can be customised below
    'default_view' => 'default_minimal',

    'template_view_path' => 'vb-email-templates::email',

    'template_keys' => [
        'user-welcome' => 'Email de bienvenue',
        'user-request-reset' => 'Demande de réinitialisation du mot de passe',
        'user-password-reset-success' => 'Confirmation de réinitialisation du mot de passe',
        'user-locked-out' => 'Compte bloqué',
        'user-verify-email' => 'Vérification d\'email',
        'user-verified' => 'Email vérifié',
        'user-login' => 'Connexion utilisateur',
        'student-application' => 'Candidature étudiant',
        'professor-notification' => 'Notification enseignant',
    ],

    //Default Logo
    // 'logo' => asset('/svg/logo_entreprises_vectorized.svg'),
    'logo' => 'media/email-templates/logo.png',

    //Browsed Logo
    'browsed_logo' => '/svg',
    // 'browsed_logo' => 'media/email-templates/logos',

    //Logo size in pixels -> 200 pixels high is plenty big enough.
    'logo_width' => '711',
    'logo_height' => '83',

    //Content Width in Pixels
    'content_width' => '600',

    //Contact details included in default email templates
    'enterprise-relations' => [
        'email' => 'entreprises@inpt.ac.ma',
        'phone' => '+212 538 002 700',
    ],

    //Footer Links
    'links' => [
        ['name' => 'Plateforme Carrières', 'url' => 'https://carriere.inpt.ac.ma', 'title' => 'Aller sur la plateforme carrières'],
        ['name' => 'Site Web INPT', 'url' => 'https://www.inpt.ac.ma', 'title' => 'Aller sur le site web'],
    ],

    //Options for alternative languages
    //Note that Laravel default locale is just 'en' you can use this but
    //we are being more specific to cater for English vs USA languages
    'default_locale' => 'fr',

    //These will be included in the language picker when editing an email template
    'languages' => [
        // 'en_GB' => ['display' => 'British', 'flag-icon' => 'gb'],
        'en_US' => ['display' => 'English', 'flag-icon' => 'us'],
        // 'es' => ['display' => 'Español', 'flag-icon' => 'es'],
        'fr' => ['display' => 'Français', 'flag-icon' => 'fr'],
        // 'pt' => ['display' => 'Brasileiro', 'flag-icon' => 'br'],
        // 'in' => ['display' => 'Hindi', 'flag-icon' => 'in'],
    ],

    //Notifiable Models who can receive emails
    'recipients' => [
        App\Models\User::class,
        App\Models\Student::class,
        App\Models\Alumni::class,
        // App\Models\Professor::class,
    ],

    /**
     * Allowed config keys which can be inserted into email templates
     * eg use ##config.app.name## in the email template for automatic replacement.
     */
    'config_keys' => [
        'app.name',
        'app.url',
        'email-templates.enterprise-relations',
        'app.timezone',
        'mail.from.address',
        'mail.from.name',
    ],

    //Most built-in emails can be automatically sent with minimal setup,
    //except "request password reset" requires a function in the User's model.  See readme.md for details
    'send_emails' => [
        'new_user_registered' => true,
        'verification' => true,
        'user_verified' => true,
        'login' => false,
        'password_reset_success' => true,
        'locked_out' => true,
    ],

];
