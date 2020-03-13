CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(258) NOT NULL,
    `password` varchar(258) NOT NULL,
    `login` varchar(258) NOT NULL,
    `first_name` varchar(258) NOT NULL,
    `last_name` int(11) NOT NULL,
    `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`user_id`),
    UNIQUE KEY `email` (`email`)
);