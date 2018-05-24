<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DataTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider additionProvider
     */
    public function testAdd($a, $b, $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider()
    {
        return[
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 1, 3] //exception, 1 + 1 not equals 3.
        ];
    }
}
