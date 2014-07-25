<?php

$api = array(
    "swaggerVersion" => "1.2",
    "apiVersion" => "0.1",
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
        array('dataset' => 'krs_osoby', 'baseurl' => '/krs','subpath' => '/osoby', 'model' => 'KrsOsoba', 'description' => 'Wyszukuj osoby w KRS'),
        array('dataset' => 'krs_podmioty', 'baseurl' => '/krs','subpath' => '/podmioty', 'model' => 'KrsPodmiot', 'description' => 'Wyszukuj podmioty w KRS')
    )
);