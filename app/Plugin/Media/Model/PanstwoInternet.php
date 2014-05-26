<?php

class PanstwoInternet extends AppModel
{

    public $useTable = false;

    public function get_annual_twitter_stats($year)
    {

        $file = 'http://admin.sejmometr.pl/_resources/twitter/stats/2013.json';
        $data = @json_decode(file_get_contents($file), true);
        return $data;

    }

    public function twitter_accounts_types()
    {

        $result = $this->query("SELECT `id`, `nazwa`, `class` FROM `twitter_accounts_types` WHERE `ranking`='1' ORDER BY `ranking_ord` ASC");
        foreach ($result as &$r)
            $r = $r['twitter_accounts_types'];

        return $result;

    }

    public function twitter_accounts_group_by_types($types, $order)
    {

        foreach ($types as &$t) {

            $t = array(
                'id' => $t,
                'search' => ClassRegistry::init('Dane.Dataobject')->find('all', array(
                        'conditions' => array(
                            'dataset' => 'twitter_accounts',
                            'typ_id' => $t,
                        ),
                        'order' => $order,
                        'limit' => 3,
                    )),
            );

        }

        return $types;

    }

    public function get_twitter_tweets_group_by_types($types, $order)
    {

        foreach ($types as &$t) {

            $t = array(
                'id' => $t,
                'search' => ClassRegistry::init('Dane.Dataobject')->find('all', array(
                        'conditions' => array(
                            'dataset' => 'twitter',
                            'twitter_accounts.typ_id' => $t,
                            '!bez_retweetow' => '1',
                            'czas_utworzenia' => '2013-*',
                        ),
                        'order' => $order,
                        'limit' => 3,
                    )),
            );

        }

        return $types;

    }

} 