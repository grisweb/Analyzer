<?php

namespace App\Service\Analyzer;

class Analyzer
{
    /**
     * @var array
     */
    protected array $parameters;

    /**
     * @var array
     */
    protected array $analyzerElements;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        foreach($parameters as $key => $value)
        {
            foreach($value as $token)
            {
                $this->parameters[$token] = $key;
            }
        }
    }


    /**
     * @param string $value
     * @return AnalyzerElement|null
     */
    protected function findValue(string $value): ?AnalyzerElement
    {
        foreach($this->analyzerElements as $item)
        {
            if($item->getValue() === $value)
            {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param array $lexerData
     * @return array|null
     */
    public function analyze(array $lexerData): ?array
    {
        $result = [];
        foreach($this->parameters as $key => $value)
        {
            $result[] = [$value => []];
        }

        $this->analyzerElements = [];

        foreach($lexerData as $item)
        {
            foreach($item as $token => $value)
            {
                if($analyzerElement = $this->findValue($value))
                {
                    $analyzerElement->setCount($analyzerElement->getCount() + 1);
                }
                else
                {
                    if(!empty($this->parameters[$token]))
                    {
                        $this->analyzerElements[] = $result[$this->parameters[$token]][] = new AnalyzerElement($token, $value);
                    }
                }
            }
        }

        return $result;
    }
}