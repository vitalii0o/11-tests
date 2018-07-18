<?php

namespace Tests\Unit;

use App\Entity\Lot;
use App\Repository\Contracts\LotRepository;
use App\Request\AddLotRequest;
use App\Service\MarketService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarketServiceTest extends TestCase
{
    private $lotRepository;

    /**
     * @var MarketService
     */
    private $marketService;

    public function setUp()
    {
        parent::setUp();
        $this->lotRepository = $this->app->make(LotRepository::class);
        $this->marketService = new MarketService($this->lotRepository);
    }

    public function testOlderDateStartThanDateClosed()
    {
        $lotRequestMock = $this->getMockBuilder(AddLotRequest::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $today              = strtotime('00:00:00');
        $yesterday          = strtotime('-1 day', $today);
        $lotRequestMock->expects($this->any())->method('getDateTimeOpen')->willReturn($today);
        $lotRequestMock->expects($this->any())->method('getDateTimeClose')->willReturn($yesterday);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrong open date');
        $this->marketService->addLot($lotRequestMock);
    }

    public function testNegativePrice()
    {
        $lotRequest = new AddLotRequest(1, 1, time(), time(), -5);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrong price format');
        $this->marketService->addLot($lotRequest);
    }

    public function testAddLot()
    {
        $today              = strtotime('00:00:00');
        $tomorrow          = strtotime('+1 day', $today);
        $lotRequest = new AddLotRequest(1, 1, $today, $tomorrow, 5);
        $lotRepository = $this->getMockBuilder(\App\Repository\LotRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $lotMock = $this->createMock(Lot::class);
        $lotRepository->expects($this->any())->method('add')->willReturn($lotMock);
        $marketService = new MarketService($lotRepository);

        $result = $marketService->addLot($lotRequest);

        $this->assertInstanceOf(Lot::class, $result);
    }
}
