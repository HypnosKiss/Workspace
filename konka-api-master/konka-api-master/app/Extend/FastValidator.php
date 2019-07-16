<?php

namespace App\Extend;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class FastValidator
{
    use ValidatesAttributes;

    protected $message;

    protected $dataTable;

    protected $rules;

    protected $data;

    public function __construct(array &$dataTable, array $rules)
    {
        $this->dataTable = $dataTable;
        $this->resolveRules($rules);
    }

    public static function make(array &$dataTable, array $rules)
    {
        return new self($dataTable, $rules);
    }

    protected function resolveRules(array $rules)
    {
        foreach ($rules as $attribute => $ruleGroup) {
            if (is_string($ruleGroup)) {
                $ruleGroup = explode('|', $ruleGroup);
            }
            if (!is_array($ruleGroup)) {
                continue;
            }
            $nullAble = false;
            if (($pos = array_search('nullable', $ruleGroup)) !== false) {
                unset($ruleGroup[$pos]);
                $nullAble = true;
            }
            foreach ($ruleGroup as $rule) {
                list($method, $parameters) = (strpos($rule, ':') === false ? [$rule, ''] : explode(':', $rule));
                \Log::info('rule explode', [
                    'rule' => $rule,
                    'method' => $method,
                    'parameters' => $parameters,
                ]);
                $this->rules[] = [
                    'attribute' => $attribute,
                    'method' => $method,
                    'parameters' => explode(',', $parameters),
                    'message' => $attribute . ' ' . title_case($method),
                    'nullAble' => $nullAble
                ];
            }
        }
    }

    /**
     * @return MessageBag
     * @throws \Exception
     */

    public function errors()
    {
        if ($this->message === null) {
            $this->message = new MessageBag();
            foreach ($this->dataTable as $row => $item) {
                $this->data = $item;
                \Log::info('run one row start', ['data' => $item]);
                foreach ($this->rules as $rule) {
                    $method = 'validate' . studly_case($rule['method']);
                    if (!method_exists($this, $method)) {
                        throw new \Exception('fast validator {' . $method . '} method not found');
                    }
                    if ($rule['nullAble'] === true) {
                        if (!empty($item[$rule['attribute']]) && !$this->{$method}($rule['attribute'], $item[$rule['attribute']], $rule['parameters'])) {
                            $this->message->add($row . '.' . $rule['attribute'], $rule['message']);
                        }
                        continue;
                    }
                    if (!$this->{$method}($rule['attribute'], $item[$rule['attribute']], $rule['parameters'])) {
                        $this->message->add($row . '.' . $rule['attribute'], $rule['message']);
                    }
                }
                \Log::info('run one row end');
            }
        }
        return $this->message;
    }
}
