<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("src/Article.php");

Class addTest extends PHPUnit_Framework_TestCase
{

    public function test_add()
    {
        $event = new Article();
                          
        $this->assertEquals(10,$event->add(1,1));
    }
}
