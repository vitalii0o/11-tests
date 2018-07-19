<?php

namespace Tests\Unit;

use App\Entity\Lot;
use App\Entity\Trade;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Service\MarketService;
use App\User;
use PhpParser\Node\Expr\Array_;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repository\Contracts\WalletRepository;

class MarketServiceTest extends TestCase
{
    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * @var MarketService
     */
    private $marketService;

    /**
     * @var TradeRepository
     */
    private $tradeRepository;

    /**
     * @var WalletRepository
     */
    private $walletRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->lotRepository = $this->getMockBuilder(\App\Repository\LotRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();


        $this->tradeRepository = $this->app->make(TradeRepository::class);
        $this->walletRepository = $this->getMockBuilder(\App\Repository\WalletRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->userRepository = $this->app->make(UserRepository::class);

        $this->marketService = new MarketService($this->lotRepository, $this->tradeRepository, $this->userRepository, $this->walletRepository);
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
        $lotMock = $this->createMock(Lot::class);
        $this->lotRepository->expects($this->any())->method('add')->willReturn($lotMock);

        $result = $this->marketService->addLot($lotRequest);

        $this->assertInstanceOf(Lot::class, $result);
    }

    public function testGetLotList()
    {
        $lot = $this->createMock(Lot::class);
        $lot2 = $this->createMock(Lot::class);
        $lot3 = $this->createMock(Lot::class);
        $lot4 = $this->createMock(Lot::class);
        $lots = [$lot, $lot2, $lot3, $lot4];

        $lotMock = $this->createMock(Lot::class);
        $this->lotRepository->expects($this->any())->method('add')->willReturn($lotMock);
        $this->lotRepository->expects($this->any())->method('findAll')->willReturn($lots);

        $result = $this->marketService->getLotList();

        $this->assertEquals(4, count($result));
    }

    public function testBuyLotWrongUser()
    {
        $buyLotRequest = new BuyLotRequest(1, 1, 10);

        $lot = new Lot();
        $lot->seller_id = 1;
        $this->lotRepository->expects($this->any())->method('getById')->willReturn($lot);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The same user');
        $this->marketService->buyLot($buyLotRequest);
    }

    public function testBuyLotSmallAmount()
    {
        $buyLotRequest = new BuyLotRequest(1, 1, 0.2);

        $lot = new Lot();
        $lot->seller_id = 2;
        $this->lotRepository->expects($this->any())->method('getById')->willReturn($lot);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Small lot amount');
        $this->marketService->buyLot($buyLotRequest);
    }

    public function testBuyLotAmountGreaterThanLotPrice()
    {
        $buyLotRequest = new BuyLotRequest(1, 1, 50);

        $lot = new Lot();
        $lot->seller_id = 2;
        $lot->price = 10;
        $this->lotRepository->expects($this->any())->method('getById')->willReturn($lot);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You want too much money');
        $this->marketService->buyLot($buyLotRequest);
    }

    public function testBuyLotClosed()
    {
        $buyLotRequest = new BuyLotRequest(1, 1, 2);
        $today = strtotime('00:00:00');
        $yesterday = strtotime('-1 day', $today);
        $lot = new Lot();
        $lot->seller_id = 2;
        $lot->price = 10;
        $lot->date_time_close = $yesterday;

        $this->lotRepository->expects($this->any())->method('getById')->willReturn($lot);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Lot has been closed');
        $this->marketService->buyLot($buyLotRequest);
    }

    /*public function testBuyLot()
    {
        $buyLotRequest = new BuyLotRequest(1, 1, 2);

        $today = strtotime('00:00:00');
        $lot = new Lot();
        $lot->seller_id = 2;
        $lot->price = 10;
        $lot->date_time_close = strtotime('+11 day', $today);
        $this->lotRepository->expects($this->any())->method('getById')->willReturn($lot);
        $user = $this->createMock(User::class);

        $this->walletRepository->expects($this->any())->method('findByUser')->willReturn($user);

        $marketService = new MarketService($this->lotRepository, $this->tradeRepository, $this->userRepository, $this->walletRepository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Lot has been closed');
        $marketService->buyLot($buyLotRequest);
    }*/
}
