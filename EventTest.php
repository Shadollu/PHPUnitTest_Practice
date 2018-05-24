<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("Event.php");
require_once("User.php");
require_once('EventException.php');

require 'vendor/autoload.php';
class EventTest extends PHPUnit_Framework_TestCase
{
//=========================================================================================
    
    /**
     *
     * Fixtures; setUp將測試的資料與物件初始化,tearDown將測試完的資料還原為初始前的狀態
     */
    private $event;
    private $user;

    public function setUp()
    {
        //建立測試資料
        $eventId = 1;
        $eventName = '活動1';
        $eventStartDate = '2014-12-24 18:00:00';
        $eventEndDate = '2014-12-24 20:00:00';
        $eventDeadline = '2014-12-23 23:59:59';
        $eventAttendeeLimit = 10;
        $this->event = new \PHPUnitEventDemo\Event($eventId, $eventName, $eventStartDate, $eventEndDate, $eventDeadline, $eventAttendeeLimit);

        $userId = 1;
        $userName = 'User1';
        $userEmail = 'user1@openfoundry.org ';
        $this->user = new \PHPUnitEventDemo\User($userId, $userName, $userEmail);
    }

    public function tearDown()
    {
        $this->event = null;
        $this->user = null;
    }
    
    //========================================================================================
    
    /*
     * 測試活動報名,在function內建立測試資料並執行,此function有兩個Assertion
     * 套用Fixtures,所以不需要再建立資料與物件
     */

    public function testReserve()
    {
        //建立測試資料


        // 使用者報名活動
        $this->event->reserve($this->user);

        // 預期報名人數
        $expectedNumber = 1;

        //檢查是否符合預期報名人數
        $this->assertEquals($expectedNumber, $this->event->getAttendeeNumber());

        // 報名清單中有已經報名的人
        $this->assertContains($this->user, $this->event->attendees);

        return [$this->event, $this->user];
    }

    /**
     *  @depends testReserve
     * 測試取消報名,這個function使用了Depends,相依於 testReverse;注意,如果相依的function的資料來源為dataProvider,在此的Depends會發生錯誤
     */
    public function testUnreserve($objs)
    {
        $event = $objs[0];
        $user = $objs[1];

        // 使用者取消報名
        $event->unreserve($user);

        // 扣除取消報名後的人數
        $unreserveExpectedCount = 0;

        //檢查是否符合報名人數
        $this->assertEquals($unreserveExpectedCount, $event->getAttendeeNumber());

        // 報名清單中沒有已經取消報名的人
        $this->assertNotContains($user, $event->attendees);
    }

    /**
     *  @dataProvider eventsDataProvider
     * 測試報名人數限制,此Function使用dataProvider,資料由eventsDataProvider提供
     */
    public function testAttendeeLimitReserve($eventId, $eventName, $eventStartDate, $eventEndDate, $eventDeadline, $attendeeLimit)
    {
        $event = new \PHPUnitEventDemo\Event($eventId, $eventName, $eventStartDate, $eventEndDate, $eventDeadline, $attendeeLimit);

        //預期報名人數數量
        $userNumber = 6;

        // 建立不同使用者報名(數量：6)
        for ($userCount = 1; $userCount <= $userNumber; $userCount++) {
            $userId = $userCount;
            $userName = 'User ' . $userId;
            $userEmail = 'user' . $userId . '@openfoundry.org';
            $user = new \PHPUnitEventDemo\User($userId, $userName, $userEmail);

            $reservedResult = $event->reserve($user);

            // 檢查報名人數是否超過
            if ($userCount > $attendeeLimit) {
                // 無法報名
                $this->assertFalse($reservedResult);
            } else {
                $this->assertTrue($reservedResult);
            }
        }
        return [$event, $user];
    }

    //提供單元測試所需的資料
    public function eventsDataProvider()
    {
        $eventId = 1;
        $eventName = "活動1";
        $eventStartDate = '2014-12-24 12:00:00';
        $eventEndDate = '2014-12-24 13:00:00';
        $eventDeadline = '2014-12-23 23:59:59';
        $eventAttendeeLimitNotFull = 5;
        $eventAttendeeFull = 10;

        $eventsData = array(
            array(
                $eventId,
                $eventName,
                $eventStartDate,
                $eventEndDate,
                $eventDeadline,
                $eventAttendeeLimitNotFull
            ),
            array(
                $eventId,
                $eventName,
                $eventStartDate,
                $eventEndDate,
                $eventDeadline,
                $eventAttendeeFull
            )
        );

        return $eventsData;
    }
    //測試重複報名,預期丟出異常

    /**
     * @expectedException \PHPUnitEventDemo\EventException
     * @expectedExceptionMessage Duplicated reservation
     * @expectedExceptionCode 1
     */
    public function testDuplicatedReservationWithException()
    {
        // 測試重複報名，預期丟出異常

        // 同一個使用者報名兩次
        $this->event->reserve($this->user);
        $this->event->reserve($this->user);
    }
}



//reference : https://www.openfoundry.org/tw/tech-column/9326-phpunit-testing

//Different with Fixtures and dataProvider, Fixtures 固定在一開始初始化時使用,而dataProvider會因為不同的環境給予不同的資訊