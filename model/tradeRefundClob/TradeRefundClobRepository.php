<?php
namespace asbamboo\openpay\model\tradeRefundClob;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_refund_clob 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradeRefundClobRepository
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
        $this->Repository   = $this->Db->getManager()->getRepository(TradeRefundClobEntity::class);
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradeRefundClobEntity|NULL
     */
    public function findOneByInRefundNo(string $in_refund_no) : ?TradeRefundClobEntity
    {
        return $this->Repository->findOneBy(['in_refund_no' => $in_refund_no]);
    }
}