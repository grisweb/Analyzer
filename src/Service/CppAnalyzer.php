<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CppAnalyzer
{
    protected string $fileContent;

    protected string $htmlContent;

    protected string $parseContent;

    protected array $operands = [];

    protected array $operators = [];

    protected function parseStringsOperands(): void
    {
        $pattern = '/(&quot;.*&quot;)/';
        $replacement = '<span class="string">$value</span>';

        $this->htmlContent = preg_replace_callback(
            $pattern,
            function($matches) use ($replacement)
            {
                if(!isset($this->operands[$matches[0]]))
                {
                    $this->operands[$matches[0]] = 1;
                }
                else
                {
                    $this->operands[$matches[0]] += 1;
                }

                return str_replace('$value', $matches[0], $replacement);
            },
            $this->htmlContent
        );
    }

    protected function codeParse(): void
    {
        $this->parseStringsOperands();
    }

    public function addCppFile(UploadedFile $cppFile): CppAnalyzer
    {
        $this->htmlContent = htmlspecialchars($cppFile->getContent());
        //dump($this->htmlContent);
        $this->codeParse();

        return $this;
    }

    public function getHtmlContent(): string
    {
        //dump($this->operands);
        return $this->htmlContent;
    }
}