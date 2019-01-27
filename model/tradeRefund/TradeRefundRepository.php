<?php
namespace asbamboo\openpay\model\tradeRefund;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_pay 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradeRefundRepository
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
        $this->Repository   = $this->Db->getManager()->getRepository(TradeRefundEntity::class);
    }

    /**
     *
     * @param string $in_refund_no
     * @return TradeRefundEntity|NULL
     */
    public function load(string $in_refund_no) : ?TradeRefundEntity
    {
        return $this->Repository->findOneBy(['in_refund_no' => $in_refund_no]);
    }

    /**
     *
     * @param string $out_refund_no
     * @return TradeRefundEntity|NULL
     */
    public function loadByOutRefundNo(string $out_refund_no) : ?TradeRefundEntity
    {
        return $this->Repository->findOneBy(['out_refund_no' => $out_refund_no]);
    }

    /**
     * 通过一个聚合平台的支付交易单号, 获取与他对应的总的创建的退款金额
     *
     * @param string $in_trade_no
     * @return int
     */
    public function getTotalRefundFeeByInTradeNo(string $in_trade_no) : int
    {
        $QueryBuilder   = $this->Repository->createQueryBuilder('t');
        $andx           = $QueryBuilder->expr()->andX();

        $QueryBuilder->select('SUM(t.refund_fee) AS rf');

        $andx->add($QueryBuilder->expr()->eq('t.in_trade_no', ':in_trade_no'));
        $QueryBuilder->setParameter('in_trade_no', $in_trade_no);

        return (int) $QueryBuilder->where($andx)->getQuery()->getSingleScalarResult();
    }
}