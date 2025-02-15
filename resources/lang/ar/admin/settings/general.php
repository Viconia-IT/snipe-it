<?php

return [
    'ad'				        => 'الدليل النشط',
    'ad_domain'				    => 'مجال الدليل النشط',
    'ad_domain_help'			=> 'هذا هو أحيانا نفس نطاق البريد الإلكتروني الخاص بك، ولكن ليس دائما.',
    'ad_append_domain_label'    => 'إلحاق اسم domain',
    'ad_append_domain'          => 'إلحاق اسم domain إلى حقل اسم المستخدم',
    'ad_append_domain_help'     => 'المستخدم غير مطلوب لكتابة "username@domain.local" ، فإنها يمكن أن تكتب فقط "اسم المستخدم".',
    'admin_cc_email'            => 'نسخة اضافية للبريد الإكتروني',
    'admin_cc_email_help'       => 'إذا كنت ترغب في إرسال نسخة من رسائل البريد الإلكتروني لتسجيل الدخول / الخروج التي يتم إرسالها إلى المستخدمين إلى حساب بريد إلكتروني إضافي، فقم بإدخالها هنا. خلاف ذلك، اترك هذه الخانة فارغة.',
    'is_ad'				        => 'هذا هو ملقم أكتيف ديركتوري',
    'alerts'                	=> 'Alerts',
    'alert_title'               => 'Update Alert Settings',
    'alert_email'				=> 'إرسال تنبيهات إلى',
    'alert_email_help'    => 'Email addresses or distribution lists you want alerts to be sent to, comma separated',
    'alerts_enabled'			=> 'التنبيهان ممكنه',
    'alert_interval'			=> 'انتهاء فترة التنبيهات (بالأيام)',
    'alert_inv_threshold'		=> 'عتبة تنبيه المخزون',
    'allow_user_skin'           => 'السماح بنمط المستخدم',
    'allow_user_skin_help_text' => 'التحقق من هذا المربع سيسمح للمستخدم باستخدام مظهر واجهة المستخدم بمظهر آخر.',
    'asset_ids'					=> 'ارقام تعريف الاصول',
    'audit_interval'            => 'مدة التدقيق',
    'audit_interval_help'       => 'If you are required to regularly physically audit your assets, enter the interval in months that you use. If you update this value, all of the "next audit dates" for assets with an upcoming audit date will be updated.',
    'audit_warning_days'        => 'عتبة تحذير التدقيق',
    'audit_warning_days_help'   => 'كم يوما مقدما يجب أن نحذركم عندما تكون الأصول مستحقة للتدقيق؟',
    'auto_increment_assets'		=> 'Generate auto-incrementing asset tags',
    'auto_increment_prefix'		=> 'البادئة (اختياري)',
    'auto_incrementing_help'    => 'Enable auto-incrementing asset tags first to set this',
    'backups'					=> 'النسخ الإحتياطية',
    'backups_help'              => 'Create, download, and restore backups ',
    'backups_restoring'         => 'Restoring from Backup',
    'backups_upload'            => 'Upload Backup',
    'backups_path'              => 'Backups on the server are stored in <code>:path</code>',
    'backups_restore_warning'   => 'Use the restore button <small><span class="btn btn-xs btn-warning"><i class="text-white fas fa-retweet" aria-hidden="true"></i></span></small> to restore from a previous backup. (This does not currently work with S3 file storage or Docker.<br><br>Your <strong>entire :app_name database and any uploaded files will be completely replaced</strong> by what\'s in the backup file.  ',
    'backups_logged_out'         => 'سيتم تسجيل الخروج من جميع المستخدمين الحاليين، بما في ذلك انت، بمجرد اكتمال الاستعادة.',
    'backups_large'             => 'Very large backups may time out on the restore attempt and may still need to be run via command line. ',
    'barcode_settings'			=> 'إعدادات الباركود',
    'confirm_purge'			    => 'تأكيد التطهير',
    'confirm_purge_help'		=> 'أدخل النص "DELETE" في المربع أدناه لإزالة السجلات المحذوفة. لا يمكن التراجع عن هذا الإجراء وسيتم حذف كافة العناصر حذف ناعمة والمستخدمين بشكل دائم. (يجب أن تقوم بعمل نسخة احتياطية أولاً، فقط لتكون آمنة.)',
    'custom_css'				=> 'CSS مخصص',
    'custom_css_help'			=> 'أدخل أي تخصيصات CSS ترغب في استخدامها. لا تقم باضافة &lt;style&gt;&lt;/style&gt;.',
    'custom_forgot_pass_url'	=> 'رابط مخصص لاعادة تعيين كلمة المرور',
    'custom_forgot_pass_url_help'	=> 'يحل هذا محل الرابط الخاص باعادة تعيين كلمة المرور على شاشة تسجيل الدخول، وهو مفيد لتوجيه الأشخاص إلى وظيفة إعادة تعيين كلمة مرور من خلال LDAP الداخلي أو المستضاف. وهذا سوف يعطل وظيفة إعادة تعيين كلمة مرور المحلية.',
    'dashboard_message'			=> 'رسالة لوحة المعلومات',
    'dashboard_message_help'	=> 'سيظهر هذا النص على لوحة التحكم لأي شخص لديه صلاحية عرض لوحة التحكم.',
    'default_currency'  		=> 'العملة الافتراضية',
    'default_eula_text'			=> 'اتفاقية ترخيص المستخدم النهائي الافتراضية',
    'default_language'			=> 'اللغة الافتراضية',
    'default_eula_help_text'	=> 'يمكنك أيضا ربط (اتفاقية ترخيص المستخدم) لاصناف محددة من الاصول.',
    'display_asset_name'        => 'عرض اسم مادة العرض',
    'display_checkout_date'     => 'عرض تاريخ الخروج',
    'display_eol'               => 'عرض نهاية العمر على شكل جدول',
    'display_qr'                => 'عرض رموز مربع',
    'display_alt_barcode'		=> 'عرض 1D الباركود',
    'email_logo'                => 'شعار البريد الإلكتروني',
    'barcode_type'				=> '2D نوع الباركود',
    'alt_barcode_type'			=> '1D نوع الباركود',
    'email_logo_size'       => 'الشعارات الشعارات البريد الإلكتروني في البريد الإلكتروني تبدو أفضل. ',
    'enabled'                   => 'Enabled',
    'eula_settings'				=> 'إعدادات اتفاقية ترخيص المستخدم النهائي',
    'eula_markdown'				=> 'تسمح اتفاقية ترخيص المستخدم هذه <a href="https://help.github.com/articles/github-flavored-markdown/">بتطبيق نمط الكتابة من Github</a>.',
    'favicon'                   => 'Favicon',
    'favicon_format'            => 'أنواع الملفات المقبولة هي رمز و png و gif. قد لا تعمل تنسيقات الصور الأخرى في كافة المستعرضات.',
    'favicon_size'          => 'وينبغي أن تكون Favicons صور مربعة ، 16x16 بكسل.',
    'footer_text'               => 'إضافة نص لتذييل الصفحة ',
    'footer_text_help'          => 'سيظهر هذا النص في تذييل الجانب الأيمن. يُسمح باستخدام الروابط باستخدام < href="https://help.github.com/articles/github-flavored-markdown/">eGithub بنكهة markdown</a>. فواصل الأسطر، رؤوس، الصور، الخ قد يؤدي إلى نتائج غير متوقعة.',
    'general_settings'			=> 'الاعدادات العامة',
    'general_settings_keywords' => 'company support, signature, acceptance, email format, username format, images, per page, thumbnail, eula,  tos, dashboard, privacy',
    'general_settings_help'     => 'Default EULA and more',
    'generate_backup'			=> 'إنشاء النسخ الاحتياطي',
    'header_color'              => 'رأس اللون',
    'info'                      => 'تتيح لك هذه الإعدادات تخصيص بعض جوانب التثبيت.',
    'label_logo'                => 'شعار التسمية',
    'label_logo_size'           => 'الشعارات المربعة تبدو أفضل - سيتم عرضها في أعلى يمين كل ملصق أصل. ',
    'laravel'                   => 'نسخة لارافيل',
    'ldap'                      => 'LDAP',
    'ldap_default_group'        => 'Default Permissions Group',
    'ldap_default_group_info'   => 'Select a group to assign to newly synced users. Remember that a user takes on the permissions of the group they are assigned.',
    'ldap_help'                 => 'LDAP/Active Directory',
    'ldap_client_tls_key'       => 'LDAP Client TLS Key',
    'ldap_client_tls_cert'      => 'LDAP Client-Side TLS Certificate',
    'ldap_enabled'              => 'تم تمكين لداب',
    'ldap_integration'          => 'دمج لداب',
    'ldap_settings'             => 'إعدادات لداب',
    'ldap_client_tls_cert_help' => 'Client-Side TLS Certificate and Key for LDAP connections are usually only useful in Google Workspace configurations with "Secure LDAP." Both are required.',
     'ldap_client_tls_key'       => 'LDAP Client-Side TLS key',
    'ldap_login_test_help'      => 'أدخل اسم مستخدم وكلمة مرور LDAP من الاسم المميز الأساسي DN الذي حددته أعلاه لاختبار ما إذا كان قد تمت تهيئة معلومات تسجيل الدخول إلى LDAP بشكل صحيح أم لا. يجب حفظ تحديث LDAP الخاص بك أولا.',
    'ldap_login_sync_help'      => 'هذا يختبر فقط أن LDAP يستطيع المزامنة بشكل صحيح. إذا كان استعلام التوثيق الى LDAP الخاص بك غير صحيح، قد لا يزال المستخدمون غير قادرين على تسجيل الدخول. يجب عليك اولا حفظ اي تغييرات في إعدادات LDAP.',
    'ldap_manager'              => 'LDAP Manager',
    'ldap_server'               => 'خادم لداب',
    'ldap_server_help'          => 'ينبغي أن يبدأ هذا مع //:ldap (للاتصال غير المشفر او TLS) او //:ldaps (لاتصال SSL)',
    'ldap_server_cert'			=> 'التحقق من صحة شهادة سل لداب',
    'ldap_server_cert_ignore'	=> 'السماح بشهادة سل غير صالحة',
    'ldap_server_cert_help'		=> 'حدد مربع الاختيار هذا إذا كنت تستخدم شهادة سل موقعة ذاتيا وترغب في قبول شهادة سل غير صالحة.',
    'ldap_tls'                  => 'استخدام تلس',
    'ldap_tls_help'             => 'يجب التحقق من ذلك فقط في حالة تشغيل ستارتلز على خادم لداب.',
    'ldap_uname'                => 'لداب ربط اسم المستخدم',
    'ldap_dept'                 => 'قسم LDAP',
    'ldap_phone'                => 'رقم هاتف LDAP',
    'ldap_jobtitle'             => 'عنوان وظيفة LDAP',
    'ldap_country'              => 'بلد LDAP',
    'ldap_pword'                => 'لداب ربط كلمة المرور',
    'ldap_basedn'               => 'قاعدة ربط دن',
    'ldap_filter'               => 'فلتر لداب',
    'ldap_pw_sync'              => 'مزامنة كلمة مرور لداب',
    'ldap_pw_sync_help'         => 'ألغ تحديد هذا المربع إذا كنت لا ترغب في الاحتفاظ بكلمات مرور لداب التي تمت مزامنتها مع كلمات المرور المحلية. ويعني تعطيل هذا أن المستخدمين قد لا يتمكنون من تسجيل الدخول إذا تعذر الوصول إلى خادم لداب لسبب ما.',
    'ldap_username_field'       => 'حقل اسم المستخدم',
    'ldap_lname_field'          => 'الكنية',
    'ldap_fname_field'          => 'لداب الاسم الأول',
    'ldap_auth_filter_query'    => 'استعلام مصادقة لداب',
    'ldap_version'              => 'إصدار لداب',
    'ldap_active_flag'          => 'لداب العلم النشط',
    'ldap_activated_flag_help'  => 'This value is used to determine whether a synced user can login to Snipe-IT. <strong>It does not affect the ability to check items in or out to them</strong>, and should be the <strong>attribute name</strong> within your AD/LDAP, <strong>not the value</strong>. <br><br>If this field is set to a field name that does not exist in your AD/LDAP, or the value in the AD/LDAP field is set to <code>0</code> or <code>false</code>, <strong>user login will be disabled</strong>. If the value in the AD/LDAP field is set to <code>1</code> or <code>true</code> or <em>any other text</em> means the user can log in. When the field is blank in your AD, we respect the <code>userAccountControl</code> attribute, which usually allows non-suspended users to log in.',
    'ldap_emp_num'              => 'رقم موظف لداب',
    'ldap_email'                => 'بريد لداب',
    'ldap_test'                 => 'Test LDAP',
    'ldap_test_sync'            => 'Test LDAP Synchronization',
    'license'                   => 'ترخيص البرنامج',
    'load_remote_text'          => 'المخطوطات عن بعد',
    'load_remote_help_text'		=> 'هذا قنص إيت تثبيت يمكن تحميل البرامج النصية من العالم الخارجي.',
    'login'                     => 'Login Attempts',
    'login_attempt'             => 'Login Attempt',
    'login_ip'                  => 'IP Address',
    'login_success'             => 'Success?',
    'login_user_agent'          => 'User Agent',
    'login_help'                => 'List of attempted logins',
    'login_note'                => 'تسجيل الدخول ملاحظة',
    'login_note_help'           => 'اختيارياً تضمين بعض الجمل على شاشة تسجيل الدخول الخاصة بك، على سبيل المثال لمساعدة الناس الذين وجدوا أحد الأجهزة المفقودة أو المسروقة. يقبل هذا الحقل <a href="https://help.github.com/articles/github-flavored-markdown/">بتطبيق نمط الكتابة من Github</a>',
    'login_remote_user_text'    => 'خيارات تسجيل دخول المستخدم عن بعد',
    'login_remote_user_enabled_text' => 'تمكين تسجيل الدخول باستخدام الخانة الرئيسية للمستخدم عن بعد',
    'login_remote_user_enabled_help' => 'هذا الخيار تمكين المصادقة عبر رأس REMOTE_USER وفقاً ل "واجهة عبّارة الشائعة (rfc3875)"',
    'login_common_disabled_text' => 'تعطيل آليات المصادقة الأخرى',
    'login_common_disabled_help' => 'يعمل هذا الخيار على تعطيل آليات المصادقة الأخرى. ما عليك سوى تمكين هذا الخيار إذا كنت متأكدًا من أن تسجيل الدخول إلى المستخدم_عن_بعد يعمل بالفعل',
    'login_remote_user_custom_logout_url_text' => 'عنوان صفحة مخصص لتسجيل الخروج',
    'login_remote_user_custom_logout_url_help' => 'إذا تم توفير عنوان URL هنا، سيتم إعادة توجيه المستخدمين إلى عنوان URL هذا بعد تسجيل خروج المستخدم من Snipe-IT. هذا مفيد لإغلاق جلسات عمل المستخدم لموفر المصادقة بشكل صحيح.',
    'login_remote_user_header_name_text' => 'الاسم المستعار للمستخدم',
    'login_remote_user_header_name_help' => 'استخدم اسم المستعار المحدد بدلاً من REMOTE_USER',
    'logo'                    	=> 'شعار',
    'logo_print_assets'         => 'الاستخدام في الطباعة',
    'logo_print_assets_help'    => 'استخدم العلامة التجارية في قوائم الأصول القابلة للطباعة ',
    'full_multiple_companies_support_help_text' => 'تقييد المستخدمين (بما في ذلك المشرفون) المعينون للشركات إلى أصول شركاتهم.',
    'full_multiple_companies_support_text' => 'كامل دعم الشركات المتعددة',
    'show_in_model_list'   => 'إظهار في القوائم المنسدلة للنماذج',
    'optional'					=> 'اختياري',
    'per_page'                  => 'النتائج لكل صفحة',
    'php'                       => 'نسخة فب',
    'php_info'                  => 'PHP Info',
    'php_overview'              => 'PHP',
    'php_overview_keywords'     => 'phpinfo, system, info',
    'php_overview_help'         => 'PHP System info',
    'php_gd_info'               => 'يجب تثبيت فب-غ لعرض رموز قر، راجع تعليمات التثبيت.',
    'php_gd_warning'            => 'لم يتم تثبيت فب معالجة الصور و غ المساعد.',
    'pwd_secure_complexity'     => 'تعقيد كلمة المرور',
    'pwd_secure_complexity_help' => 'حدد أي قواعد تعقيد كلمة المرور التي ترغب في فرضها.',
    'pwd_secure_complexity_disallow_same_pwd_as_user_fields' => 'Password cannot be the same as first name, last name, email, or username',
    'pwd_secure_complexity_letters' => 'Require at least one letter',
    'pwd_secure_complexity_numbers' => 'Require at least one number',
    'pwd_secure_complexity_symbols' => 'Require at least one symbol',
    'pwd_secure_complexity_case_diff' => 'Require at least one uppercase and one lowercase',
    'pwd_secure_min'            => 'كلمة المرور الحد الأدنى من الأحرف',
    'pwd_secure_min_help'       => 'الحد الأدنى المسموح به هو 8',
    'pwd_secure_uncommon'       => 'منع كلمات المرور الشائعة',
    'pwd_secure_uncommon_help'  => 'سيؤدي ذلك إلى منع المستخدمين من استخدام كلمات المرور الشائعة من أعلى 10000 كلمة مرور يتم الإبلاغ عنها في حالات خرق.',
    'qr_help'                   => 'تمكين رموز قر أولا لتعيين هذا',
    'qr_text'                   => 'نص رمز الاستجابة السريعة',
    'saml'                      => 'SAML',
    'saml_title'                => 'Update SAML settings',
    'saml_help'                 => 'SAML settings',
    'saml_enabled'              => 'تمكين SAML',
    'saml_integration'          => 'الدمج مع نظام ادارة طلبات الزبائن',
    'saml_sp_entityid'          => 'معرف الكيان',
    'saml_sp_acs_url'           => 'رابط خدمة مستهلك الضمان (ACS)',
    'saml_sp_sls_url'           => 'رابط خدمة تسجيل الخروج الفردي (SLS)',
    'saml_sp_x509cert'          => 'شهادة عامة',
    'saml_sp_metadata_url'      => 'رابط بيانات التعريف',
    'saml_idp_metadata'         => 'بيانات تعريف هوية SAML',
    'saml_idp_metadata_help'    => 'يمكنك تحديد بيانات التعريف الشخصية الشخصية باستخدام عنوان URL أو ملف XML.',
    'saml_attr_mapping_username' => 'تعيين السمة - اسم المستخدم',
    'saml_attr_mapping_username_help' => 'سيتم استخدام اسم المعرف إذا كانت خرائط السمة غير محددة أو غير صالحة.',
    'saml_forcelogin_label'     => 'SAML Force Login',
    'saml_forcelogin'           => 'جعل SAML تسجيل الدخول الأساسي',
    'saml_forcelogin_help'      => 'يمكنك استخدام \'/login?nosaml\' للوصول إلى صفحة تسجيل الدخول العادية.',
    'saml_slo_label'            => 'تسجيل الخروج الفردي لSAML',
    'saml_slo'                  => 'إرسال طلب تسجيل الدخول إلى الهوية عند تسجيل الخروج',
    'saml_slo_help'             => 'سيؤدي هذا إلى إعادة توجيه المستخدم لأول مرة إلى تسجيل الخروج من الهوية الشخصية. اتركه دون تحديد إذا كانت الهوية الشخصية لا تدعم بشكل صحيح SP-SAML SLO.',
    'saml_custom_settings'      => 'إعدادات SAML المخصصة',
    'saml_custom_settings_help' => 'يمكنك تحديد إعدادات إضافية لمكتبة onelogin/php-saml. استخدمها على مسؤوليتك الخاصة.',
    'saml_download'             => 'Download Metadata',
    'setting'                   => 'ضبط',
    'settings'                  => 'إعدادات',
    'show_alerts_in_menu'       => 'عرض التنبيهات في القائمة العلوية',
    'show_archived_in_list'     => 'الأصول المحفوظة',
    'show_archived_in_list_text'     => 'عرض الأصول المحفوظة في قائمة "جميع الأصول"',
    'show_assigned_assets'      => 'إظهار الأصول المسندة إلى الأصول',
    'show_assigned_assets_help' => 'عرض الأصول التي تم تعيينها إلى الأصول الأخرى في عرض المستخدم -> الأصول، عرض المستخدم -> معلومات -> طباعة جميع المعينات و في الحساب -> عرض الأصول المعينة.',
    'show_images_in_email'     => 'إظهار الصور في رسائل البريد الإلكتروني',
    'show_images_in_email_help'   => 'قم بإلغاء تحديد هذا المربع إذا كان تثبيت Snipe-IT وراء شبكة VPN أو شبكة مغلقة ولن يتمكن المستخدمون خارج الشبكة من تحميل الصور التي يخدمها هذا التثبيت في رسائل البريد الإلكتروني الخاصة بهم.',
    'site_name'                 => 'اسم الموقع',
    'slack'                     => 'Slack',
    'slack_title'               => 'Update Slack Settings',
    'slack_help'                => 'Slack settings',
    'slack_botname'             => 'سلاك بوتنام',
    'slack_channel'             => 'قناة سلاك',
    'slack_endpoint'            => 'نقطة نهاية سلاك',
    'slack_integration'         => 'إعدادات سلاك',
    'slack_integration_help'    => 'التكامل الأسود اختياري، ولكن نقطة النهاية والقناة مطلوبة إذا كنت ترغب في استخدامها. لتكوين تكامل Slack ، يجب أولاً <a href=":slack_link" target="_new" rel="noopener">إنشاء ويب هوك وارد</a> على حساب Slack الخاص بك. انقر على زر <strong>اختبار التكامل السوداء</strong> لتأكيد أن إعداداتك صحيحة قبل الحفظ. ',
    'slack_integration_help_button'    => 'عند الانتهاء من حفظ معلومات Slack الخاصة بك، سوف يظهر زر الفحص.',
    'slack_test_help'           => 'اختبر ما إذا كان تكامل Slack الخاص بك قد تم تكوينه بشكل صحيح. لقد قمت بحفظ إعدادات SLACK الخاصة بك.',
    'snipe_version'  			=> 'قنص-إيت الإصدار',
    'support_footer'            => 'دعم روابط تذييل الصفحة ',
    'support_footer_help'       => 'تحديد من يرى الروابط إلى دليل معلومات الدعم للمستخدمين عن طريق القناصة',
    'version_footer'            => 'رقم الاصدار في التذييل ',
    'version_footer_help'       => 'حدد من سوف يرى إصدار Snipe-IT و رقم النسخة.',
    'system'                    => 'معلومات النظام',
    'update'                    => 'إعدادات التحديث',
    'value'                     => 'القيمة',
    'brand'                     => 'العلامات التجارية',
    'brand_keywords'            => 'footer, logo, print, theme, skin, header, colors, color, css',
    'brand_help'                => 'Logo, Site Name',
    'web_brand'                 => 'نوع العلامات التجارية للويب',
    'about_settings_title'      => 'حول الإعدادات',
    'about_settings_text'       => 'تتيح لك هذه الإعدادات تخصيص بعض جوانب التثبيت.',
    'labels_per_page'           => 'عدد التسميات لكل صفحة',
    'label_dimensions'          => 'أبعاد التسمية (بوصة)',
    'next_auto_tag_base'        => 'الزيادة التلقائية التالية',
    'page_padding'              => 'هوامش الصفحة (بوصة)',
    'privacy_policy_link'       => 'رابط سياسة الخصوصية',
    'privacy_policy'            => 'سياسة الخصوصية',
    'privacy_policy_link_help'  => 'إذا تم تضمين عنوان URL هنا، سيتم تضمين رابط لسياسة الخصوصية الخاصة بك في تذييل التطبيق وفي أي رسائل البريد الإلكتروني التي يرسلها النظام، وفقاً لهذا التقرير. ',
    'purge'                     => 'تطهير السجلات المحذوفة',
    'purge_deleted'             => 'Purge Deleted ',
    'labels_display_bgutter'    => 'الجزء السفلي للتسمية',
    'labels_display_sgutter'    => 'الجزء الجانبي للتسمية',
    'labels_fontsize'           => 'حجم خط التسمية',
    'labels_pagewidth'          => 'عرض ورقة التسميات',
    'labels_pageheight'         => 'ارتفاع ورقة التسميات',
    'label_gutters'        => 'تباعد التسميات (بوصة)',
    'page_dimensions'        => 'أبعاد الصفحة (بوصة)',
    'label_fields'          => 'الحقول المرئية للتسميات',
    'inches'        => 'بوصة',
    'width_w'        => 'ث',
    'height_h'        => 'ح',
    'show_url_in_emails'                => 'الرابط إلى Snipe-IT في رسائل البريد الإلكتروني',
    'show_url_in_emails_help_text'      => 'قم بإلغاء اختيار هذا المربع إذا كنت لا ترغب في الربط مرة أخرى إلى Snipe-IT في تذييل البريد الإلكتروني الخاص بك. تعد مفيدة إذا كان معظم المستخدمين لا يقومون بتسجيل الدخول. ',
    'text_pt'        => 'حزب العمال',
    'thumbnail_max_h'   => 'ماكس ارتفاع الصورة المصغرة',
    'thumbnail_max_h_help'   => 'الحد الأقصى للارتفاع بالبكسل الذي قد تعرضه الصور المصغرة في طريقة عرض بطاقة البيانات. الحد الأدنى 25، 500 كحد أقصى.',
    'two_factor'        => 'توثيق ذو عاملين',
    'two_factor_secret'        => 'رمز عاملين',
    'two_factor_enrollment'        => 'اثنان عامل التسجيل',
    'two_factor_enabled_text'        => 'تمكين عاملين',
    'two_factor_reset'        => 'إعادة تعيين سر عاملين',
    'two_factor_reset_help'        => 'سيؤدي هذا إلى إجبار المستخدم على تسجيل أجهزته باستخدام أداة مصادقة غوغل مرة أخرى. ويمكن أن يكون ذلك مفيدا إذا فقدت أو سرقت الجهاز المسجل حاليا.',
    'two_factor_reset_success'          => 'جهاز عاملين إعادة تعيين بنجاح',
    'two_factor_reset_error'          => 'أخفق إعادة تعيين عامل عامل اثنين',
    'two_factor_enabled_warning'        => 'سيؤدي تمكين عاملين إذا لم يتم تمكينه حاليا إلى إجبارك فورا على المصادقة باستخدام جهاز مسجل في غوغل أوث. سيكون لديك القدرة على تسجيل جهازك إذا كان أحد غير مسجل حاليا.',
    'two_factor_enabled_help'        => 'سيؤدي هذا إلى تشغيل المصادقة الثنائية باستخدام غوغل أوثنتيكاتور.',
    'two_factor_optional'        => 'انتقائي (يمكن للمستخدمين تمكين أو تعطيل إذا كان مسموحا به)',
    'two_factor_required'        => 'اجباري لجميع المستخدمين',
    'two_factor_disabled'        => 'معاق',
    'two_factor_enter_code'	=> 'أدخل رمز عاملين',
    'two_factor_config_complete'	=> 'إرسال الرمز',
    'two_factor_enabled_edit_not_allowed' => 'لا يسمح لك المشرف بتعديل هذا الإعداد.',
    'two_factor_enrollment_text'	=> "التوثيق ذو العاملين 2FA اجباري، ولكن لم يتم تسجيل جهازك بعد. افتح تطبيق غوغل للتوثيق Google Authenticator وافحص رمز الاستجابة السريعة QR أدناه لتسجيل جهازك. بعد تسجيل جهازك، أدخل الرمز أدناه",
    'require_accept_signature'      => 'يتطلب التوقيع',
    'require_accept_signature_help_text'      => 'سيتطلب تمكين هذه الميزة من المستخدمين تسجيل الدخول فعليا عند قبول مادة العرض.',
    'left'        => 'اليسار',
    'right'        => 'حق',
    'top'        => 'أعلى',
    'bottom'        => 'الأسفل',
    'vertical'        => 'عمودي',
    'horizontal'        => 'أفقي',
    'unique_serial'                => 'أرقام تسلسلية مميزة',
    'unique_serial_help_text'                => 'تحديد المربع سيؤدي الى فرض سياسة التفرد على الرقم التسلسلي للمتلكات',
    'zerofill_count'        => 'طول ترميز الأصل، بما في ذلك تعبئة الاصفار',
    'username_format_help'   => 'سيتم استخدام هذا الإعداد فقط من قبل عملية الاستيراد إذا لم يتم توفير اسم المستخدم ويتعين علينا إنشاء اسم مستخدم لك.',
    'oauth_title' => 'OAuth API Settings',
    'oauth' => 'OAuth',
    'oauth_help' => 'Oauth Endpoint Settings',
    'asset_tag_title' => 'Update Asset Tag Settings',
    'barcode_title' => 'Update Barcode Settings',
    'barcodes' => 'Barcodes',
    'barcodes_help_overview' => 'Barcode &amp; QR settings',
    'barcodes_help' => 'This will attempt to delete cached barcodes. This would typically only be used if your barcode settings have changed, or if your Snipe-IT URL has changed. Barcodes will be re-generated when accessed next.',
    'barcodes_spinner' => 'Attempting to delete files...',
    'barcode_delete_cache' => 'Delete Barcode Cache',
    'branding_title' => 'Update Branding Settings',
    'general_title' => 'Update General Settings',
    'mail_test' => 'Send Test',
    'mail_test_help' => 'This will attempt to send a test mail to :replyto.',
    'filter_by_keyword' => 'Filter by setting keyword',
    'security' => 'Security',
    'security_title' => 'Update Security Settings',
    'security_keywords' => 'password, passwords, requirements, two factor, two-factor, common passwords, remote login, logout, authentication',
    'security_help' => 'Two-factor, Password Restrictions',
    'groups_keywords' => 'permissions, permission groups, authorization',
    'groups_help' => 'Account permission groups',
    'localization' => 'Localization',
    'localization_title' => 'Update Localization Settings',
    'localization_keywords' => 'localization, currency, local, locale, time zone, timezone, international, internatinalization, language, languages, translation',
    'localization_help' => 'Language, date display',
    'notifications' => 'Notifications',
    'notifications_help' => 'Email alerts, audit settings',
    'asset_tags_help' => 'Incrementing and prefixes',
    'labels' => 'Labels',
    'labels_title' => 'Update Label Settings',
    'labels_help' => 'Label sizes &amp; settings',
    'purge' => 'Purge',
    'purge_keywords' => 'permanently delete',
    'purge_help' => 'Purge Deleted Records',
    'ldap_extension_warning' => 'It does not look like the LDAP extension is installed or enabled on this server. You can still save your settings, but you will need to enable the LDAP extension for PHP before LDAP syncing or login will work.',
    'ldap_ad' => 'LDAP/AD',
    'employee_number' => 'Employee Number',
    'create_admin_user' => 'Create a User ::',
    'create_admin_success' => 'Success! Your admin user has been added!',
    'create_admin_redirect' => 'Click here to go to your app login!',
    'setup_migrations' => 'Database Migrations ::',
    'setup_no_migrations' => 'There was nothing to migrate. Your database tables were already set up!',
    'setup_successful_migrations' => 'Your database tables have been created',
    'setup_migration_output' => 'Migration output:',
    'setup_migration_create_user' => 'Next: Create User',
    'ldap_settings_link' => 'LDAP Settings Page',
    'slack_test' => 'Test <i class="fab fa-slack"></i> Integration',
];
