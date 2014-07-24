<?php

$api = array(
    "swaggerVersion" => "1.2",
    "apiVersion" => "1.0",
    "apis" => array(
        array(
            "path" => "[KRS/KrsApp/search]",
            "description" => "Podmioty w Krajowym Rejestrze Sądowym",
            "operations" => array(
                array(
                    "method" => "GET",
                    "summary" => "Znajdź podmiot",
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
    )
);