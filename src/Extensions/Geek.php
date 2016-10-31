<?php

namespace Zazalt\Strink\Extension;

class Geek
{
    public function clearSqlComments()
    {
        $sqlComments = '@(([\'"`]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms'; // remove SQL comments
        return new static(trim(preg_replace($sqlComments, '$1', $this->string)));
    }
}