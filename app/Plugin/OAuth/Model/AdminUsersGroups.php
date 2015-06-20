<?php

App::uses('OAuthAppModel', 'OAuth.Model');

class AdminUsersGroups extends OAuthAppModel
{

    public function get($user_id) {
        return $this->getDataSource()->fetchAll("
            SELECT `admin_users_groups`.`name`
            FROM `admin_users_groups`
            JOIN `admin_user_group`
              ON `admin_user_group`.`admin_user_group_id` = `admin_users_groups`.`id`
            WHERE
              `admin_user_group`.`user_id` = ?",
            array($user_id)
        );
    }

}