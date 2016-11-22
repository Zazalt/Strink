<?php

namespace Zazalt\Strink\Tests;

class GeekTest extends \Zazalt\Strink\Tests\ZazaltTest
{
    protected $that;

    public function __construct()
    {
        parent::loader($this, []);
    }

    public function testClearSQLComments()
    {
        $stringsToTest = [
            'SELECT * FROM TEST; --comment here' => 'SELECT * FROM TEST;'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->clearSQLComments());
        }
    }
}