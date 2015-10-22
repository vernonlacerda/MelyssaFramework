<?php

$routes = array(
    'default-controller' => 'Welcome',
    'default-action' => 'index',

    'Welcome' => array(
        'callables' => array(
            'index' => array(
                'methods' => array('GET'),
            ),
        ),
    ),
);
