<?php

namespace Lib\router;

class Request
{
    public array $inputs;

    public function __construct(array $inputs)
    {
        $temp = [];
        foreach ($inputs['GET'] as $k => $v)
            $temp[$k] = htmlspecialchars($v);
        $this->inputs['GET'] = $temp;

        $temp = [];
        foreach ($inputs['POST'] as $k => $v)
            $temp[$k] = htmlspecialchars($v);
        $this->inputs['POST'] = $temp;

        $this->inputs['FILES'] = $inputs['FILES'];
    }
}