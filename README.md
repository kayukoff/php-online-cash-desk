# PHP SDK для интеграции с онлайн-кассами АТОЛ, Дримкас

Данный PHP SDK содержит код для интеграции с онлайн-кассами [АТОЛ](https://online.atol.ru/) и [Дримкас](https://dreamkas.ru/). 
Поддерживаемые версии API:

* АТОЛ — v3 и v4
* Дримкас — v1

Для регистрации чеков в онлайн-кассе используется поллинг, поскольку сами кассы не регистрируют чек сразу, а возвращают 
его уникальный идентификатор, с помощью которого в дальнейшем можно получить статус регистрации чека в ОФД. Таким образом 
создание и регистрация чека проходит в несколько этапов:

1. создается объект чека, заполняются его атрибуты, после чего данные чека сохраняются в БД для дальнейшей регистрации в онлайн-кассе
2. код, выполняющийся, например, по крону, отправляет чек на регистрацию в онлайн-кассу
3. спустя некоторое время, получаем статус регистрации чека в онлайн-кассе

Таким образом, все дорогостоящие операции обращения к стороннему API будут выполняться вне кода, с которым взаимодействует клиент.


## Установка

С помощью composer:

```
...
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/kayukoff/php-online-cash-desk"
        }
    ]
...
```

Структура таблицы для хранения чеков в БД приведена в файле [receipts.sql](receipts.sql).

## Пример использования

```php
use \OnlineCashDesk\CashDesk;
use \OnlineCashDesk\Atol\v4\ReceiptItem;
use \OnlineCashDesk\Atol\v4\Receipt;
use \OnlineCashDesk\Atol\v4\ApiCaller;


// Инициализируем соединение с БД для сохранения данных чеков
$dbAdapter = new \Zend\Db\Adapter\Adapter([
    'driver' => 'Pdo_Mysql',
    'hostname' => 'localhost',
    'username' => 'DB_USER_NAME',
    'password' => 'DB_USER_PASS',
    'database' => 'DB_USER_HOST',
    'charset' => 'UTF8',
]);
$receiptsTable = new \Zend\Db\TableGateway\TableGateway('receipts', $dbAdapter);

// Создаем кассу
$onlineCashDeskConfig = [
    'login' => 'ATOL_LOGIN',
    'pass' => 'ATOL_PASS',
    'group_code' => 'ATOL_GROUP_CODE',
    'inn' => 'COMPANY_INN',
    'email' => 'COMPANY_EMAIL_FOR_RECEIPTS_FROM',
    'payment_address' => 'COMPANY_PAYMENT_ADDRESS_FROM_ATOL',
    'sno' => Receipt::TAX_SYSTEM_OSN,
];
$cashDesk = new CashDesk($onlineCashDeskConfig, $receiptsTable);

// Создаем чек
$item1 = new ReceiptItem();
$item1->setName('Струны Ernie Ball Regular Slinky 10-46')
    ->setPrice(389)
    ->setQuantity(2)
    ->setVatType(ReceiptItem::VAT_TYPE_NONE)
;

$item2 = new ReceiptItem();
$item2->setName('Жидкость для очистки струн Dunlop')
    ->setPrice(349)
    ->setVatType(ReceiptItem::VAT_TYPE_NONE)
;

$receipt = new Receipt($cashDesk);
$receipt->setOrderId('ORDER-ID-12345')
    ->setCustomerEmail('customer@email.com')
    ->addItem($item1)
    ->addItem($item2)
;

// Добавляем чек в кассу
$cashDesk->addReceipt($receipt);

// ...

// В коде, выполняемом, например, по крону, вызываем метод регистрации чеков
$apiCaller = new ApiCaller($onlineCashDeskConfig);
$cashDesk->registerPendingReceipts($apiCaller);
```