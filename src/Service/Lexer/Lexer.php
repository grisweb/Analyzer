<?php

namespace App\Service\Lexer;

class Lexer
{
    /**
     * @var array<string>
     */
    protected array $tokens = [];


    /**
     * @var string
     */
    protected string $htmlContent = '';

    /**
     * @param array<string> $tokens
     */
    public function __construct(array $tokens)
    {
        foreach ($tokens as $name => $definition) {
            $this->tokens[] = sprintf('(?<%s>%s)', $name, $definition);
        }
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    /**
     * @param string $sources
     * @return array
     */
    public function lex(string $sources): array
    {
        $result = [];

        $this->htmlContent = preg_replace_callback(
            $this->compilePcre(),
            function(array $matches) use (&$result)
            {
                foreach(array_reverse($matches) as $name => $value)
                {
                    if(is_string($name) && $value !== '')
                    {
                        if($name === 'T_TAG')
                        {
                            $value = str_replace('<', '&lt;', $value);
                            $value = str_replace('>', '&gt;', $value);
                        }

                        $result[] = [$name => $value];

                        return '<span class="' . str_replace('t_', '', mb_strtolower($name)) . '">' . $value . '</span>';
                    }
                }
                return null;
            },
            $sources);

        return $result;
    }

    /**
     * @return string
     */
    protected function compilePcre(): string
    {
        return sprintf('/(?:%s)/', implode('|', $this->tokens));
    }
}