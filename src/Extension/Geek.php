<?php

namespace Zazalt\Strink\Extension;

class Geek
{
    public function clearSQLComments()
    {
        $sqlComments = '@(([\'"`]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms'; // remove SQL comments
        return new static(trim(preg_replace($sqlComments, '$1', $this->string)));
    }

    public function minifyHTML()
    {
        $search = array(
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s'     // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        return new static(preg_replace($search, $replace, $this->string));
    }

    public function minifyCSS()
    {
        $this->string = preg_replace('#\s+#', ' ', $this->string);
        $this->string = preg_replace('#/\*.*?\*/#s', '', $this->string);
        $this->string = str_replace('; ', ';', $this->string);
        $this->string = str_replace(': ', ':', $this->string);
        $this->string = str_replace(' {', '{', $this->string);
        $this->string = str_replace('{ ', '{', $this->string);
        $this->string = str_replace(', ', ',', $this->string);
        $this->string = str_replace('} ', '}', $this->string);
        $this->string = str_replace(';}', '}', $this->string);

        return new static($this->string);
    }

    public function compressCSS()
    {
        /* remove comments */
        $this->string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->string);
        /* remove tabs, spaces, newlines, etc. */
        $this->string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $this->string);

        return new static($this->string);
    }

    public function sanitizeHTML()
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'   // Strip multi-line comments
        );

        return new static(preg_replace($search, '', $this->string));
    }
}