<?php

namespace Database\Seeders;

use Modules\Landlord\Models\Language;
use Modules\Landlord\Models\LanguageLine;
use Modules\Landlord\Models\Translation;
use Illuminate\Database\Seeder;

class LanguageTranslationSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Languages ────────────────────────────────────────────
        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English',  'direction' => 'ltr', 'is_default' => true],
            ['code' => 'ar', 'name' => 'Arabic',  'native_name' => 'العربية',  'direction' => 'rtl', 'is_default' => false],
            ['code' => 'fr', 'name' => 'French',  'native_name' => 'Français', 'direction' => 'ltr', 'is_default' => false],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }

        $this->command->info('Languages seeded: en, ar, fr');

        // ─── Translations (each row = JSON text per locale) ──────
        $items = [
            // nav
            ['nav', 'home',       ['en' => 'Home',                    'ar' => 'الرئيسية',               'fr' => 'Accueil']],
            ['nav', 'about',      ['en' => 'About',                   'ar' => 'من نحن',                 'fr' => 'À propos']],
            ['nav', 'pricing',    ['en' => 'Pricing',                 'ar' => 'الباقات',                'fr' => 'Tarifs']],
            ['nav', 'contact',    ['en' => 'Contact',                 'ar' => 'اتصل بنا',               'fr' => 'Contact']],
            ['nav', 'startTrial', ['en' => 'Start Free Trial',        'ar' => 'ابدأ النسخة التجريبية',  'fr' => 'Essai gratuit']],

            // hero
            ['hero', 'badge',    ['en' => 'Trusted by 500+ companies worldwide',                       'ar' => 'موثوق من أكثر من 500 شركة حول العالم',                     'fr' => 'Approuvé par plus de 500 entreprises']],
            ['hero', 'title',    ['en' => 'Scale Your Business with NexaERP',                          'ar' => 'ارتقِ بأعمالك مع NexaERP',                                 'fr' => 'Propulsez votre entreprise avec NexaERP']],
            ['hero', 'subtitle', ['en' => 'The all-in-one cloud ERP platform.',                         'ar' => 'منصة ERP السحابية المتكاملة',                              'fr' => 'La plateforme ERP cloud complète']],
            ['hero', 'cta',      ['en' => 'Get Started Free',          'ar' => 'ابدأ مجاناً',           'fr' => 'Commencer gratuit']],
            ['hero', 'demo',     ['en' => 'Watch Demo',               'ar' => 'شاهد العرض',            'fr' => 'Voir la démo']],

            // pricing
            ['pricing', 'title',        ['en' => 'Plans for Every Business Size', 'ar' => 'باقات تناسب كل حجم أعمال',              'fr' => 'Des offres pour chaque taille d\'entreprise']],
            ['pricing', 'monthly',      ['en' => 'Monthly',                      'ar' => 'شهري',                                 'fr' => 'Mensuel']],
            ['pricing', 'annual',       ['en' => 'Annual',                       'ar' => 'سنوي',                                 'fr' => 'Annuel']],
            ['pricing', 'save',         ['en' => 'Save 20%',                     'ar' => 'وفر 20%',                              'fr' => 'Économisez 20%']],
            ['pricing', 'getStarted',   ['en' => 'Get Started',                  'ar' => 'ابدأ الآن',                            'fr' => 'Commencer']],
            ['pricing', 'mostPopular',  ['en' => 'Most Popular',                 'ar' => 'الأكثر طلباً',                         'fr' => 'Le plus populaire']],
            ['pricing', 'perMonth',     ['en' => '/mo',                          'ar' => '/شهر',                                'fr' => '/mois']],

            // contact
            ['contact', 'title',    ['en' => 'Get in Touch',          'ar' => 'تواصل معنا',             'fr' => 'Contactez-nous']],
            ['contact', 'name',     ['en' => 'Full Name',             'ar' => 'الاسم الكامل',           'fr' => 'Nom complet']],
            ['contact', 'email',    ['en' => 'Email Address',         'ar' => 'البريد الإلكتروني',      'fr' => 'Adresse email']],
            ['contact', 'company',  ['en' => 'Company Name',          'ar' => 'اسم الشركة',             'fr' => 'Nom de l\'entreprise']],
            ['contact', 'subject',  ['en' => 'Subject',               'ar' => 'الموضوع',                'fr' => 'Sujet']],
            ['contact', 'message',  ['en' => 'Message',               'ar' => 'الرسالة',                'fr' => 'Message']],
            ['contact', 'send',     ['en' => 'Send Message',          'ar' => 'إرسال الرسالة',          'fr' => 'Envoyer']],
            ['contact', 'sending',  ['en' => 'Sending...',            'ar' => 'جارٍ الإرسال...',        'fr' => 'Envoi en cours...']],
            ['contact', 'sent',     ['en' => 'Message Sent!',         'ar' => 'تم الإرسال بنجاح!',      'fr' => 'Message envoyé !']],

            // auth
            ['auth', 'login',               ['en' => 'Login',                          'ar' => 'تسجيل الدخول',             'fr' => 'Connexion']],
            ['auth', 'logout',              ['en' => 'Logout',                         'ar' => 'تسجيل الخروج',            'fr' => 'Déconnexion']],
            ['auth', 'email',               ['en' => 'Email Address',                  'ar' => 'البريد الإلكتروني',        'fr' => 'Adresse email']],
            ['auth', 'password',            ['en' => 'Password',                       'ar' => 'كلمة المرور',              'fr' => 'Mot de passe']],
            ['auth', 'mobile',              ['en' => 'Mobile Number',                  'ar' => 'رقم الجوال',               'fr' => 'Numéro mobile']],
            ['auth', 'otp',                 ['en' => 'Verification Code',              'ar' => 'رمز التحقق',               'fr' => 'Code de vérification']],
            ['auth', 'sendOtp',             ['en' => 'Send Code',                      'ar' => 'إرسال الرمز',              'fr' => 'Envoyer le code']],
            ['auth', 'verifyOtp',           ['en' => 'Verify Code',                    'ar' => 'تحقق من الرمز',            'fr' => 'Vérifier le code']],
            ['auth', 'loginWithPassword',   ['en' => 'Login with Password',            'ar' => 'تسجيل بكلمة المرور',       'fr' => 'Connexion par mot de passe']],
            ['auth', 'loginWithEmailOtp',   ['en' => 'Login with Email OTP',           'ar' => 'تسجيل برمز البريد',        'fr' => 'Connexion par email OTP']],
            ['auth', 'loginWithMobileOtp',  ['en' => 'Login with Mobile OTP',          'ar' => 'تسجيل برمز الجوال',        'fr' => 'Connexion par mobile OTP']],

            // common
            ['common', 'save',     ['en' => 'Save',       'ar' => 'حفظ',     'fr' => 'Enregistrer']],
            ['common', 'cancel',   ['en' => 'Cancel',     'ar' => 'إلغاء',   'fr' => 'Annuler']],
            ['common', 'delete',   ['en' => 'Delete',     'ar' => 'حذف',     'fr' => 'Supprimer']],
            ['common', 'edit',     ['en' => 'Edit',       'ar' => 'تعديل',   'fr' => 'Modifier']],
            ['common', 'create',   ['en' => 'Create',     'ar' => 'إضافة',   'fr' => 'Créer']],
            ['common', 'search',   ['en' => 'Search',     'ar' => 'بحث',     'fr' => 'Rechercher']],
            ['common', 'loading',  ['en' => 'Loading...', 'ar' => 'جارٍ التحميل...', 'fr' => 'Chargement...']],
            ['common', 'noData',   ['en' => 'No data found',  'ar' => 'لا توجد بيانات', 'fr' => 'Aucune donnée']],
            ['common', 'confirm',  ['en' => 'Confirm',   'ar' => 'تأكيد',   'fr' => 'Confirmer']],
            ['common', 'back',     ['en' => 'Back',      'ar' => 'رجوع',    'fr' => 'Retour']],
            ['common', 'next',     ['en' => 'Next',      'ar' => 'التالي',  'fr' => 'Suivant']],
            ['common', 'actions',  ['en' => 'Actions',   'ar' => 'الإجراءات', 'fr' => 'Actions']],
            ['common', 'status',   ['en' => 'Status',    'ar' => 'الحالة',  'fr' => 'status']],
            ['common', 'active',   ['en' => 'Active',    'ar' => 'نشط',     'fr' => 'Actif']],
            ['common', 'inactive', ['en' => 'Inactive',  'ar' => 'غير نشط', 'fr' => 'Inactif']],
            ['common', 'yes',      ['en' => 'Yes',       'ar' => 'نعم',     'fr' => 'Oui']],
            ['common', 'no',       ['en' => 'No',        'ar' => 'لا',      'fr' => 'Non']],

            // admin/manager
            ['admin', 'dashboard',          ['en' => 'Dashboard',          'ar' => 'لوحة التحكم',       'fr' => 'Tableau de bord']],
            ['admin', 'tenants',            ['en' => 'Tenants',            'ar' => 'العملاء',           'fr' => 'Clients']],
            ['admin', 'packages',           ['en' => 'Packages',           'ar' => 'الباقات',           'fr' => 'Forfaits']],
            ['admin', 'subscriptions',      ['en' => 'Subscriptions',      'ar' => 'الاشتراكات',        'fr' => 'Abonnements']],
            ['admin', 'portfolioCms',       ['en' => 'Portfolio CMS',      'ar' => 'إدارة الموقع',      'fr' => 'Gestion du site']],
            ['admin', 'settings',           ['en' => 'Settings',           'ar' => 'الإعدادات',         'fr' => 'Paramètres']],
            ['admin', 'totalTenants',       ['en' => 'Total Tenants',      'ar' => 'إجمالي العملاء',    'fr' => 'Total clients']],
            ['admin', 'activeSubscriptions',['en' => 'Active Subscriptions','ar' => 'الاشتراكات النشطة', 'fr' => 'Abonnements actifs']],
            ['admin', 'monthlyRevenue',     ['en' => 'Monthly Revenue',    'ar' => 'الإيرادات الشهرية', 'fr' => 'Revenus mensuels']],
            ['admin', 'totalUsers',         ['en' => 'Total Users',        'ar' => 'إجمالي المستخدمين', 'fr' => 'Total utilisateurs']],
        ];

        foreach ($items as [$group, $key, $text]) {
            LanguageLine::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['text' => $text]
            );
        }

        Translation::flushCache();
        $this->command->info('Translations seeded: ' . count($items) . ' keys × 3 languages');
    }
}
