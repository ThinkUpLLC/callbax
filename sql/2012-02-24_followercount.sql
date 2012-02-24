ALTER TABLE  cb_users ADD  follower_count INT NOT NULL DEFAULT 0
COMMENT  'Total number of user followers/subscribers/friends.' AFTER  username;

ALTER TABLE  cb_users ADD  last_follower_count TIMESTAMP NOT NULL
COMMENT  'Last time the user follower count was updated.';

UPDATE cb_users SET last_follower_count='2010-01-01 01:00:00';