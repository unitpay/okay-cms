<?php


namespace Okay\Modules\OkayCMS\UnitPay\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Psr\Log\LoggerInterface;

class CallbackController extends AbstractController
{
    public function payOrder(
        Money            $money,
        Notify           $notify,
        LoggerInterface  $logger,
        OrdersEntity     $ordersEntity,
        PaymentsEntity   $paymentsEntity,
        CurrenciesEntity $currenciesEntity
    ) {
        $this->response->setContentType(RESPONSE_JSON);

        $params  = $this->request->get('params');
        $orderId = $params['account'];

        $order = $ordersEntity->get((int) $orderId);
        if (empty($order)) {
            $this->response->setContent(json_encode(["error" => ["message" => "Заказ не найден"]]));
            $this->response->sendContent();
            exit;
        }

        $paymentSettings = $paymentsEntity->getPaymentSettings((int) $order->payment_method_id);
        if (empty($paymentSettings)) {
            $this->response->setContent(json_encode(["error" => ["message" => "Неверно настроен способ оплаты"]]));
            $this->response->sendContent();
            exit;
        }

        $secretKey = $paymentSettings['secret_key'];
        if (empty($secretKey)) {
            $this->response->setContent(json_encode(["error" => ["message" => "Не задан секретный ключ"]]));
            $this->response->sendContent();
            exit;
        }

        $method  = $this->request->get('method');
        if (! $this->verifySignature($method, $params, $secretKey)) {
            $this->response->setContent(json_encode(["error" => ["message" => "Неверная сигнатура"]]));
            $this->response->sendContent();
            exit;
        }
        
        if ($method === 'pay') {
            $ordersEntity->update(intval($order->id), ['paid'=>1]);
            $notify->emailOrderUser(intval($order->id));
            $notify->emailOrderAdmin(intval($order->id));
            $ordersEntity->close(intval($order->id));
        }

        $this->response->setContent(json_encode(["result" => ["message" => "Запрос успешно обработан"]]));
    }

    private function verifySignature($method, $params, $secretKey)
    {
        return $params['signature'] == $this->getSignature($method, $params, $secretKey);
    }

    private function getSignature($method, array $params, $secretKey)
    {
        ksort($params);
        unset($params['sign']);
        unset($params['signature']);
        array_push($params, $secretKey);
        array_unshift($params, $method);
        return hash('sha256', join('{up}', $params));
    }
}
