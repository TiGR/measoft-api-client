<?php

namespace Measoft\Operation;

use Measoft\MeasoftException;
use Measoft\Object\Order;
use Measoft\Traits\Client;
use Measoft\Traits\StreamId;
use SimpleXMLElement;

class OrderSearchOperation extends AbstractOperation
{
    use Client;
    use StreamId;

    public const DONE_FLAG_NOT_DONE = 1;
    public const DONE_FLAG_DONE = 2;
    public const DONE_FLAG_NEW = 3;
    public const DONE_FLAG_DELIVERY = 4;

    private const DONE_FLAGS = [
        self::DONE_FLAG_NOT_DONE => 'ONLY_NOT_DONE',
        self::DONE_FLAG_DONE     => 'ONLY_DONE',
        self::DONE_FLAG_NEW      => 'ONLY_NEW',
        self::DONE_FLAG_DELIVERY => 'ONLY_DELIVERY',
    ];

    /** @var string $orderNumber Поиск по номеру заказа */
    private $orderNumber;

    /** @var string $orderNumber2 Поиск по номеру заказа из срочных */
    private $orderNumber2;

    /** @var string $orderCode Поиск по внутреннему коду заказа */
    private $orderCode;

    /** @var string $givenCode Поиск товаров, название которых начинается с указанной строки */
    private $givenCode;

    /** @var string $dateFrom Поиск с указанной даты */
    private $dateFrom;

    /** @var string $dateTo Поиск по указанную дату */
    private $dateTo;

    /** @var string $target Поиск по строке, которая содержится в названии компании или адресе получателя */
    private $target;

    /**
     * @var string $done
     * ONLY_NOT_DONE - Только не доставленные
     * ONLY_DONE     - Только доставленные
     * ONLY_NEW      - Только новые
     * ONLY_DELIVERY - Только заказы в обработке - заказы, находящиеся в любом статусе, кроме конечных: Доставлено, Не доставлено, Отменён и т.д.
     * null          - Все корреспонденции
     */
    private $done;

    /** @var string $onlyLast Передача только изменившихся статусов */
    private $onlyLast;

    /** @var string $quickStatus Указывает "глубину" передаваемых статусов */
    private $quickStatus;

    /**
     * Поиск по номеру заказа
     *
     * @param string $orderNumber Номер заказа
     * @return self
     */
    public function orderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Поиск по номеру заказа из срочных
     *
     * @param string $orderNumber2 Номер заказа из срочных
     * @return self
     */
    public function orderNumber2(string $orderNumber2): self
    {
        $this->orderNumber2 = $orderNumber2;

        return $this;
    }

    /**
     * Поиск по внутреннему коду заказа
     *
     * @param string $orderCode Внутренний код заказа
     * @return self
     */
    public function orderCode(string $orderCode): self
    {
        $this->orderCode = $orderCode;

        return $this;
    }

    /**
     * Поиск товаров, название которых начинается с указанной строки
     *
     * @param string $givenCode Строка
     * @return self
     */
    public function givenCode(string $givenCode): self
    {
        $this->givenCode = $givenCode;

        return $this;
    }

    /**
     * Поиск с указанной даты
     *
     * @param string $dateFrom Дата "с"
     * @return self
     */
    public function dateFrom(string $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Поиск по указанную дату
     *
     * @param string $dateTo Дата "по"
     * @return self
     */
    public function dateTo(string $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Поиск по строке, которая содержится в названии компании или адресе получателя
     *
     * @param string $target Строка поиска
     * @return self
     */
    public function target(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * 0 - Все корреспонденции
     * 1 - Только не доставленные
     * 2 - Только доставленные
     * 3 - Только новые
     * 4 - Только заказы в обработке - заказы, находящиеся в любом статусе, кроме конечных: Доставлено, Не доставлено, Отменён и т.д.
     *
     * @param int $done
     * @return self
     */
    public function done(int $done): self {
        $this->done = self::DONE_FLAGS[$done] ?? null;

        return $this;
    }

    /**
     * Передача только изменившихся статусов
     *
     * @param bool $onlyLast
     * @return self
     */
    public function onlyLast(bool $onlyLast = true): self
    {
        $this->onlyLast = $onlyLast ? 'ONLY_LAST' : null;

        return $this;
    }

    /**
     * Указывает "глубину" передаваемых статусов
     *
     * @param bool $quickStatus true - быстрые статусы, false - точные статусы
     * @return self
     */
    public function setQuickStatus(bool $quickStatus = true): self
    {
        $this->quickStatus = $quickStatus ? 'YES' : 'NO';

        return $this;
    }

    /**
     * Сформировать XML
     *
     * @return SimpleXMLElement
     */
    protected function buildXml(): SimpleXMLElement
    {
        $xml = $this->createXml('statusreq');
        $this->buildClientXML($xml);
        $xml->addChild('orderno', $this->orderNumber);
        $xml->addChild('orderno2', $this->orderNumber2);
        $xml->addChild('ordercode', $this->orderCode);
        $xml->addChild('givencode', $this->givenCode);
        $xml->addChild('datefrom', $this->dateFrom);
        $xml->addChild('dateto', $this->dateTo);
        $xml->addChild('target', $this->target);
        $xml->addChild('done', $this->done);
        $xml->addChild('changes', $this->onlyLast);
        $xml->addChild('quickstatus', $this->quickStatus);
        $this->buildStreamIdXML($xml);

        return $xml;
    }

    /**
     * Поиск по заданным условиям
     *
     * @return Order[]
     * @throws MeasoftException
     */
    public function search(): array
    {
        foreach ($this->getResults() as $item) {
            $result[] = Order::getFromXml($item);
        }

        return $result ?? [];
    }
}
