<?php

$api = array(
    "swaggerVersion" => "1.2",
    "apiVersion" => "1.0",
    "apis" => array(
        array(
            "path" => "[KRS/KrsApp/search_api]",
            "description" => "Podmioty w Krajowym Rejestrze Sądowym",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Znajdź osobę lub  podmiot",
                    "nickname" => "search",
                    "type" => "array[KrsOsoba|KrsPodmiot]",
                    "parameters" => array(
                        array(
                            "paramType" => "query",
                            "name" => "q",
                            "required" => "true",
                            "type" => "string",
                            "description" => "Nazwa osoby lub podmiotu"
                        ),
                    ),
                    "responseMessages" => array(
                        array(
                            "code" => 400,
                            "message" => "Niepoprawne żądanie"
                        )
                    )
                )
            )
        ),
    ),
    '_search_endpoints' => array(// TODO _search_baseurl zamienić na Router::url
        array('_search_dataset' => 'krs_osoby', '_search_baseurl' => '/krs','_search_subpath' => '/osoby', '_search_model' => 'KrsOsoba'),
        array('_search_dataset' => 'krs_podmioty', '_search_baseurl' => '/krs','_search_subpath' => '/podmioty', '_search_model' => 'KrsPodmiot')
    )
);