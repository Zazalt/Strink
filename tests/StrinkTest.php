<?php

namespace Zazalt\Strink\Tests;

class StrinkTest extends \Zazalt\Strink\Tests\ZazaltTest
{
    protected $that;

    public function __construct()
    {
        parent::loader($this, []);
    }

    public function testCompressSpaces()
    {
        $stringsToTest = [
            ' test  test test "  test   "' => ' test test test " test "',
            "Loreim I  psum dor  å  m" => "Loreim I psum dor å m",
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressSpaces());
        }
    }

    public function testCompressSlashes()
    {
        $stringsToTest = [
            'lorem//ipsum/' => 'lorem/ipsum/',
            'lorem//IPSUM/' => 'lorem/IPSUM/',
            'lorem\\ipsum/' => 'lorem\\ipsum/',
            'test//utf¿8//strings\/' => 'test/utf¿8/strings\/',
            'http://localhost//test' => 'http://localhost/test',
            'http://///localhost//test' => 'http://localhost/test',
            'https://localhost//test' => 'https://localhost/test',
            'https://///localhost//test' => 'https://localhost/test',
            '////%var%//mytest/test///testing' => '/%var%/mytest/test/testing'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressSlashes());
        }
    }

    public function testCompressDoubleQuotesProvider()
    {
        return [
            '""Please test this string""' => '"Please test this string"',
            '"""Please "test\' this string""' => '"Please "test\' this string"',
            '""Pleașe țest thîs string""' => '"Pleașe țest thîs string"',
            '"""Pleașe țest thîs string""' => '"Pleașe țest thîs string"',
            '""Please test \"this\" string""' => '"Please test \"this\" string"',
            '"""Please test this string""' => '"Please test this string"',
            '""Pleașe țest thîs string""' => '"Pleașe țest thîs string"',
            '"""Pleașe țest thîs string""' => '"Pleașe țest thîs string"',
        ];
    }

    public function testCompressDoubleQuotes()
    {
        foreach($this->testCompressDoubleQuotesProvider() as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressDoubleQuotes());
        }
    }

    public function testCompressSimpleQuotesProvider()
    {
        return [
            "''Please test this string''" => "'Please test this string'",
            "'''Please 'test\" this string''" => "'Please 'test\" this string'",
            "''Pleașe țest thîs string''" => "'Pleașe țest thîs string'",
            "'''Pleașe țest thîs string''" => "'Pleașe țest thîs string'",
            "''Please test \'this\' string''" => "'Please test \'this\' string'",
            "'''Please test this string''" => "'Please test this string'",
            "''Pleașe țest thîs string''" => "'Pleașe țest thîs string'",
            "'''Pleașe țest thîs string''" => "'Pleașe țest thîs string'",
        ];
    }

    public function testCompressSimpleQuotes()
    {
        foreach($this->testCompressSimpleQuotesProvider() as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressSimpleQuotes());
        }
    }

    public function testCompressQuotes()
    {
        foreach($this->testCompressDoubleQuotesProvider() as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressQuotes());
        }

        foreach($this->testCompressSimpleQuotesProvider() as $key => $value) {
            //$this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressQuotes());
        }

        foreach(array_merge($this->testCompressDoubleQuotesProvider(), $this->testCompressSimpleQuotesProvider()) as $key => $value) {
            //$this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->compressQuotes());
        }
    }

    public function testRandomString()
    {
        $this->assertEquals(10, strlen(\Zazalt\Strink\Strink::turn()->randomString(10)));
    }

    public function testLimitedString()
    {
        // Test the right cut
        $stringsToTest = [
            'This should be a very long string' => 'This should be a...',
            'Please test this string' => 'Please test this...',
            'Pleașe tesț this ștring' => 'Pleașe tesț this...'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->limitedString(16, '...', 'right'));
        }

        unset($stringsToTest);

        // Test the middle cut
        $stringsToTest = [
            'This should be a very long string' => 'This sho...g string',
            'Please test this string' => 'Please t...s string',
            'Pleașe tesț this ștring' => 'Pleașe t...s ștring'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->limitedString(16, '...', 'middle'));
        }
    }

    public function testSnakeCaseToCamelCase()
    {
        $stringsToTest = [
            'sample' => 'sample',
            'snake_case' => 'snakeCase',
            'sna-ke_case' => 'sna-keCase',
            'SNAKE_CASE' => 'snakeCase'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->snakeCaseToCamelCase());
        }
    }

    public function testCamelCaseToSnakeCase()
    {
        $stringsToTest = [
            'sample' => 'sample',
            'snakeCase' => 'snake_case',
            'SnakeCase' => 'snake_case',
            'loremIpsum dolor' => 'lorem_ipsum dolor'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->camelCaseToSnakeCase());
        }
    }

    public function testTransliterateUtf8String()
    {
        $stringsToTest = [
            'sample' => 'sample',
            'teșț//utf¿8//strings\/' => 'test//utf¿8//strings\/',
            'crème brûlée'  =>  'creme brulee',
            'pêches épinards' => 'peches epinards',
            'ľ š č ť ž ý á í é Č Á Ž Ý' => 'l s c t z y a i e C A Z Y',
            'ĂăÎîȘșȚț' => 'AaIiSsTt'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->transliterateUtf8String());
        }
    }

    public function testSlugify()
    {
        // ASCII test

        $stringsToTest = [
            'sample' => 'sample',
            'SAMPLE-STRING' => 'sample-string',
            'snakeCase 1' => 'snake-case-1',
            'SnakeCase 2' => 'snake-case-2',
            'Lorem_ipsum”dolor~sit!amet' => 'lorem-ipsum-dolor-sit-amet',
            'loremIpsum dolor' => 'lorem-ipsum-dolor',
            'Pleașe tesț this ștring' => 'please-test-this-string',
            '/kind of--bad-string-' => 'kind-of-bad-string',
            'teșț//utf¿8//strings\/' => 'test-utf-8-strings'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->slugify(false));
        }

        // UTF-8 test

        $stringsToTest = [
            'sample' => 'sample',
            'snakeCase' => 'snake-case',
            'SnakeCase' => 'snake-case',
            'loremIpsum dolor' => 'lorem-ipsum-dolor',
            'Pleașe tesț This ștring' => 'pleașe-tesț-this-ștring',
            '/kind of--bad-String-' => 'kind-of-bad-string',
            'Test//utf¿8//strings\/' => 'test-utf-8-strings'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->slugify(true));
        }
    }

    public function testClearSQLComments()
    {
        $stringsToTest = [
            'SELECT * FROM TEST;' => 'SELECT * FROM TEST;',
            'SELECT * FROM TEST; --comment here' => 'SELECT * FROM TEST;'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->clearSQLComments());
        }
    }

    public function testMinifyHTML()
    {
        $stringsToTest = [
            '<!-- test -->' => '',
            '<div> <!-- /.col --> </div>' => '<div> </div>',
            '<div><!-- /.col --></div>' => '<div></div>'
        ];

        foreach($stringsToTest as $key => $value) {
            $this->assertEquals($value, \Zazalt\Strink\Strink::turn($key)->minifyHTML());
        }
    }

    public function testMinifyCSS()
    {

    }

    public function testCompressCSS()
    {

    }

    public function testSanitizeHTML()
    {
        
    }
}