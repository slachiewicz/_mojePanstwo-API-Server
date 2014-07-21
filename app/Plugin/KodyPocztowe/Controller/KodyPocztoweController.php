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
    public $uses = array('KodyPocztowe.Code');

    /**
     * @SWG\Api(
     *   path="[KodyPocztoweController/view]/{postal_code}",
     *   description="Kody pocztowe",
     *   @SWG\Operation(
     *      method = "GET",
     *      summary = "Znajdź adresy objęte kodem pocztowym",
     *      type = "",
     *      nickname = "code2address",
     *
     *      @SWG\Parameter(
     *           name="postal_code",
     *           description="Kod pocztowy w formacie [0-9]{2}-?[0-9]{3}",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         ),
     *      @SWG\ResponseMessage(code=404, message="Nie znaleziono kodu"),
     *   )
     * ))
     *
     */
    public function view()
    {
        $id = @$this->request->params['id'];
        $id = (int)str_replace('-', '', $id);

        $this->request->query['layers'];

        $code = $this->Code->find('first', array(
            'conditions' => array(
                'kod_int' => $id,
            ),
        ));

        $layer = $this->Dataobject->getObjectLayer($alias, $id, $layer, $params);

        $this->setSerialized('code', $code['Code']);
    }
}