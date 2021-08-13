<?php

namespace App\Service;

use App\Service\Analyzer\Analyzer;
use App\Service\Lexer\Lexer;

class CppAnalyzer
{
    protected const DEFAULT_TOKENS = [
        'T_TAG' => '<(.*)>',
        'T_STRING' => '"([^"\\\\]*(?:\.[^"]*)*)"',
        'T_CAST' => '(long double|double|long|int|)\b',
        'T_VARIABLE' => '[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*\b(?!\()',
        'T_FUNCTION' => '[a-zA-Z_\\x7f-\\hhero][a-zA-Z0-9_\\x7f-\\xff]*\b',
        'T_FLOAT' => '([0-9]*\\.[0-9]+|[0-9]+\\.[0-9]*)(?:[eE][\\+\\-]?[0-9]+)?',
        'T_INT' => '(?:0|[1-9][0-9]*)(?:[eE][\\+\\-]?[0-9]+)?',
        'T_PLUS' => '\+',
        'T_MINUS' => '\-',
        'T_BIN_AND' => '&',
        'T_MUL' => '\*',
        'T_DIV' => '\/',
        'T_ASSIGN' => '=',
        'T_INCLUDE' => '\#include',
        'T_PARENTHESIS_OPEN' => '\\(',
        'T_PARENTHESIS_CLOSE' => '\\)',
        'T_BRACKET_OPEN' => '\\[',
        'T_BRACKET_CLOSE' => '\\]',
        'T_BRACE_OPEN' => '{',
        'T_BRACE_CLOSE' => '}',
        'T_SEMICOLON' => ';',
    ];

    protected const PARAMETERS = [
        'operators' => [
            'T_SEMICOLON',
            'T_CAST',
            'T_PLUS',
            'T_MINUS',
            'T_BIN_AND',
            'T_MUL',
            'T_DIV',
            'T_ASSIGN',
            'T_PARENTHESIS_OPEN',
            'T_PARENTHESIS_CLOSE',
            'T_BRACKET_OPEN',
            'T_BRACKET_CLOSE',
            'T_BRACE_OPEN',
            'T_BRACE_CLOSE',
            'T_FUNCTION',
        ],
        'operands' => [
            'T_STRING',
            'T_VARIABLE',
            'T_FLOAT',
            'T_INT',
        ],
    ];

    /**
     * @var array
     */
    protected array $operators;

    /**
     * @var array
     */
    protected array $operands;

    /**
     * @var string
     */
    protected string $htmlContent;

    public function parseCode(string $code): CppAnalyzer
    {
        $lexer = new Lexer(self::DEFAULT_TOKENS);
        $analyzer = new Analyzer(self::PARAMETERS);

        $lexerData = $lexer->lex($code);
        $this->htmlContent = $lexer->getHtmlContent();

        $analyzerData = $analyzer->analyze($lexerData);

        $this->operators = $analyzerData['operators'];
        $this->operands = $analyzerData['operands'];

        foreach($this->operators as $operator)
        {
            if($operator->getToken() === 'T_FUNCTION')
            {
                $operator->setValue($operator->getValue() . '()');

                foreach($this->operators as $item)
                {
                    if($item->getToken() === 'T_PARENTHESIS_OPEN' || $item->getToken() === 'T_PARENTHESIS_CLOSE')
                    {
                        $item->setCount($item->getCount() - $operator->getCount());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOperators(): array
    {
        return $this->operators;
    }

    /**
     * @return array
     */
    public function getOperands(): array
    {
        return $this->operands;
    }

    /**
     * @return int
     */
    public function getOperatorsCount(): int
    {
        $count = 0;

        foreach($this->operators as $operator)
        {
            $count += $operator->getCount();
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getOperandsCount(): int
    {
        $count = 0;

        foreach($this->operands as $operand)
        {
            $count += $operand->getCount();
        }

        return $count;
    }

    /**
     * @return string
     */
    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getVolume(): ?float
    {
        return (new HalsteadMetrics())->estimateVolume(count($this->getOperators()),
            count($this->getOperands()), $this->getOperatorsCount(), $this->getOperandsCount());
    }
}