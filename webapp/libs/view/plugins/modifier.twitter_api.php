<?php
function smarty_modifier_twitter_api($version) {
    return (version_compare($version, '1.3', ">=") )?'green':'red';
}
