<?php
namespace Melyssa;

use Melyssa\Logger\Log;

class UserAgent
{
    private $agent;
    private $platforms; // array contendo todos os tipos suportados de plataformas;
    private $mobiles; // array contendo os mobile devices
    private $tablets; // array contendo os tablet devices
    private $browsers; // array contendo os browsers
    private $platform; // plataforma identificada:
    private $browser; // browser identificado:
    private $mobile; // mobile identificado:
    private $tablet; // tablet identificado:
    private $isBrowser = false;
    private $isMobile = false;
    private $isTablet = false;
    private static $instance = null;

    public function __construct()
    {
        // Carregando configurações:
        $this->loadStrings();
        // Indentificando a string do user agent utilizado:
        $this->parseUserAgent();
        // Setando plataforma em uso:
        $this->setPlatform();
        // Verificando se é um browser:
        $this->setBrowser();
        // Verificando se é um dispositivo móvel:
        $this->setMobile();
        // Verificando se é um tablet:
        $this->setTablet();
        // assinalando instância:
        self::$instance =& $this;
    }

    private function loadStrings()
    {
        $userAgent = array();
        if (is_dir(APP_PATH . '/Configs/') and is_file(APP_PATH . '/Configs/UserAgent.php')) {
            include(APP_PATH . '/Configs/UserAgent.php');
        }
        if (isset($userAgent['platforms']) and !empty($userAgent['platforms'])) {
            $this->platforms = $userAgent['platforms'];
        }
        if (isset($userAgent['browsers']) and !empty($userAgent['browsers'])) {
            $this->browsers = $userAgent['browsers'];
        }
        if (isset($userAgent['mobiles']) and !empty($userAgent['mobiles'])) {
            $this->mobiles = $userAgent['mobiles'];
        }
        if (isset($userAgent['tablets']) and !empty($userAgent['tablets'])) {
            $this->tablets = $userAgent['tablets'];
        }

        return true;
    }

    private function parseUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->agent = trim($_SERVER['HTTP_USER_AGENT']);
        } else {
            $this->agent = "Melyssa Framework";
        }
    }

    private function setPlatform()
    {
        $log = Log::getInstance();
        if (is_array($this->platforms) and count($this->platforms) > 0) {
            foreach ($this->platforms as $k => $v) {
                if (preg_match('|' .  preg_quote($k). '|i', $this->agent)) {
                    $this->platform = $v;
                    break;
                } else {
                    $this->platform = "Unknown platform";
                }
            }
        } else {
            $this->platform = "Unknown platform";
        }

        $log->debugMessage("Plataforma: " . $this->platform);
    }

    private function setBrowser()
    {
        if (is_array($this->browsers) and count($this->browsers) > 0) {
            foreach ($this->browsers as $k => $v) {
                if (preg_match('|' . preg_quote($k) . '.*?([0-9\.]+)|i', $this->agent)) {
                    $this->isBrowser = true;
                    $this->browser = $v;
                    break;
                } else {
                    $this->browser = "Unknown browser";
                }
            }
        } else {
            $this->browser = "Unknown browser";
        }
        $log = Log::getInstance();
        $log->debugMessage("Browser: " . $this->browser);
    }

    private function setMobile()
    {
        if (is_array($this->mobiles) and count($this->mobiles) > 0) {
            foreach ($this->mobiles as $k => $v) {
                if (preg_match('|' . preg_quote($k) . '|i', $this->agent)) {
                    $this->isMobile = true;
                    $this->mobile = $v;
                    break;
                } else {
                    $this->mobile = "Unknown mobile";
                }
            }
        } else {
            $this->mobile = "Unknown Mobile";
        }

        if ($this->isMobile) {
            $log = Log::getInstance();
            $log->debugMessage("Mobile: " . $this->mobile);
        }
    }

    private function setTablet()
    {
        if (is_array($this->tablets) and count($this->tablets) > 0) {
            foreach ($this->tablets as $k => $v) {
                if (preg_match('|' . preg_quote($k) . '|i', $this->agent)) {
                    $this->isTablet = true;
                    $this->tablet = $v;
                    break;
                } else {
                    $this->tablet = "Unknown tablet";
                }
            }
        } else {
            $this->tablet = "Unknown tablet";
        }

        if ($this->isTablet) {
            $log = Log::getInstance();
            $log->debugMessage("Tablet: " . $this->tablet);
        }
    }

    // Getters das propriedades:

    public function isBrowser()
    {
        return $this->isBrowser;
    }

    public function isMobile()
    {
        return $this->isMobile;
    }

    public function isTablet()
    {
        return $this->isTablet;
    }

    public static function &getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
