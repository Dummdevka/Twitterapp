DROP TABLE IF EXISTS attachments;


CREATE TABLE IF NOT EXISTS attachments (
    id INT(6) unsigned NOT NULL AUTO_INCREMENT,
    tweet_id INT(11) NOT NULL,
    file_name VARCHAR(256) default '',
    url VARCHAR(256) default '',
    created_on TIMESTAMP default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_attachments_tweets_id`
    FOREIGN KEY (`tweet_id`) 
    REFERENCES `tweets` (`id`) 
    ON DELETE CASCADE ON UPDATE NO ACTION
)
