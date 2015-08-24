<?php

/**
 * @property TwitterAccountSuggestion TwitterAccountSuggestion
 */
class TwitterController extends AppController {

    public $uses = array('Media.TwitterAccountSuggestion', 'Media.TwitterAccount');

    public function suggestNewAccount()
    {
        $this->request->data['user_id'] =
            (int) $this->Auth->user('type') == 'account' ?
                $this->Auth->user('id') :
                0;

        try {

            if($this->TwitterAccount->find('count', array(
                'conditions' => array(
                    'TwitterAccount.twitter_name' => @$this->request->data['name']
                )
            )))
                throw new Exception('Konto o podanej nazwie już istnieje');

            $this->set('response', $this->TwitterAccountSuggestion->suggest($this->request->data));
            $this->set('_serialize', 'response');
        } catch (Exception $e) {
            $this->set('error', $e->getMessage());
            $this->set('_serialize', 'error');
        }
    }

}