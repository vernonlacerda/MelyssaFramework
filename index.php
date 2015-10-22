<?php

// Startando sessions:
session_start();

// Setando timezone:
date_default_timezone_set("America/Sao_Paulo");

// Tamanho máximo permitido para upload de imagens:
ini_set('upload_max_filesize', 10000);

// Tempo máximo de execução dos scripts:
ini_set('max_execution_time', 1000);

// Pasta Raiz
define('BASE_PATH', dirname(__FILE__) . '/');

// Pasta da Applicação
define('APP_PATH', BASE_PATH . 'Application/');

// Pasta core
define('CORE_PATH', BASE_PATH . 'Core/');

// Qual a pasta de armazenamento do Core do Sistema (onde fica o núcleo do framework)?
define('VENDOR_PATH', CORE_PATH . 'Vendor/');

// Ambiente da aplicação (Development, Testing e Production)
define('ENVIRONMENT', 'Development');

// PHP mailer
require_once VENDOR_PATH . 'PhpMailer/class.phpmailer.php';

// Constantes do sistema:
require_once CORE_PATH . 'Configs/Constants.php';

// Autoloader:
require_once VENDOR_PATH . 'Melyssa/Bootstrap.php';

/*
* E agora sim, tudo ok, chamamos a função dispatch() para dar vida à todo o sistema:
*/
Melyssa\Bootstrap::dispatch();

/* Fim do arquivo: index.php */
/* Local: ./index.php */
