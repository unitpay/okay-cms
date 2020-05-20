<?php


namespace Okay\Modules\OkayCMS\UnitPay;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Money
     */
    private $money;
    
    public function __construct(EntityFactory $entityFactory, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->money         = $money;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
	{
		/** @var OrdersEntity $ordersEntity */
	    $ordersEntity     = $this->entityFactory->get(OrdersEntity::class);
	    
		/** @var PaymentsEntity $paymentsEntity */
	    $paymentsEntity   = $this->entityFactory->get(PaymentsEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
	    
		$order           = $ordersEntity->get((int)$orderId);
		$paymentMethod   = $paymentsEntity->get($order->payment_method_id);
		$paymentSettings = $paymentsEntity->getPaymentSettings($paymentMethod->id);

        $sum       = round($this->money->convert($order->total_price, $paymentMethod->currency_id, false), 2);
        $account   = $orderId;
        $publicKey = $paymentSettings['public_key'];
        $secretKey = $paymentSettings['secret_key'];// todo
        $desc      = 'Оплата покупок в интернете';
        $backUrl   = Router::generateUrl('order', ['url' => $order->url], true);

        $currencyCode = $currenciesEntity->get((int) $paymentMethod->currency_id)->code;
        if ($currencyCode === 'RUR') {
            $currencyCode = 'RUB';
        }
        
        $hashStr = $account.'{up}'.$currencyCode.'{up}'.$desc.'{up}'.$sum.'{up}'.$secretKey;
        $signature = hash('sha256', $hashStr);

        $this->design->assign('public_key',    $publicKey);
        $this->design->assign('sum',           $sum);
        $this->design->assign('account',       $account);
        $this->design->assign('desc',          $desc);
        $this->design->assign('currency_code', $currencyCode);
        $this->design->assign('back_url',      $backUrl);
        $this->design->assign('signature',     $signature);

        return $this->design->fetch('form.tpl');
	}
}