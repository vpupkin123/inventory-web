<?php
return [
    // Navigation
    'nav.dashboard' => 'Панель управления',
    'nav.computers' => 'Компьютеры',
    'nav.users' => 'Пользователи',
    'nav.reports' => 'Отчеты',
    'nav.logout' => 'Выйти',
    'nav.logged_in_as' => 'Вы вошли как',
    'nav.language' => 'Язык',

    // Login
    'login.title' => 'Вход в систему',
    'login.login' => 'Логин',
    'login.password' => 'Пароль',
    'login.submit' => 'Войти',
    'login.error_invalid' => 'Неверный логин или пароль',
    'login.error_empty' => 'Введите логин и пароль',
    'login.error_access_denied' => 'Доступ запрещен',

    // Change password
    'change_password.title' => 'Смена пароля',
    'change_password.description' => 'Необходимо сменить пароль перед продолжением работы.',
    'change_password.new_password' => 'Новый пароль',
    'change_password.confirm_password' => 'Подтверждение пароля',
    'change_password.submit' => 'Сменить пароль',
    'change_password.error_empty' => 'Заполните все поля',
    'change_password.error_mismatch' => 'Пароли не совпадают',
    'change_password.error_short' => 'Пароль должен содержать минимум 6 символов',
    'change_password.error_failed' => 'Не удалось сменить пароль',
    'change_password.success' => 'Пароль успешно изменен',

    // Dashboard
    'dashboard.welcome' => 'Добро пожаловать',
    'dashboard.your_role' => 'Ваша роль',
    'dashboard.next_steps' => 'Это панель управления. Следующие шаги:',
    'dashboard.upload_reports' => 'Загрузка JSON отчетов',
    'dashboard.view_computers' => 'Просмотр списка компьютеров',
    'dashboard.manage_users' => 'Управление пользователями (только администратор)',

    // Reports
    'report.upload_title' => 'Загрузка отчета',
    'report.list_title' => 'Загруженные отчеты',
    'report.select_file' => 'Выберите JSON файл',
    'report.upload_button' => 'Загрузить',
    'report.upload_new' => 'Загрузить новый отчет',
    'report.no_reports' => 'Отчеты еще не загружены.',
    'report.file_name' => 'Имя файла',
    'report.uploaded_by' => 'Загрузил',
    'report.uploaded_at' => 'Дата загрузки',
    'report.error_no_file' => 'Файл не выбран или ошибка загрузки.',
    'report.error_invalid_format' => 'Неверный формат файла. Допустимы только JSON.',
    'report.error_duplicate' => 'Этот файл уже был загружен.',
    'report.error_processing' => 'Ошибка обработки файла',
    'report.success_uploaded' => 'Успешно загружено. Компьютеров добавлено на склад: :count',

    // Common
    'common.save' => 'Сохранить',
    'common.cancel' => 'Отмена',
    'common.delete' => 'Удалить',
    'common.edit' => 'Изменить',
    'common.back' => 'Назад',
    'common.error_404' => '404 Страница не найдена',
    'common.error_403' => '403 Доступ запрещен',
    'common.error_403_message' => 'У вас нет прав для доступа к этой странице.',

    // Navigation
    'nav.processing' => 'Обработка',

    // Processing
    'processing.title' => 'Очередь обработки компьютеров',
    'processing.empty_queue' => 'Нет компьютеров, ожидающих обработки.',
    'processing.computer_name' => 'Имя ПК',
    'processing.hardware' => 'Железо',
    'processing.reported_by' => 'От кого',
    'processing.comment' => 'Комментарий',
    'processing.create_user' => 'Создать пользователя',
    'processing.process_selected' => 'Обработать выбранные',
    'processing.no_selected' => 'Не выбрано ни одного компьютера.',
    'processing.success' => 'Компьютеры успешно обработаны.',
    'processing.error' => 'Ошибка при обработке компьютеров',
];