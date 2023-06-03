CREATE TABLE IF NOT EXISTS `user_session` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID Session DB',
  `session_id` bigint NULL DEFAULT NULL COMMENT 'ID session php',
  `user_id` bigint NULL DEFAULT NULL COMMENT 'ID user in system',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Date create',
  `lastactive_time` timestamp NULL DEFAULT NULL COMMENT 'Date last active time',
  `ip_address_create` char(255) NULL DEFAULT NULL COMMENT 'IP address at create',
  `ip_address_last` char(255) NULL DEFAULT NULL COMMENT 'IP address at now',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица для хранения акутального меня для юзера. В каком меню сейчас находится юзер

CREATE TABLE IF NOT EXISTS `menu_history_user` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID',
  `user_id` bigint NULL DEFAULT NULL COMMENT 'ID user in system',
  `menu_id` char(255) NULL DEFAULT NULL COMMENT 'ID menu user current',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `menu` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID Session DB',
  `menu_id` char(255) UNSIGNED AUTO_INCREMENT COMMENT 'ID menu DB',
  `menu_name` char(255) NULL DEFAULT NULL COMMENT 'Menu name in system',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID Session DB',
  `menu_id` char(255) NULL DEFAULT NULL COMMENT 'Menu ID in system',
  `item_name` char(255) NULL DEFAULT NULL COMMENT 'Item menu name in system',
  `action` char(255) NULL DEFAULT NULL COMMENT 'Что будет делать при нажатии',
  `action_data` char(255) NULL DEFAULT NULL COMMENT 'Доп данные для действия',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `trainings` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID training',
  `training_name` char(255) NULL DEFAULT NULL COMMENT 'Название тренинга',
  `description` text NULL DEFAULT NULL COMMENT 'Описание тренинга',
  `active` int(1) DEFAULT '0' COMMENT 'Активность тренинга',
  `date_create` int(11) DEFAULT '0' COMMENT 'Дата создания тренинга',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `generic_arguments` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID',
  `user_id` bigint NULL DEFAULT NULL COMMENT 'ID юзера',
  `chat_id` bigint NULL DEFAULT NULL COMMENT 'ID чата',
  `command` char(255) DEFAULT '0' COMMENT 'Команда',
  `argument` text DEFAULT NULL COMMENT 'Аргумент',
  `date_create` int(11) DEFAULT '0' COMMENT 'Дата создания аргумента',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID',
  `user_id` bigint NULL DEFAULT NULL COMMENT 'ID юзера',
  `sum` float NOT NULL COMMENT 'Сумма платежа в валюте сервиса',
  `payment_system` char(255) NULL COMMENT 'Название платежной системы',
  `date_create` datetime NOT NULL,
  `date_complete` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Новый 0, В процессе 1, Оплачен 2, Ошибка 3, Отменен 4, Завершен вручную 5',
  `payment_system_request` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Сохраняет запрос от платежной системы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_system_settings` (
  `id` bigint UNSIGNED AUTO_INCREMENT COMMENT 'ID',
  `mk_shop_id` char(255) NULL COMMENT 'Идентификатор ПС',
  `mk_secret_key` char(255) NULL COMMENT 'Секретный ключ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;