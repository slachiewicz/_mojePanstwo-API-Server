<?php

/**
 * Class CodesController
 *
 *
 *
 */
class CodesController extends AppController
{
    /**
     * Viewo
     *
     *  @SWG\Resource(
     *      resourcePath="/kodyPocztowe",
     *
     * @SWG\Api(
     *   path="/kodyPocztowe/codes/{postal_code}",
     *   description="Kody pocztowe",
     *   @SWG\Operation(
     *      method = "GET",
     *      summary = "Znajdź adresy objęte kodem pocztowym",
     *      type = "",
     *      nickname = "code2address",
     *
     *      @SWG\Parameter(
     *           name="postal_code",
     *           description="ID of pet that needs to be fetched",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         ),
     *      @SWG\ResponseMessage(code=404, message="Pet not found"),
     *      @SWG\ResponseMessage(code=422, message="Validation failed", responseModel="ErrorModel")
     *   )
     * ))
     *
     */
    public function view()
    {

        $id = @$this->request->params['id'];
        $id = (int)str_replace(array('-', ' ', '.', ','), '', $id);

        $this->set('search', $this->Code->find('first', array(
            'conditions' => array(
                'kod_int' => $id,
            ),
            'fields' => array('id', 'kod'),
        )));
        $this->set('_serialize', array('search'));

    }
} 