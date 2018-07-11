<?php

namespace OnlineCashDesk;


use \Zend\Db\ResultSet\ResultSet;
use \Zend\Db\Sql\Select;
use \Zend\Db\TableGateway\TableGatewayInterface;

class CashDesk
{
    /**
     * @var array Cash Desk config
     */
    private $config;

    /**
     * @var TableGatewayInterface "Хранилище" чеков
     */
    private $receiptsStore = null;


    /**
     * CashDesk constructor.
     *
     * @param TableGatewayInterface $receiptsStore Хранилище данных чеков
     */
    public function __construct(array $config, TableGatewayInterface $receiptsStore)
    {
        $this->config = $config;
        $this->setReceiptsStore($receiptsStore);
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param TableGatewayInterface $receiptsStore Хранилище данных чеков
     */
    public function setReceiptsStore(TableGatewayInterface $receiptsStore)
    {
        $this->receiptsStore = $receiptsStore;
    }

    /**
     * Метод возвращает чеки кассы.
     *
     * @param array $options Параметры выборки чеков кассы
     *
     * @return ResultSet
     */
    public function getReceipts(array $options)
    {
        if (!$this->receiptsStore) {
            throw new \RuntimeException('Receipts store is not set');
        }

        return $this->receiptsStore->select(function(Select $select) use ($options) {
            if (!empty($options['sort'])) {
                $select->order($options['sort']);
            } else {
                $select->order('created DESC');
            }

            if (isset($options['filter']['status'])) {
                $select->where(['status' => $options['filter']['status']]);
            }
        });
    }

    public function addReceipt(Receipt $receipt)
    {
        $this->storeReceipt($receipt);
    }

    /**
     * Метод создает новый фискальный чек, но не регистрирует его в онлайн кассе. Непосредственно запрос на отправку
     * чеков в онлайн кассу (облако) выполняется методом registerPendingReceipts() кассы.
     */
    public function registerPendingReceipts(ApiCaller $apiCaller)
    {
        $errors = [];

        // Обновляем статус чеков, по которым ожидается ответ от онлайн кассы
        $onHoldReceiptsRaw = $this->getReceipts([
            'filter' => ['status' => Receipt::STATUS_PENDING],
            'sort' => 'created ASC',
        ]);

        /** @var \ArrayObject $row */
        foreach ($onHoldReceiptsRaw as $row) {
            $receiptData = json_decode($row->receipt_data, true);
            $responseData = json_decode($row->response_data, true);
            if ($result = $apiCaller->checkReceiptStatus($receiptData, $responseData)) {
                $row->response_data = json_encode($result->getResponseData());
                $row->status = $result->getReceiptStatus();

                $this->receiptsStore->update($row->getArrayCopy(), [
                    'id' => $row->id,
                ]);

                if ($result->hasError()) {
                    $errors[] = [
                        'id' => $row->id,
                        'operation' => 'status check',
                        'text' => $result->getError(),
                    ];
                }
            }
        }

        // Отправляем ожидающие отправку чеки в онлайн кассу
        $pendingReceiptsRaw = $this->getReceipts([
            'filter' => ['status' => Receipt::STATUS_NEW],
            'sort' => 'created ASC',
        ]);

        /** @var \ArrayObject $row */
        foreach ($pendingReceiptsRaw as $row) {
            $receiptData = json_decode($row->receipt_data, true);
            if ($result = $apiCaller->sendReceipt($receiptData)) {
                $row->response_data = json_encode($result->getResponseData());
                $row->status = $result->getReceiptStatus();

                $this->receiptsStore->update($row->getArrayCopy(), [
                    'id' => $row->id,
                ]);

                if ($result->hasError()) {
                    $errors[] = [
                        'id' => $row->id,
                        'operation' => 'send',
                        'text' => $result->getError(),
                    ];
                }
            }
        }

        return [
            'errors' => $errors,
        ];
    }

    private function storeReceipt(Receipt $receipt)
    {
        if (!$this->receiptsStore) {
            throw new \RuntimeException('Receipts store is not set');
        }

        $this->receiptsStore->insert([
            'created' => $receipt->getCreationDateTime()->format('Y-m-d H:i:s'),
            'receipt_data' => json_encode($receipt->toArray()),
            'status' => Receipt::STATUS_NEW,
        ]);
    }
}