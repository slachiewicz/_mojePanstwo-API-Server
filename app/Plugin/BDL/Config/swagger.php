<?

$api = array(
    "swaggerVersion" => "1.2",
    // "apiVersion" => CHANGE IN DB
    "apis" => array(
        array(
            // In path one can use [plugin/controller/action/param1:val1,param2:val2]/pathparam1 that will be reverse-mapped by Router::url
            "path" => "[BDL/BDL/tree]",
            "description" => "Drzewo kategorii wskaźników",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Drzewo kategorii wskaźników",
                    "nickname" => "tree",
                    "type" => "Tree",
                    "parameters" => array(),
                    "responseMessages" => array(
                        array(
                            "code" => 200,
                            "message" => "Drzewo kategorii wskaźników"
                        ),
                    )
                )
            )
        ),
        array(
            "path" => "[BDL/BDL/search]",
            "description" => "Wyszukuj wskaźniki",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Wyszukuj wskaźniki w grupach i kategoriach",
                    "nickname" => "search",
                    "type" => "array",
                    "parameters" => array(
                        array(
                            "paramType" => "query",
                            "name" => "q",
                            "type" => "string",
                            "required" => true,
                            "description" => "Wyszukiwana fraza"
                        )
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne żądanie, brak parametru q"
                        ),
                        array(
                            "code" => 200,
                            "message" => "Odpowiedź"
                        )
                    )
                )
            )
        ),
        array(
            "path" => "[BDL/BDL/series]",
            "description" => "Zwróć dane serii",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Dane serii dla wybranych wskaźników",
                    "nickname" => "series",
                    "type" => "array",
                    "parameters" => array(
                        array(
                            "paramType" => "query",
                            "name" => "metric_id",
                            "type" => "integer",
                            "required" => true,
                            "description" => "Id wskaźnika, do którego mają należeć serie"
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "slice",
                            "type" => "string",
                            "required" => false,
                            "description" => "Tablica id wymiarów, dla których przecięcia zostaną zwrócone dane. Format: [1,34,*]. Uzyj gwiazdki, aby zwrócić serie dla wszystkich punktów wymiaru. Brak argumentów oznacza wszystkie możliwe przecięcia."
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "time_range",
                            "type" => "string",
                            "required" => false,
                            "description" => "Podaj rok lub zakres (np. 2000:2010), z którego zostaną zwrócone dane. Brak argumentu oznacza pełen dostępny przedział."
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "wojewodztwo_id",
                            "type" => "integer",
                            "required" => false,
                            "description" => "Id województwa, dla którego zostaną zwrócone dane lub * dla wszystkich"
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "powiat_id",
                            "type" => "integer",
                            "required" => false,
                            "description" => "Id powiatu, dla którego zostaną zwrócone dane lub * dla wszystkich"
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "gmina_id",
                            "type" => "integer",
                            "required" => false,
                            "description" => "Id gminy, dla którego zostaną zwrócone dane lub * dla wszystkich"
                        ),
                        array(
                            "paramType" => "query",
                            "name" => "meta",
                            "type" => "integer",
                            "required" => false,
                            "description" => "Czy zwrać metadane odpowiedzi? Wartości: 0/1-domyślne"
                        )
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne żądanie"
                        ),
                        array(
                            "code" => 200,
                            "message" => "Dane wielu serii"
                        )
                    )
                )
            )
        )    ),
//    '_search_endpoints' => array(// TODO _search_baseurl zamienić na Router::url
//        array('dataset' => 'kody_pocztowe', 'baseurl' => '/kody_pocztowe','subpath' => '', 'model' => 'PostalCode', 'description' => 'Wyszukuj kody pocztowe')
//    )
);