<?php
use Swagger\Annotations as SWG;

/**
 * Mapowanie kodów pocztowych na adresy
 *
 * @SWG\Resource(
 *      resourcePath="/kodyPocztowe",
 *      basePath="http://mojepanstwo.pl/api1",
 *      apiVersion="1.0"
 * )
 *
 */
class AddressController extends AppController
{
    /**
     * Indexo
     *
     * @SWG\Api(
     *   basePath="http://mojepanstwo.pl/api2/",
     *   path="/kodyPocztowe/cities/{city_id}/addresses/",
     *   description="Kody pocztowe",
     *   @SWG\Operation(
     *      method = "GET",
     *      summary = "Znajdź kod pocztowy na podstawie adresu",
     *      type = "",
     *      nickname = "address2code",
     *
     *      @SWG\Parameter(
     *           name="city_id",
     *           description="ID of pet that needs to be fetched",
     *           paramType="path",
     *           required=true,
     *           type="string"
     *         ),
     *      @SWG\ResponseMessage(code=404, message="Pet not found"),
     *      @SWG\ResponseMessage(code=422, message="Validation failed", responseModel="ErrorModel")
     *   )
     * )
     *
     */
    public function index()
    {

        $city_id = @$this->request->params['city_id'];
        $street = @$this->request->query['street'];
        $limit = 10;

        $conditions = array(
            'miejscowosc_id' => $city_id,
        );

        if ($street) {
            $conditions['ulica LIKE'] = "%" . $street . "%";
            $limit = 30;
        }


        if ($city_id) {

            $search = $this->Address->find('all', array(
                'conditions' => $conditions,
                'limit' => $limit,
            ));
            $this->set('search', $search);
            $this->set('_serialize', array('search'));

        }

    }

} 