<?php

namespace App\implementation\services;

use Illuminate\Http\Request;
use App\Interfaces\services\iapifilterService;

class _apifilterRepositiory implements iapifilterService
{
    protected $safeparams = [
    ];
    protected $operatormap = [
        'eq' => '==',
        'ne' => '!=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
        'like' => 'like'
    ];
    protected $columnmap = [
    ];

    public function transform(Request $request)
    {
        $query = [];

        foreach($this->safeparams as $param=>$operators)
        {
            $value = $request->query($param);
            if(!isset($value))
            {
                continue;
            }
            $column = $this->columnmap[$param] ?? $param;
            foreach($operators as $operator)
            {
                if(isset($value[$operator]))
                {
                    $query[] = [$column, $this->operatormap[$operator], $value[$operator]];
                }
            }
        }
        return $query;
    }
}
