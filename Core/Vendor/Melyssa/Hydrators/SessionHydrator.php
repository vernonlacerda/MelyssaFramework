<?php

namespace Melyssa\Hydrators;

use Melyssa\Session;

class SessionHydrator extends Session
{
    private $data;
    private $prefix = null;

    public function __construct($from, $prefix = null)
    {
        parent::__construct();
        $this->data = $from;
        $this->prefix = $prefix;
        $this->hydrateValues();
    }

    private function hydrateValues()
    {
        foreach ($this->data as $key => $value) {
            if ($this->prefix !== null) {
                $this->makeSession($this->prefix . $key, $value);
            } else {
                $this->makeSession($key, $value);
            }
        }
    }
}
