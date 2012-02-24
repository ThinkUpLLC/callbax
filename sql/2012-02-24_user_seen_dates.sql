ALTER TABLE cb_users ADD  first_seen timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'First seen timestamp';

UPDATE cb_users SET first_seen=now();

ALTER TABLE cb_users ADD  last_seen timestamp COMMENT 'Last seen timestamp'; 

UPDATE cb_users SET last_seen=now();