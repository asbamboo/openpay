<?php
namespace asbamboo\openpay\model\tradePayClob;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_pay_clob 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradePayClobRepository
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
        $this->Repository   = $this->Db->getManager()->getRepository(TradePayClobEntity::class);
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradePayClobEntity|NULL
     */
    public function findOneByInTradeNo(string $in_trade_no) : ?TradePayClobEntity
    {
        return $this->Repository->findOneBy(['in_trade_no' => $in_trade_no]);
    }
}