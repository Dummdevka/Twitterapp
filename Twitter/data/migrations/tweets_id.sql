ALTER TABLE tweets
    ADD userid VARCHAR(40) NOT NULL
    AFTER id;