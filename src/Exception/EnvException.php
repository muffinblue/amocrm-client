<?php

namespace AmoCrm\Exception;

class EnvException extends AmoException
{
    public function __construct($message) {
        parent::__construct($message);
    }
}