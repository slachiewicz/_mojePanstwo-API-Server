<?php

/**
 * Mapowanie kodów pocztowych na adresy
 *
 * @SWG\Resource(
 *      resourcePath="/kodyPocztowe",
 *      apiVersion="1.0"
 * )
 *
 */
class KodyPocztoweController extends AppController
{
    public $uses = array('Dane.Dataobject');

    /**
     * @SWG\Api(
     *   path="[KodyPocztowe/KodyPocztowe/view/id:{postal_code}]",
     *   description="Kody pocztowe",
     *   @SWG\Operation(
     *      method = "GET",
     *      summary = "Znajdź adresy objęte kodem pocztowym",
     *      type = "PostalCode",
     *      nickname = "code2address",
     *
     *      @SWG\Parameter(
     *           name="postal_code",
     *           description="Kod pocztowy w formacie [0-9]{2}-?[0-9]{3}",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         ),
     *      @SWG\ResponseMessage(code=400, message="Niepoprawne żądanie"),
     *      @SWG\ResponseMessage(code=404, message="Nie znaleziono kodu")
     *   )
     * )
     *
     */
    public function view()
    {
        $id = @$this->request->params['id'];
        $id = (int)str_replace('-', '', $id);

        $response = $this->Dataobject->find('all', array(
            'conditions' => array(
                'dataset' => 'kody_pocztowe',
                'kod_int' => $id
            )
        ));

        if (!isset($response['dataobjects']) && empty($response['dataobjects'])) {
            throw new NotFoundException();
        }

        $this->setSerialized('code', $response['dataobjects'][0]);
    }

    /**
     * @SWG\Api(
     *   path="[KodyPocztowe/KodyPocztowe/address2code]",
     *   description="Kody pocztowe",
     *   @SWG\Operation(
     *      method = "GET",
     *      summary = "Znajdź kod pocztowy dla danego adresu",
     *      type = "",
     *      nickname = "address2code",
     *
     *      @SWG\Parameter(
     *           name="q",
     *           description="Adres pełnym tekstem",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *      @SWG\ResponseMessage(code=400, message="Niepoprawne żądanie"),
     *      @SWG\ResponseMessage(code=404, message="Nie znaleziono adresu")
     *   )
     * )
     *
     */
    public function address2code() {
        // pl_kody_pocztowe_pna
//        'fields' => array(
//            'Address.id',
//            'Address.nazwa',
//            'Address.ulica',
//            'Address.numery',
//            'Address.kod_id',
//            'Address.kod',
//        ),
//            'order' => array('ulica ASC', 'numery ASC')
        // TODO

        $this->setSerialized('code', false);
    }
}