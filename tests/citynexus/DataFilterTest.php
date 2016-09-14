<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataFilterTest extends TestCase
{
    public function test()
    {
        $this->assertSame(1,1);
    }
//    use DatabaseTransactions;
//    public function __construct()
//    {
//        $this->filter = new \CityNexus\CityNexus\DataFilter();
//    }
//
//    public function invokeMethod(&$object, $methodName, array $parameters = array())
//    {
//        $reflection = new \ReflectionClass(get_class($object));
//        $method = $reflection->getMethod($methodName);
//        $method->setAccessible(true);
//
//        return $method->invokeArgs($object, $parameters);
//    }
//
//    public function testSearchReplace()
//    {
//        $options = ['needle' => 'test', 'replace' => 'new'];
//        $options = (object) $options;
//        $data = 'testThistest';
//
//        $result = $this->invokeMethod($this->filter, 'searchReplace', [$data, $options]);
//
//        $this->assertSame('newThisnew', $result);
//    }

}
