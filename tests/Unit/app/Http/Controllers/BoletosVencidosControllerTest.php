<?php

declare(strict_types=1);

namespace Tests\Unit\app\Http\Controllers;

use App\Http\Controllers\BoletosVencidosController;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

/**
 * Class BoletosVencidosControllerTest
 */
class BoletosVencidosControllerTest extends TestCase
{
    /**
     * @var BoletosVencidosController
     */
    private $mockController;

    /**
     * @var Request
     */
    private $mockRequest;

    /**
     * @var Response
     */
    private $mockResponse;

    public function setUp(): void
    {
        $this->mockController = $this->getMockBuilder(BoletosVencidosController::class)
            ->onlyMethods(['getBoletoResponse', 'validaBoleto', 'calculaBoleto', 'saveBoleto'])
            ->getMock();
        $this->mockResponse = $this->getMockBuilder(Response::class)
             ->disableOriginalConstructor()
             ->getMock();
        $this->mockRequest = $this->getMockBuilder(Request::class)->getMock();
    }

    /**
     * @throws \Exception
     */
    public function testIndex(): void
    {

        $this->mockRequest = new Request([
            'original_amount' => 1000,
            'amount' => 100.0,
            'due_date' => '2023-06-05',
            'payment_date' => '2022-03-01',
            'interest_amount_calculated' => 50.0,
            'fine_amount_calculated' => 50.0,
        ]);

        $this->mockController->expects($this->once())
            ->method('getBoletoResponse')
            ->with($this->equalTo($this->mockRequest))
            ->willReturn($this->mockResponse);



    }


}
