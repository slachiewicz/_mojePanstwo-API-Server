<?

$api = array(
    "swaggerVersion" => "1.2",
    "apiVersion" => "1.0",
    "resourcePath" => "/kodyPocztowe",
    "apis" => array(
        array(
            "path" => "[KodyPocztowe/KodyPocztowe/address2code]",
            "description" => "Kody pocztowe",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Znajd\u017a kod pocztowy dla danego adresu",
                    "nickname" => "address2code",
                    "type" => "",
                    "parameters" => array(
                        array(
                            "paramType" => "query",
                            "name" => "q",
                            "type" => "string",
                            "required" => false,
                            "description" => "Adres pe\u0142nym tekstem"
                        )
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne \u017c\u0105danie"
                        ),
                        array(
                            "code" => 404,
                            "message" => "Nie znaleziono adresu"
                        )
                    )
                )
            )
        ),
        array(
            "path" => "[KodyPocztowe/KodyPocztowe/view/id:{postal_code}]",
            "description" => "Kody pocztowe",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Znajd\u017a adresy obj\u0119te kodem pocztowym",
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
                            "description" => "Warstwy, kt\u00f3re maj\u0105 by\u0107 za\u0142adowane dla obiektu. Mo\u017cna u\u017cy\u0107 \u017c\u0105dania layers=*, aby za\u0142adowa\u0107 wszystkie warstwy",
                            "items" => array(
                                "type" => "string"
                            )
                        )
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne \u017c\u0105danie"
                        ),
                        array(
                            "code" => 404,
                            "message" => "Nie znaleziono kodu"
                        )
                    )
                )
            )
        )
    )
);