function post(url, data, callback) {
    return $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: callback,
        dataType: 'json'
    });
}

function updateTotalPrice(price) {
    var deliveryPrice = parseInt($("tr.cart-total-area span.delivery-price").attr("data-delivery-price"));
    var total = deliveryPrice + parseInt(price.replace(/\s/g, ""));
    $("div.shopping-cart-price span.cart-price").text(price);
    $("tr.cart-total-area span.position-total").text(price);
    $("tr.cart-total-area span.total").text(numberToPrice(total));
}

function numberToPrice(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

$.fn.datepicker.dates['ru'] = {
    days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
    daysShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
    daysMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
    monthsShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
    today: "Сегодня",
    clear: "Очистить",
    format: "dd.mm.yyyy",
    weekStart: 1
};

$().ready(function () {
    $().ready(function () {
        $("input[data-plugin=phone-mask]").inputmask("99-999-99-99");
    });
    $('[data-plugin=datepicker]').datepicker({
        "orientation": "bottom left",
        "format": "dd.mm.yyyy",
        "autoclose": true,
        "todayHighlight": true,
        "language": "ru",
        'startDate': "2019-03-08"
    });

    $('body').on('click', 'a[data-act=add-to-cart]', function () {
        var id = $(this).attr('data-id');
        // alert(window.location.hostname);
        post('/ajax/add-to-cart', {id: id}, function (r) {
            // console.log(r);
            if (r.state == "OK") {
                $("span#cart-badge").text(r.count);
                $("div.shopping-cart-price span.cart-price").text(r.total);
                $("#cart-block").removeClass('hidden');
            }
            $('body').append(r.alert);
            setTimeout(function () {
                $("div.alert-area").remove();
            }, 3000);
        });
        return false;
    });

    $('body').on('click', 'span[data-act=minus-quantity]', function () {
        var orderId = $(this).closest('table').attr("data-order-id");
        var tr = $(this).closest('tr');
        var itemId = tr.attr("data-id");
        var quantity = tr.attr("data-quantity");
        var price = parseFloat(tr.attr("data-price"));
        if (quantity && quantity === "1") {
            return false;
        }
        post('/ajax/minus-quantity', {orderId: orderId, itemId: itemId, quantity: quantity}, function (r) {
            if (r.state === "OK") {
                if (r.quantity === 1) {
                    tr.find("span.quantity-minus").addClass("op3");
                } else {
                    tr.find("span.quantity-minus").removeClass("op3");
                }
                tr.attr("data-quantity", r.quantity);
                tr.find("input.quantity-field").val(r.quantity);
                tr.find("span.uc-price").text(numberToPrice(r.itemPrice));
                updateTotalPrice(r.total);
            }
        });
        return false;
    });

    $('body').on('click', 'span[data-act=plus-quantity]', function () {
        var orderId = $(this).closest('table').attr("data-order-id");
        var tr = $(this).closest('tr');
        var itemId = tr.attr("data-id");
        var quantity = tr.attr("data-quantity");
        var price = tr.attr("data-price");

        post('/ajax/plus-quantity', {orderId: orderId, itemId: itemId, quantity: quantity}, function (r) {
            if (r.state === "OK") {
                tr.find("span.quantity-minus").removeClass("op3");
                tr.attr("data-quantity", r.quantity);
                tr.find("input.quantity-field").val(r.quantity);
                tr.find("span.uc-price").text(numberToPrice(r.itemPrice));
                updateTotalPrice(r.total);
            }
        });
        return false;
    });

    $('body').on('click', 'a[data-act=delete-item]', function () {
        var orderId = $(this).closest('table').attr("data-order-id");
        var tr = $(this).closest('tr');
        var itemId = tr.attr("data-id");

        post('/ajax/delete-item', {orderId: orderId, itemId: itemId}, function (r) {
            console.log(r);
            if (r.state === "OK") {
                tr.fadeOut(500);
                updateTotalPrice(r.total);
            }
        });
        return false;
    });

    $("input.quantity-field").focusout(function () {
        var obj = $(this);
        var orderId = obj.closest('table').attr("data-order-id");
        var tr = obj.closest("tr");
        var itemId = tr.attr("data-id");
        var price = tr.attr("data-price");
        var quantity = parseInt($(this).val());

        if (!quantity || quantity <= 0) {
            quantity = 1;
        }

        post('/ajax/set-quantity', {orderId: orderId, itemId: itemId, quantity: quantity}, function (r) {
            if (r.state === "OK") {
                if (r.quantity === 1) {
                    tr.find("span.quantity-minus").addClass("op3");
                } else {
                    tr.find("span.quantity-minus").removeClass("op3");
                }
                tr.attr("data-quantity", r.quantity);
                obj.val(r.quantity);
                tr.find("span.uc-price").text(numberToPrice(r.itemPrice));
                updateTotalPrice(r.total);
            }
        });
        return false;

    });

    $('body').on('click', 'button[data-act=add-gift]', function () {
        var id = $(this).attr('data-id');
        post('/ajax/add-to-cart', {id: id}, function (r) {
            // console.log(r);
            if (r.state == "OK") {
                window.location.reload();
            }
        });
        return false;
    });

    $("#add-card").click(function () {
        var isChecked = $('input[type=checkbox]:checked', $(this)).length > 0
        if (isChecked) {
            $("div.order-options-detail").slideDown();
        } else {
            $("div.order-options-detail").slideUp();
        }
    });

    $('body').on('click', 'button[data-act=callback]', function () {
        var number = $("#callback-number").val();
        $("#callbackModal div.input-group").removeClass("has-error");
        $("#callbackModal div.input-group").addClass("has-success");
        post('/ajax/send-callback', {number: number}, function (r) {
            if (r.state === "OK") {
                $("#callback-number").val("");
                $("#callbackModal").modal("hide");
                alert("Ваш запрос принят. Наши менеджеры свяжутся с вами. Спасибо за внимание!")
            }
        });
        return false;
    });
});