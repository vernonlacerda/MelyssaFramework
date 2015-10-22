<?php

// Startando sessions:
session_start();

// Setando timezone:
date_default_timezone_set("America/Sao_Paulo");

// Tamanho máximo permitido para upload de imagens:
ini_set('upload_max_filesize', 10000);

// Tempo máximo de execução dos scripts:
ini_set('max_execution_time', 1000);

// Qual a pasta de armazenamento do Core do Sistema (onde fica o núcleo do framework)?

define('VENDOR_PATH', 'Core/Vendor/');

// Qual a pasta de armazenamento da sua aplicação (onde ficam os controllers, models, views e etc.)?

define('APP_PATH', 'Application/');

// Ambiente da aplicação (Development, Testing e Production)

define('ENVIRONMENT', 'Development');

// PHP mailer

require_once VENDOR_PATH . 'PhpMailer/class.phpmailer.php';

// Constantes do sistema:

require_once ( 'Core/Configs/Constants.php' );

// Autoloader:

require_once ( 'Core/Vendor/Melyssa/Bootstrap.php' );

/*
 * E agora sim, tudo ok, chamamos a função dispatch() para dar vida à todo o sistema:
 */
Melyssa\Bootstrap::dispatch();

/* Fim do arquivo: index.php */
/* Local: ./index.php */