CREATE TABLE IF NOT EXISTS cb_callbacks (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID',
  referrer varchar(255) NOT NULL COMMENT 'Full raw path of the referring installation',
  version varchar(10) NOT NULL COMMENT 'Version number installation queried about',
  last_seen timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last seen timestamp',
  is_opted_out tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the installation has opted out of usage tracking (1 if so, 0 if not.)',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Raw callbacks.';

CREATE TABLE IF NOT EXISTS cb_installations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID',
  url varchar(255) NOT NULL COMMENT 'Base URL of installation',
  version varchar(10) NOT NULL COMMENT 'Version installation is running',
  user_count int(11) NOT NULL COMMENT 'Total service users detected on this installation.',
  last_seen timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last seen timestamp',
  is_opted_out tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether or not the installation has opted out of usage tracking (1 if so, 0 if not.)',
  PRIMARY KEY (id),
  UNIQUE KEY url (url)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Installations';

CREATE TABLE IF NOT EXISTS cb_users (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID',
  installation_id int(11) NOT NULL COMMENT 'Installation ID',
  service varchar(20) NOT NULL COMMENT 'Service name like facebook, twitter, facebook page',
  username varchar(255) NOT NULL COMMENT 'Service username',
  follower_count int(11) NOT NULL COMMENT 'Total number of user followers/subscribers/friends.',
  first_seen timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'First seen timestamp',
  last_seen timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last seen timestamp',
  last_follower_count timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last time the user follower count was updated.',
  PRIMARY KEY (id),
  UNIQUE KEY user_installation (installation_id,service,username),
  KEY installation_id (installation_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Installation service users';
