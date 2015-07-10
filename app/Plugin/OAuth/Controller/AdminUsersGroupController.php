<?php

class AdminUsersGroupController extends OAuthAppController {

    public $uses = array('OAuth.AdminUsersGroups');

    public function get() {
        $data = $this->request->query;
        if(!isset($data['user_id'])) {
            throw new BadRequestException();
        }

        $groups = array();
        $results = $this->AdminUsersGroups->get($data['user_id']);
        foreach($results as $row) {
            $groups[] = $row['admin_users_groups']['name'];
        }

        $this->set(array(
            'groups' => $groups,
            '_serialize' => array('groups'),
        ));
    }

}