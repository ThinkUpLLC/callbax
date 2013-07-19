<?php
class CaptureFollowerCountsController {
    public function control() {
        $user_dao = new UserMySQLDAO();
        $cfg = Config::getInstance();

        $times_around_the_carousel = 4;
        while ($times_around_the_carousel > 0) {
            //get last 50 Twitter users whose follower count hasn't been updated in 30 days
            $users_to_update = $user_dao->getStaleFollowerCounts('twitter', 50);

            $usernames_to_update = array();
            foreach ($users_to_update as $user) {
                $usernames_to_update[] = $user->username;
            }
            $comma_delimited_users = implode(',', $usernames_to_update);

            $twitter_oauth_consumer_key = $cfg->getValue('twitter_oauth_consumer_key');
            $twitter_oauth_consumer_secret = $cfg->getValue('twitter_oauth_consumer_secret');
            $twitter_oauth_access_token = $cfg->getValue('twitter_oauth_access_token');
            $twitter_oauth_access_token_secret = $cfg->getValue('twitter_oauth_access_token_secret');

            $to = new TwitterOAuth($twitter_oauth_consumer_key, $twitter_oauth_consumer_secret, $twitter_oauth_access_token,
            $twitter_oauth_access_token_secret );

            $results = $to->get('https://api.twitter.com/1.1/users/lookup.json',
            array('screen_name'=>$comma_delimited_users));

            //update count and last update in database
            $total_successfully_fetched_counts = 0;
            $total_unsuccessfully_fetched_counts = 0;
            $updated_usernames = array();
            if (is_array($results) && isset($results[0]->followers_count)) {
                foreach ($results as $result) {
                    $user_dao->updateFollowerCount('twitter', $result->screen_name, $result->followers_count);
                    $updated_usernames[] = $result->screen_name;
                    $total_successfully_fetched_counts++;
                }
                foreach ($usernames_to_update as $username_to_check) {
                    if (!in_array($username_to_check, $updated_usernames)) {
                        //haven't gotten follower count, don't check again for 30 days
                        $user_dao->updateLastFollowerCount('twitter', $username_to_check);
                        $total_unsuccessfully_fetched_counts++;
                    }
                }
                echo "
Callbax: Successfully fetched updated follower counts for ".$total_successfully_fetched_counts.
                " Twitter user(s), with ".$total_unsuccessfully_fetched_counts." not fetched.

";
            } else if (isset($results->error)) {
                echo $results->error;
            }
            $times_around_the_carousel--;
        }
    }
}
