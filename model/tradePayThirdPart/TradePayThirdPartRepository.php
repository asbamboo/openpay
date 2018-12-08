<?php
namespace asbamboo\openpay\model\tradePayThirdPart;

use asbamboo\database\FactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 专门用来处理 tt_trade_pay 数据的查询
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
class TradePayThirdPartRepository
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
        $this->Repository   = $this->Db->getManager()->getRepository(TradePayThirdPartEntity::class);
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradePayThirdPartEntity|NULL
     */
    public function findOneByInTradeNo(string $in_trade_no) : ?TradePayThirdPartEntity
    {
        return $this->Repository->findOneBy(['in_trade_no' => $in_trade_no]);
    }
}