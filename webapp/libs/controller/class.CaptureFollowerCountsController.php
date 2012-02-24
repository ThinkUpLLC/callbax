<?php
class CaptureFollowerCountsController {
    public function control() {
        $user_dao = new UserMySQLDAO();

        //get last 100 Twitter users whose follower count hasn't been updated in 30 days
        $users_to_update = $user_dao->getStaleFollowerCounts('twitter', 100);

        foreach ($users_to_update as $user) {
            //get count from Twitter
            $result = json_decode(self::getURLContents('https://api.twitter.com/1/users/lookup.json?screen_name='.
            $user->username));
            //update count and last update in database
            if (is_array($result) && isset($result[0]->followers_count)) {
                $user_dao->updateFollowerCount('twitter', $user->username, $result[0]->followers_count);
            }
        }
        return '';
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
