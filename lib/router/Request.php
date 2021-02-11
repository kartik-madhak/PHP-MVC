<?php

namespace Lib\router;

class Request
{
    public array $inputs;

    public function __construct(array $inputs)
    {
        foreach ($inputs['GET'] as $k => $v)
            $inputs[$k] = htmlspecialchars($v);
        foreach ($inputs['POST'] as $k => $v)
            $inputs[$k] = htmlspecialchars($v);
        $this->inputs = $inputs;
    }
}