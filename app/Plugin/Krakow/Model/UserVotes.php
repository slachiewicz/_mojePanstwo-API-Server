<?php

App::uses('ConnectionManager', 'Model');

class UserVotes extends AppModel {

    public $useTable = 'krakow_user_druki_glosy';

    public function vote($druk_id, $user_id, $vote) {
        if(!in_array($vote, array(1, 2, 3)))
            return false;

        $id = (int) $this->DB->selectValue("SELECT id FROM krakow_user_druki_glosy WHERE druk_id = $druk_id AND user_id = $user_id");

        if($id) {
            $this->read(null, $id);
            $this->set('vote', $vote);
            return $this->save();
        } else {
            return $this->save(array(
                'user_id' => (int) $user_id,
                'druk_id' => (int) $druk_id,
                'vote' => (int) $vote,
                'vote_ts' => date('Y-m-d H:i:s')
            ));
        }
    }

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