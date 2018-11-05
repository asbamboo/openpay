<?php
namespace asbamboo\openpay\model\tradeRefundThirdPart;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_pay 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradeRefundThirdPartRespository
{
    /**
     *
     * @var FactoryInterface
     */
    private $Db;

    /**
     *
     * @var EntityRepository
     */
    private $Repository;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db)
    {
        $this->Db           = $Db;
        $this->Repository   = $this->Db->getManager()->getRepository(TradeRefundThirdPartEntity::class);
    }

    /**
     * 
     * @param string $in_trade_no
     * @return TradeRefundThirdPartEntity|NULL
     */
    public function findOneByInRefundNo(string $in_refund_no) : ?TradeRefundThirdPartEntity
    {
        return $this->Repository->findOneBy(['in_refund_no' => $in_refund_no]);
    }
}