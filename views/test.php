<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<button id="payButton">salom</button>

<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>

<script>
    let btn = document.getElementById("payButton")
    //let language = navigator.language //or fix
    let language = "ru-RU"

    function pay() {
        var widget = new cp.CloudPayments({
            language: language
        })
        widget.pay('auth', // или 'charge'
            { //options
                publicId: 'test_api_00000000000000000000002', //id из личного кабинета
                description: 'Оплата товаров в example.com', //назначение
                amount: 1000, //сумма
                currency: 'RUB', //валюта
                accountId: 'user@example.com', //идентификатор плательщика (необязательно)
                invoiceId: '1234567', //номер заказа  (необязательно)
                skin: "mini", //дизайн виджета (необязательно)
                autoClose: 3
            }, {
                onSuccess: function(options) { // success
                    //действие при успешной оплате
                },
                onFail: function(reason, options) { // fail
                    //действие при неуспешной оплате
                },
                onComplete: function(paymentResult, options) { //Вызывается как только виджет получает от api.cloudpayments ответ с результатом транзакции.
                    //например вызов вашей аналитики Facebook Pixel
                }
            }
        )
    }

    //window.addEventListener('load', pay)
    btn.addEventListener('click', pay)
</script>
</body>
</html>