<?php
class CaptureFollowerCountsController {
    public function control() {
        $user_dao = new UserMySQLDAO();

        //get last 100 Twitter users whose follower count hasn't been updated in 30 days
        $users_to_update = $user_dao->getStaleFollowerCounts('twitter', 150);

        $returned_error = false;
        $total_successfully_fetched_counts = 0;
        $total_rate_limit_errors = 0;
        foreach ($users_to_update as $user) {
            if (!$returned_error) {
                //get count from Twitter
                $result = json_decode(self::getURLContents('https://api.twitter.com/1/users/lookup.json?screen_name='.
                $user->username));
                //update count and last update in database
                if (is_array($result) && isset($result[0]->followers_count)) {
                    $user_dao->updateFollowerCount('twitter', $user->username, $result[0]->followers_count);
                    $total_successfully_fetched_counts++;
                } else if (isset($result->error) || isset($result->errors)) {
                    $user_dao->updateLastFollowerCount('twitter', $user->username);
                    if (isset($result->errors[0]->code) && $result->errors[0]->code != '34') {
                        $returned_error = true;
                        echo "Error getting https://api.twitter.com/1/users/lookup.json?screen_name=". $user->username."
";
                        print_r($result);
                    } elseif (isset($result->error)
                    && $result->error == 'Rate limit exceeded. Clients may not make more than 150 requests per hour.') {
                        $returned_error = true;
                        $total_rate_limit_errors++;
                    }
                }
            }
        }
        echo "Callbax: Successfully fetched updated follower counts for ".$total_successfully_fetched_counts.
        " Twitter user(s). ".$total_rate_limit_errors. " rate limit error(s) encountered.
";
        return "";
    }

    public static function getURLContents($URL) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }
}
