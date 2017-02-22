<?php

namespace Zazalt\Strink\Tests;

class ExtensionGeekTest extends \Zazalt\Strink\Tests\ZazaltTest
{
    protected $that;

    public function __construct()
    {
        parent::loader($this, '');
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
            '<a><!-- test --></a>' => '<a></a>',
            '<div> <!-- /.col --> </div>' => '<div></div>',
            '<div><!-- /.col --></div>' => '<div></div>',
            '<!--[if lt IE 9]> <script></script> <![endif]-->' => '<!--[if lt IE 9]><script></script><![endif]-->',
            '<!--[if lt IE 9]>
                <script></script>
            <![endif]-->' => '<!--[if lt IE 9]><script></script><![endif]-->',
            '<script></script>' => '<script></script>',
            '<div class="login-box-body"> <!--<p class="login-box-msg">Sign in to start your session</p>-->' => '<div class="login-box-body">',
            '<!--
<div>
new line HTML comment
</div>
-->' => '',
            '<!--
                <div>
                    new line HTML comment
                </div>-->' => ''
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