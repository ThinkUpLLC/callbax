
ALTER TABLE cb_callbacks ADD  is_opted_out TINYINT( 1 ) NOT NULL DEFAULT  '0' 
COMMENT 'Whether or not the installation has opted out of usage tracking (1 if so, 0 if not.)';

ALTER TABLE cb_installations ADD  is_opted_out TINYINT( 1 ) NOT NULL DEFAULT  '0' 
COMMENT 'Whether or not the installation has opted out of usage tracking (1 if so, 0 if not.)';