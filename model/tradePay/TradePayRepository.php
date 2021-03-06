<?php
namespace asbamboo\openpay\model\tradePay;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_pay 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradePayRepository
{
    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var EntityRepository
     */
    protected $Repository;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db)
    {
        $this->Db           = $Db;
        $this->Repository   = $this->Db->getManager()->getRepository(TradePayEntity::class);
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradePayEntity|NULL
     */
    public function load(string $in_trade_no) : ?TradePayEntity
    {
        return $this->Repository->findOneBy(['in_trade_no' => $in_trade_no]);
    }

    /**
     *
     * @param string $out_trade_no
     * @return TradePayEntity|NULL
     */
    public function loadByOutTradeNo(string $out_trade_no) : ?TradePayEntity
    {
        return $this->Repository->findOneBy(['out_trade_no' => $out_trade_no]);
    }
}