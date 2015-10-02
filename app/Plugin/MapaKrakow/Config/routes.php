<?php

Router::connect('/mapa_krakow/layers', array('plugin'=>'MapaKrakow','controller'=>'mapa','action'=>'layers'));

//Router::connect('/mapa_krakow/:action', array('plugin'=>'MapaKrakow','controller'=>'mapa',));