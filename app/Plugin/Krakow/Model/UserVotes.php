<?php

App::uses('ConnectionManager', 'Model');

class UserVotes extends AppModel {

    public $useTable = 'krakow_user_druki_glosy';

    public function getVotes($druk_id, $user_id) {
        App::import('model','DB');
        $this->DB = new DB();
        $druk_id = (int) $druk_id;

        $votes = $this->DB->selectAssocs("
            SELECT COUNT(*) as `count`, vote FROM krakow_user_druki_glosy WHERE druk_id = $druk_id GROUP BY vote
        ");

        $user = (int) $this->DB->selectValue("SELECT vote FROM krakow_user_druki_glosy WHERE druk_id = $druk_id AND user_id = $user_id");

        return array(
            'votes' => $votes,
            'user' => $user
        );
    }

}