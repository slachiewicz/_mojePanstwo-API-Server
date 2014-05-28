<?php

class PanstwoInternetController extends AppController
{
	
	public function getTwitterStats()
    {

        $range = $this->request->params['id'];

        $data = $this->PanstwoInternet->get_twitter_stats($range);

        $this->set('data', $data);
        $this->set('_serialize', 'data');


    }
	
    public function getAnnualTwitterStats()
    {

        $year = $this->request->params['id'];

        $data = $this->PanstwoInternet->get_annual_twitter_stats($year);

        $this->set('data', $data);
        $this->set('_serialize', 'data');


    }

    public function getTwitterAccountsTypes()
    {

        $accounts = $this->PanstwoInternet->twitter_accounts_types();

        $this->set('accounts', $accounts);
        $this->set('_serialize', 'accounts');

    }

    public function getTwitterAccountsGroupByTypes()
    {

        $range = @$this->request->query['range'];
        $types = @$this->request->query['types'];
        $sort = @$this->request->query['sort'];

        if (is_array($types) && !empty($types)) {

            $accounts = $this->PanstwoInternet->twitter_accounts_group_by_types($range, $types, $sort);

            $this->set('accounts', $accounts);
            $this->set('_serialize', 'accounts');

        }


    }

    public function getTwitterTweetsGroupByTypes()
    {

        $range = @$this->request->query['range'];
        $types = @$this->request->query['types'];
        $sort = @$this->request->query['sort'];

        if (is_array($types) && !empty($types)) {

            $tweets = $this->PanstwoInternet->get_twitter_tweets_group_by_types($range, $types, $sort);

            $this->set('tweets', $tweets);
            $this->set('_serialize', 'tweets');

        }


    }

}