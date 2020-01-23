<?php

namespace App\Tests;

use App\Service\EscapingService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EncoderTest extends WebTestCase
{

    public function testSomething()
    {
        self::bootKernel();

        $container = self::$container;


        $escapingService = $container->get(EscapingService::class);
        $test1 =  ".";
        $output = $escapingService->escape_char($test1, "-");

        $test2 = "jacopo.trapani";
        $output2 = $escapingService->escape($test2, "-");

        $this->assertEquals("-2e",$output);
        $this->assertEquals("jacopo-2etrapani",$output2);
    }
}
