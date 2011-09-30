
ALTER TABLE  cb_installations ADD  user_count INT NOT NULL COMMENT 'Total service users detected on this installation.' AFTER  version;

UPDATE cb_installations i SET user_count = (SELECT COUNT(*) FROM cb_users u WHERE u.installation_id = i.id);