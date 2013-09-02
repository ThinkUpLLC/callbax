DELETE FROM cb_callbacks;
ALTER TABLE  cb_callbacks ADD UNIQUE (referrer);