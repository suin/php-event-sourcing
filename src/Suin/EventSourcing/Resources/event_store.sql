CREATE TABLE `tbl_es_event_store` (
    `event_id` bigint(20) NOT NULL auto_increment,
    `event_body` varchar(65000) NOT NULL,
    `event_type` varchar(250) NOT NULL,
    `stream_name` varchar(250) NOT NULL,
    `stream_version` int(11) NOT NULL,
    KEY (`stream_name`),
    UNIQUE KEY (`stream_name`, `stream_version`),
    PRIMARY KEY (`event_id`)
) ENGINE=InnoDB;
