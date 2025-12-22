-- Table des accès aux sondages
CREATE TABLE IF NOT EXISTS `survey_access` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `form_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `access_type` ENUM('view','answer','admin') NOT NULL DEFAULT 'answer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_form_user` (`form_id`,`user_id`),
  CONSTRAINT `fk_access_form` FOREIGN KEY (`form_id`) REFERENCES `form`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_access_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
