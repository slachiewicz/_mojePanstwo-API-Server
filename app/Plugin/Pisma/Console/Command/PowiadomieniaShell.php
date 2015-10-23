<?php
App::uses('CakeEmail', 'Network/Email');

class PowiadomieniaShell extends Shell
{
    public $uses = array('Pisma.Document');

    public function NotificationPastLoop()
    {
        while (1) {
            if (
            $pismo = $this->Document->find('first', array(
                'conditions' => array(
                    "sent = '1'",
                    "TIMESTAMPADD(DAY,14,`sent_at`)<NOW()",
                    "TIMESTAMPADD(DAY,21,`sent_at`)>'2015-10-22'",
                    "`from_user_type`='account'",
                    "`powiadomienie_termin`='0'",
                    "`name`='Wniosek o udostępnienie informacji publicznej'",

                ),
                'fields' => array('id', 'from_user_id', 'sent_at'),
                'order' => array('sent_at' => 'ASC')
            ))
            ) {

                $db = ConnectionManager::getDataSource('default');
                $user = $db->query("SELECT email FROM users WHERE id=" . $pismo['Document']['from_user_id'] . " LIMIT 1");


                $status = $this->Document->notify($user['email'], 'final');

                if ($status == true) {
                    $db->query("UPDATE `pisma_documents` SET `powiadomienie_termin`='1', `powiadomienie_termin_ts`=NOW() WHERE `alphaid`='" . addslashes($pismo['id']) . "'");
                }
                var_dump($status);
            }
            sleep(15);
        }
    }

    public function Notification2DaysLoop()
    {
        while (1) {
            if ($pismo = $this->Document->find('first', array(
                'conditions' => array(
                    "sent = '1'",
                    "TIMESTAMPADD(DAY,11,`sent_at`)<NOW()",
                    "TIMESTAMPADD(DAY,14,`sent_at`)>NOW()",
                    "`from_user_type`='account'",
                    "`powiadomienie_termin`='0'",
                    "`powiadomienie_zbliza`='0'",
                    "`name`='Wniosek o udostępnienie informacji publicznej'",

                ),
                'fields' => array('alphaid', 'from_user_id', 'sent_at'),
                'order' => array('sent_at' => 'ASC')
            ))
            ) {

                $db = ConnectionManager::getDataSource('default');
                $user = $db->query("SELECT email FROM users WHERE id=" . $pismo['Document']['from_user_id'] . " LIMIT 1");


                $status = $this->Document->notify($user['email'], '3dni');

                if ($status == true) {
                    $db->query("UPDATE `pisma_documents` SET `powiadomienie_zbliza`='1', `powiadomienie_2dni_ts`=NOW() WHERE `alphaid`='" . addslashes($pismo['id']) . "'");
                }
                var_dump($status);
            }
            sleep(15);
        }
    }
}