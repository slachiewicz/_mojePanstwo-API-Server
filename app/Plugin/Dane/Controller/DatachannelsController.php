<?

class DatachannelsController extends AppController
{
    public $uses = array('Dane.Datachannel', 'Dane.Dataset', 'Dane.Dataobject', 'Dane.Stream', 'Paszport.UserAdditionalData');

    public function info()
    {

        $alias = @addslashes($this->request->params['alias']);

        $datachannel = $this->Datachannel->find('first', array(
                'conditions' => array(
                    'Datachannel.slug' => $alias,
                ),
            )
        );

        $this->set('datachannel', $datachannel);
        $this->set('_serialize', array('datachannel'));
    }

    public function index()
    {
        $datachannels = $this->Datachannel->find('all', array(
                'contain' => array(
                    'Dataset' => array(
                        'fields' => array(
                            'id',
                            'alias',
                            'count',
                            'name',
                            'class',
                        ),
                    ),
                    'Dataset.Stream'
                )
            )
        );
        if (!$this->UserAdditionalData->hasPermissionToStream($this->stream_id)) {

        }
        foreach ($datachannels as $dkey => &$datachannel) {
            foreach ($datachannel['Dataset'] as $key => $dataset) {
                $found = false;
                foreach ($dataset['Stream'] as $stream) {
                    if ($stream['id'] == $this->stream_id) {
                        $found = true;
                    }
                }
                if (!$found) {
                    unset($datachannel['Dataset'][$key]);
                }
            }
            if (count($datachannel['Dataset']) < 1) {
                unset($datachannels[$dkey]);
            }
        }
        $this->set('datachannels', $datachannels);
        $this->set('_serialize', array('datachannels'));
    }

    public function search()
    {

        $alias = @addslashes($this->request->params['alias']);

        $queryData = $this->request->query;
        $queryData['conditions']['datachannel'] = $alias;

        if (isset($queryData['q'])) {
            $queryData['conditions']['q'] = $queryData['q'];
        }

        $search = $this->Dataobject->find('all', $queryData);

        $this->set('search', $search);
        $this->set('_serialize', array('search'));
    }

}