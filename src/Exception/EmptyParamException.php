<?php

namespace AmoCrm\Exception;

class EmptyParamException extends AmoException
{
    public function __construct($which = '') {
        parent::__construct(
            empty($which)
                ? 'Missing required parameter'
                : 'Missing required parameter: ' . $which
        );
    }
}