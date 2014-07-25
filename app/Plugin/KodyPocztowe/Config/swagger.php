<?

$api = array(
    "swaggerVersion" => "1.2",
    "apiVersion" => "1.0",
    "apis" => array(
        array(
            // In path one can use [plugin/controller/action/param1:val1,param2:val2]/pathparam1 that will be reverse-mapped by Router::url
            "path" => "[KodyPocztowe/KodyPocztowe/view/id:{postal_code}]",
            "description" => "Kody pocztowe",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Znajdź adresy objęte kodem pocztowym",
                    "nickname" => "code2address",
                    "type" => "PostalCode",
                    "parameters" => array(
                        array(
                            "paramType" => "path",
                            "name" => "postal_code",
                            "type" => "string",
                            "required" => true,
                            "description" => "Kod pocztowy w formacie [0-9]{2}-?[0-9]{3}"
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "layers",
                            "type" => "array",
                            "required" => false,
                            "description" => "Warstwy, które mają być załadowane dla obiektu. Można użyć żądania layers=*, aby załadować wszystkie warstwy",
                            "items" => array(
                                "type" => "string"
                            )
                        )
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne żądanie"
                        ),
                        array(
                            "code" => 404,
                            "message" => "Nie znaleziono kodu"
                        )
                    )
                )
            )
        ),
        // TODO implement
//        array(
//            "path" => "[KodyPocztowe/KodyPocztowe/address2code]",
//            "description" => "Kody pocztowe",
//            "operations" => array(
//                array(
//                    "method" => "GET",
//                    "summary" => "Znajdź kod pocztowy dla danego adresu",
//                    "nickname" => "address2code",
//                    "type" => "",
//                    "parameters" => array(
//                        array(
//                            "paramType" => "query",
//                            "name" => "q",
//                            "type" => "string",
//                            "required" => false,
//                            "description" => "Adres pełnym tekstem"
//                        )
//                    ),
//                    "responseMessages" => array(
//                        array(
//                            "code" => 400,
//                            "message" => "Niepoprawne żądanie"
//                        ),
//                        array(
//                            "code" => 404,
//                            "message" => "Nie znaleziono adresu"
//                        )
//                    )
//                )
//            )
//        )
    ),
    '_search_endpoints' => array(// TODO _search_baseurl zamienić na Router::url
        array('dataset' => 'kody_pocztowe', 'baseurl' => '/kodyPocztowe','subpath' => '', 'model' => 'PostalCode', 'description' => 'Wyszukuj kody pocztowe')
    )
);