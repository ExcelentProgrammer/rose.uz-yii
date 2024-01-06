const Markup = require('telegraf/markup');
const models = require('./models');
const fs = require('fs');
const { numberWithSpaces, getKeyboardButtons, parseSession, keyboardOptions } = require('./config/methods');
const knex = models.knex;

Object.defineProperty(Array.prototype, 'chunk_inefficient', {
    value: function(chunkSize) {
        var array=this;
        return [].concat.apply([],
            array.map(function(elem,i) {
                return i%chunkSize ? [] : [array.slice(i,i+chunkSize)];
            })
        );
    }
});

const productsToShow = parseInt(process.env.PRODUCTS_TO_SHOW);
const paymeToken = process.env.PAYME_TOKEN;
const clickToken = process.env.CLICK_TOKEN;
const keyboard = {
	main_menu: [
			['📋 Каталог', '🛒 Корзина'],
			['📦 Статус заказа', '🔍 Помощь']
		],
	askClearCart: [
		['Да, очистить корзину'],
		['Отмена']
	]
}
const paymentTypeKeyboard = [
	['Payme', 'payme'],
	['Click', 'click'],
	['Яндекс Деньги', 'yandex'],
  ['Виза-Мастеркард', 'yandex-visa'],
	['QIWI', 'qiwi'],
	['Webmoney', 'web-money'],
  ['Наличные', 'cash']
]
const convertToCurrency = value => numberWithSpaces(value) + ' сум';
const convertDate = date => [date.getDate(), ('0' + (date.getMonth() + 1)).slice(-2), date.getFullYear()].join('.');
const convertDateWithHours = date => [date.getDate(), ('0' + (date.getMonth() + 1)).slice(-2), date.getFullYear()].join('.') + ' ' + [date.getHours(), ('0' + (date.getMinutes() + 1)).slice(-2)].join(':');
const statuses = [
    "Создано",
    "Оплачено",
    "Принят",
    "Собирается",
    "Отправлено",
    "Доставлено",
    "Отменено",
];

const Shop = {
	/* text */
	start:
		(ctx) => {
			ctx.session.stage = '';
			ctx.session.checkout = {};
			ctx.reply('Главное меню', Markup.keyboard(keyboard.main_menu).resize().extra())
		},
	catalogue:
		async (ctx) => {
			ctx.session.stage = 'catalogue';
			let results = await models.CategoryModel.getCategories(2);
			// let inlineKeyboard = results.map(cat => Markup.callbackButton(cat.name, `category-${cat.name}-${cat.id};0`));
			let keyboard = ['Главное меню', '🛒 Корзина'].concat(results.map(cat => cat.name));
			ctx.reply(
				'Выберите категорию из меню',
				// Markup.inlineKeyboard(inlineKeyboard, { columns: 2 }).resize().extra()
				Markup.keyboard(keyboard, { columns: 2 }).resize().extra()
			);
		},
	cart:
		ctx => {
			ctx.reply(
				'Нажмите - Посмотреть корзину 🛍, чтобы увидеть текущее содержимое Вашей корзины.',
				Markup.inlineKeyboard([ Markup.switchToCurrentChatButton('Посмотреть корзину 🛍', ctx.state.user.id) ]).resize().extra()
			);
		},
	_cart:
		async ctx => {
			const query = ctx.from.id;
			session = await models.SessionsModel.getSession(query);
			if(!session.length)
				return ctx.reply(
					'В вашей корзине пусто'
				);
			session = parseSession(session[0].session);
			keys = Object.keys(session.cart);
			let products = [],
					total_amount = 0,
					total_price = 0;
			for(let i = 0; i < keys.length; ++i) {
				product = await models.ProductsModel.getProductById(keys[i]);
				if(session.cart[product[0].id]) {
					total_amount += session.cart[product[0].id];
					total_price += product[0].price * session.cart[product[0].id];
					products.push(product[0]);
				}
			}
			if(products.length == 0) {
				return ctx.reply(
					'В вашей корзине пусто'
				);
			}
			// result.push({
			// 	type: 'article',
			// 	id: query + Math.random(),
			// 	input_message_content: {
			// 		message_text: `очистить корзину`
			// 	},
			// 	title: '❌ ОЧИСТИТЬ КОРЗИНУ',
			// 	description: `Удалить все товары из корзины`
			// });
			// result.push({
			// 	type: 'article',
			// 	id: query + Math.random(),
			// 	input_message_content: {
			// 		message_text: `оформить заказ`
			// 	},
			// 	title: '✔️ ОФОРМИТЬ ЗАКАЗ',
			// 	description: `В корзине: ${total_amount}\nИтого: ${convertToCurrency(total_price)}`
			// })
			let keyboard = products.map(product => `📝#${product.id}`);
			keyboard = keyboard.chunk_inefficient(2);
			keyboard = keyboard.concat([
				['❌ ОЧИСТИТЬ КОРЗИНУ'],
				['✔️ ОФОРМИТЬ ЗАКАЗ'],
        ['Главное меню'],
			]);
			ctx.replyWithMarkdown(
				'*«Наименование»* - редактировать одну позицию\n\n' +
				products.reduce((p, c) => p + `*${c.name}*\nСумма: ${convertToCurrency(c.price * session.cart[c.id])}\nКоличество: ${session.cart[c.id]}\n`,'') +
				`\nВ корзине: ${total_amount}\nИтого: ${convertToCurrency(total_price)}`,
				Markup.keyboard(keyboard).resize().extra()
			)
		},
	product:
		async ctx => {
			const product_id = ctx.message.text.substring(ctx.message.text.indexOf('📝#') + 3);
			let product = await models.ProductsModel.getProductById(product_id);
			if(!product.length)
				return ctx.reply('Нет такого товара');
			product = product[0];
			let buttons = await getKeyboardButtons(ctx, product_id);
			let options = keyboardOptions(ctx, product_id);
			let keyboard = Markup.inlineKeyboard(buttons, options).resize().extra();
			keyboard.caption = `${product.name}\nЦена: ${convertToCurrency(product.price)}${product.description && '\nОписание: ' + product.description}\n${ctx.session.cart[product.id] && `В корзине: ${ctx.session.cart[product.id]}\nСумма: ${convertToCurrency(ctx.session.cart[product.id] * product.price)}` || ''}`;
			return ctx.replyWithPhoto(
				{ url: process.env.URL_IMAGES + product.photo },
				keyboard
			);
		},
	askClearCart:
		ctx =>
			ctx.reply('Вы точно решили удалить все товары из Вашей корзины?', Markup.keyboard(keyboard.askClearCart).resize().extra()),
	clearCart:
		ctx => {
			ctx.session.cart = {};
			ctx.reply('Ваша корзина очищена', Markup.keyboard(keyboard.main_menu).resize().extra());
		},
	checkout:
		(ctx, calendar) => {
			let session = ctx.session;
			if(ctx.message.text == '✔️ ОФОРМИТЬ ЗАКАЗ') {
				session.stage = 'sender_name';
				session.checkout = {};
				return ctx.reply('Введите имя отправителя', Markup.keyboard( [ctx.state.user.first_name, 'Отмена'] ).resize().extra());
			}
			switch(session.stage) {
				case 'sender_name':
					session.checkout.chat_id = ctx.chat.id;
					session.checkout.sender_name = ctx.message.text;
					session.stage = 'sender_phone';
					return ctx.reply(
						'Введите номер отправителя',
						Markup.keyboard( [Markup.contactRequestButton('Отправить мой номер телеграма'), 'Отмена'] ).resize().oneTime().extra()
					);
				case 'sender_phone':
					session.checkout.sender_phone = ctx.message.text;
					session.stage = 'receiver_phone';
					return ctx.reply('Введите номер получателя', Markup.keyboard(['Отмена']).resize().extra());
				case 'receiver_phone':
					session.checkout.receiver_phone = ctx.message.text;
					session.stage = 'card_text';
					return ctx.reply('Вы можете добавить открытку с вашим письмом в букет. Напишите текст открытки.', Markup.keyboard(['Пропустить','Отмена']).resize().extra());
				case 'card_text':
					session.checkout.card_text = ctx.message.text;
					session.stage = 'receiver_address';
					return ctx.reply('Введите адрес доставки', Markup.keyboard(['Узнать у получателя','Отмена']).resize().extra());
				case 'receiver_address':
					session.checkout.receiver_address = ctx.message.text;
					session.stage = 'receiver_name';
					return ctx.reply('Введите имя получателя', Markup.keyboard(['Отмена']).resize().extra());
				case 'receiver_name':
					session.checkout.receiver_name = ctx.message.text;
					session.stage = 'delivery_date';
					calendar.setMinDate(new Date());
					return ctx.reply('Выберите дату доставки', calendar.getCalendar());
				default:
					return Shop.start(ctx);
			}
		},
	cancel:
		ctx => Shop.start(ctx),
	successfulPayment:
		async ctx => {
			ctx.session = {};
			(function() {
				ctx.session = {};
				(() => ctx.session = {})();
			})();
			models.UserModel.getAdmins().then(admins => {
				ctx.session = {};
				if(admins.length) {
					ctx.session = {};
					admins.forEach(async admin => {
						let order_id = (await models.OrdersModel.lastOrder(ctx.update.message.chat.id));
						order_id = order_id[order_id.length - 1];
						const answer = `Новый заказ: /cart_${order_id.id}\nСтатус: ${statuses[order_id.state]}\nДата: ${convertDate(order_id.date)}`;
						return ctx.telegram.sendMessage(admin.id, answer);
					});
				}
			}).then(() => ctx.reply('Оплата была произведена', Markup.keyboard(keyboard.main_menu).resize().extra()));
		},
	help:
		ctx =>
			ctx.reply(`/catalog — Каталог
/cart — Корзина
/status — Статус заказа
/help — Помощь`),
	status:
		async ctx => {
			let order_ids = await models.OrdersModel.getOrders(ctx.state.user.id);
			if(!order_ids.length) {
				return ctx.reply('Вы еще ничего не заказали');
			}
			let answer = '';
			for(let i = 0; i < order_ids.length; ++i) {
				order_id = order_ids[i];
				answer += `Номер заказа: /cart_${order_id.id}\nСтатус: ${statuses[order_id.state]}\nДата: ${convertDateWithHours(order_id.date)}\n======\n\n`;
			}
			return ctx.reply(answer, Markup.keyboard(['Мой последний заказ', 'Главное меню']).resize().extra());
		},
	lastOrder:
		async ctx => {
			let order_id = (await models.OrdersModel.lastOrder(ctx.state.user.id));
			order_id = order_id[order_id.length - 1];
			const answer = `Номер заказа: /cart_${order_id.id}\nСтатус: ${statuses[order_id.state]}\nДата: ${convertDateWithHours(order_id.date)}`;
			return ctx.reply(answer, Markup.keyboard(keyboard.main_menu).resize().extra());
		},
	cartHistory:
		async ctx => {
			const order_id = ctx.message.text.substring(ctx.message.text.indexOf('_') + 1);
			const isAdmin = await models.UserModel.isAdmin(ctx.state.user.id);
			const doesBelongsTo = await models.OrdersModel.doesBelongsTo(order_id, ctx.state.user.id);
			if(!isAdmin && !doesBelongsTo) {
					return ctx.reply('Нет такого заказа');
			}
			let orderItems = await models.OrderItemsModel.getOrderItems(order_id);
			if(orderItems.length) {
				let order = (await models.OrdersModel.getOrder(order_id))[0];
				let msg = {
					'🔵 Имя отправителя': order.sender_name,
					'📱 Номер отправителя': order.sender_phone,
					'🔴 Имя получателя': order.receiver_name,
					'📞 Номер получателя': order.receiver_phone,
					'📬 Адрес доставки': order.receiver_address || 'Узнать у получателя',
					'📃 Текст открытки': order.card_text || 'Нет открытки',
					'⚙️ Статус': statuses[order.state],
					'💵 Платежная система': order.payment_type,
					'📦 Дата доставки': convertDate(order.delivery_date)
				}
				let total_price = 0;
				for(let i = 0; i < orderItems.length; ++i) {
					let order = orderItems[i];
					let product = (await models.ProductsModel.getProductById(order.product_id))[0];
					total_price += product.price * order.amount;
					let answer = `Название: ${product.name}\nЦена: ${convertToCurrency(product.price)}\nКоличество: ${order.amount}\nСумма: ${convertToCurrency(product.price * order.amount)}`;
					await ctx.replyWithPhoto(
						{ url: process.env.URL_IMAGES + product.photo },
						{ caption: answer }
					);
				}
				ctx.replyWithHTML(
					Object.keys(msg).reduce((string, key) => string + `<strong>${key}</strong>: ${msg[key]}\n`, `<strong>Итого:</strong> ${convertToCurrency(total_price)}\n`),
					isAdmin && Markup.inlineKeyboard([ Markup.callbackButton('Изменить статус', `status-${order_id}-`) ]).resize().extra()
				);
			}
		},
	/* contact */
	onContact:
		ctx => {
			const phone = ctx.update.message.contact.phone_number;
			models.UserModel.setPhone(ctx.state.user.id, phone);
			if(ctx.session.stage == 'sender_phone') {
				ctx.session.checkout.sender_phone = phone;
				ctx.session.stage = 'receiver_phone';
				return ctx.reply('Введите номер получателя', Markup.keyboard(['Отмена']).resize().extra());
			}
			return Shop.start(ctx);
		},
	/* date */
	onDate:
		(ctx, date) => {
			if(ctx.session.stage == 'delivery_date') {
				ctx.session.checkout.delivery_date = date;
				ctx.session.stage = 'checkout';
				const keyboard = Markup.inlineKeyboard(
					paymentTypeKeyboard.map(x => Markup.callbackButton(x[0], 'payment-type-' + x[1])), { columns: 1 }
				).resize().extra();
				return ctx.reply('Выберите способ оплаты', keyboard);
			}
			return Shop.start(ctx);
		},
	/* actions */
	changeStatus:
		async ctx => {
			const order_id = ctx.match.input.substring(ctx.match.input.indexOf('-') + 1);
			const status = ctx.match.input.substring(ctx.match.input.lastIndexOf('-') + 1);

			if(status) {
				await models.OrdersModel
					.updateStatus(order_id, status)
					.then(() => models.OrdersModel.getOrder(order_id))
					.then(res => ctx.telegram.sendMessage(res[0].chat_id, `Произошло изменение в заказе /cart_${order_id}. Статус изменен на ${statuses[status]}`));
			}
			let order = (await models.OrdersModel.getOrder(order_id))[0];
			let msg = {
				'🔵 Имя отправителя': order.sender_name,
				'📱 Номер отправителя': order.sender_phone,
				'🔴 Имя получателя': order.receiver_name,
				'📞 Номер получателя': order.receiver_phone,
				'📬 Адрес доставки': order.receiver_address || 'Узнать у получателя',
				'📃 Текст открытки': order.card_text || 'Нет открытки',
				'⚙️ Статус': statuses[order.state],
				'💵 Платежная система': order.payment_type,
				'📦 Дата доставки': convertDate(order.delivery_date)
			}
			const keyboard =
				status ?
					[ Markup.callbackButton('Изменить статус', `status-${order_id}-`) ] :
					statuses.map(status => Markup.callbackButton(status, `status-${order_id}-${statuses.indexOf(status)}`));
			ctx.editMessageText(
				Object.keys(msg).reduce((string, key) => string + `${key}: ${msg[key]}\n`, ''),
				Markup.inlineKeyboard(keyboard, { columns: 1 }).resize().extra()
			);
			ctx.answerCbQuery();
		},
	paymentType:
		ctx => {
			let session = ctx.session;
			if(session.stage != 'checkout') {
				ctx.answerCbQuery();
				return;
			}
			const input = ctx.match.input;
			const payment_type = input.substring(input.lastIndexOf('-') + 1);
			debugger;
			session.checkout.payment_type = payment_type;
			session.stage = '';
			session.checkout.delivery_date = new Date(session.checkout.delivery_date);
			if(session.checkout.receiver_address == 'Узнать у получателя') {
				session.checkout.receiver_address = '';
				session.checkout.know_address = true;
			}
			if(session.checkout.card_text == 'Пропустить') {
				session.checkout.card_text = '';
				session.checkout.add_card = false;
			}
			ctx.answerCbQuery();
			return models.OrdersModel
				.saveOrder(session.checkout)
				.then(async res => {
					id = res.id;
					let { cart } = ctx.session;
					let amount = 0;
					let cartKeys = Object.keys(cart);
					for(let i = 0; i < cartKeys.length; ++i) {
						let key = cartKeys[i];
						await models.ProductsModel
							.getProductById(key)
							.then(async product => {
								product = product[0];
								amount += cart[key] * product.price;
								let orderItem = {
									order_id: id,
									product_id: product.id,
									product_name: product.name,
									amount: cart[key],
									price: product.price
								};
								await models.OrderItemsModel.saveOrderItem(orderItem);
							});
					}
          if (payment_type == 'cash') {
            return ctx.replyWithMarkdown(
              'Заказ создан. Наш менеджер скоро с вами свяжется',
            )
          }
          ctx.replyWithMarkdown(
            'Заказ создан. Оплатите его нажав на кнопку "оплатить"',
            Markup.inlineKeyboard([
              Markup.urlButton('Оплатить', `https://rose.uz/pay/${id}/${payment_type}`)
            ]).resize().extra()
          )
					// if(payment_type == 'payme' || payment_type == 'click') {
					// 	let invoice = {
					// 		provider_token: payment_type == 'payme' ? paymeToken : clickToken,
					// 		start_parameter: id,
					// 		title: `Оплата через ${payment_type == 'payme' ? 'Payme' : 'Click'}`,
					// 		description: `Номер заказа: ${id}\nСумма: ${convertToCurrency(amount)}\nЧтобы перейти к оплате нажмите кнопку "Оплатить"`,
					// 		currency: 'uzs',
					// 		is_flexible: false,
					// 		prices: [
					// 			{ label: amount+' so\'m', amount: amount*100 },
					// 		],
					// 		need_name: false,
					// 		need_phone_number: false,
					// 		need_email: false,
					// 		need_shipping_address: false,
					// 		payload: {
					// 			id, amount
					// 		}
					// 	}
					// 	return ctx.replyWithInvoice(invoice, 'Оплатить');
					// }
					throw err;
				})
				// .then(() => ctx.reply('Заказ принят'))
				.catch(() => {
					ctx.session = {};
					(function() {
						ctx.session = {};
						(() => ctx.session = {})();
					})();
					return Shop.start(ctx);
				});
		},
	categories:
		async (ctx) => {
			let text = ctx.message.text;
			let category = (await models.CategoryModel.getCategories(2)).find(category => category.name === text);
			let cat_id = category.id;
			let product_ids = await models.ProductCategoriesModel.getProductIds(cat_id);
			let offset = 0;
			let products = await knex
				.select('*')
				.from('product_categories')
				.where('cat_id', '=', cat_id)
        .andWhere('products.hidden', 0)
				.leftJoin('products', 'product_categories.product_id', 'products.id')
				.orderBy('product_id')
				.offset(offset)
				.limit(productsToShow);

			if(products.length == 0) {
				let buttons = await getKeyboardButtons(ctx, ctx.session.showMoreButton.id);
				buttons.push(Markup.callbackButton('Больше товаров нет', '---'));
				ctx.editMessageReplyMarkup(
					Markup.inlineKeyboard(
						buttons,
						keyboardOptions(ctx, ctx.session.showMoreButton.id)
					)
				).then(() => ctx.session.showMoreButton.id = 0)
			}
			if(offset == 0)
				ctx.reply(`⬇️ ${category.name}`);
			else {
				ctx.editMessageReplyMarkup(
					Markup.inlineKeyboard(
						await getKeyboardButtons(ctx, ctx.session.showMoreButton.id),
						keyboardOptions(ctx, ctx.session.showMoreButton.id)
					)
				);
			}

			offset += productsToShow;
			for(let i = 0; i < products.length; ++i) {
				const product = products[i];
				let buttons = await getKeyboardButtons(ctx, product.id);
				let options = keyboardOptions(ctx, product.id);
				if(i == products.length - 1) {
					buttons.push(Markup.callbackButton('Загрузить еще', `category-${category.name}-${cat_id};${offset}`));
					ctx.session.showMoreButton = {
						id: product.id, cat_name: category.name, cat_id, offset
					}
				}
				let keyboard = Markup.inlineKeyboard(buttons, options).resize().extra();
				keyboard.caption = `${product.name}\nЦена: ${convertToCurrency(product.price)}${product.description && '\nОписание: ' + product.description}\n${ctx.session.cart[product.id] && `В корзине: ${ctx.session.cart[product.id]}\nСумма: ${convertToCurrency(ctx.session.cart[product.id] * product.price)}`  || ''}`;
				await ctx.replyWithPhoto(
					{ url: process.env.URL_IMAGES + product.photo },
					keyboard
				);
			}
			ctx.answerCbQuery();
		},
	products:
		async (ctx) => {
			let text = ctx.message.text;
			let [_, cat_id] = ctx.session.stage.split(':');
			let products = await knex
				.select('*')
				.from('product_categories')
				.where('cat_id', '=', cat_id)
        .andWhere('products.hidden', 0)
				.leftJoin('products', 'product_categories.product_id', 'products.id')
				.orderBy('product_id');
			let product = products.find(x => x.name === text);
			let buttons = await getKeyboardButtons(ctx, product.id);
			let options = keyboardOptions(ctx, product.id);
			let keyboard = Markup.inlineKeyboard(buttons, options).resize().extra();
			keyboard.caption = `${product.name}\nЦена: ${convertToCurrency(product.price)}${product.description && '\nОписание: ' + product.description}\n${ctx.session.cart[product.id] && `В корзине: ${ctx.session.cart[product.id]}\nСумма: ${convertToCurrency(ctx.session.cart[product.id] * product.price)}`  || ''}`;
			await ctx.replyWithPhoto(
				{ url: process.env.URL_IMAGES + product.photo },
				keyboard
			);
		},
	onCategory:
		async (ctx) => {
			let input = ctx.match.input;
			let cat_name = input.substring(input.indexOf('-') + 1, input.lastIndexOf('-'));
			let cat_id = input.substring(input.lastIndexOf('-') + 1, input.indexOf(';'));
			let offset = parseInt(input.substr(input.indexOf(';') + 1));
			let product_ids = await models.ProductCategoriesModel.getProductIds(cat_id);
			let products = await knex
				.select('*')
				.from('product_categories')
				.where('cat_id', '=', cat_id)
        .andWhere('products.hidden', 0)
				.leftJoin('products', 'product_categories.product_id', 'products.id')
				.orderBy('product_id')
				.offset(offset)
				.limit(productsToShow);

			if(products.length == 0) {
				let buttons = await getKeyboardButtons(ctx, ctx.session.showMoreButton.id);
				buttons.push(Markup.callbackButton('Больше товаров нет', '---'));
				ctx.editMessageReplyMarkup(
					Markup.inlineKeyboard(
						buttons,
						keyboardOptions(ctx, ctx.session.showMoreButton.id)
					)
				).then(() => ctx.session.showMoreButton.id = 0)
			}
			if(offset == 0)
				ctx.editMessageText(`⬇️ ${cat_name}`);
			else {
				ctx.editMessageReplyMarkup(
					Markup.inlineKeyboard(
						await getKeyboardButtons(ctx, ctx.session.showMoreButton.id),
						keyboardOptions(ctx, ctx.session.showMoreButton.id)
					)
				);
			}

			offset += productsToShow;
			for(let i = 0; i < products.length; ++i) {
				const product = products[i];
				let buttons = await getKeyboardButtons(ctx, product.id);
				let options = keyboardOptions(ctx, product.id);
				if(i == products.length - 1) {
					buttons.push(Markup.callbackButton('Загрузить еще', `category-${cat_name}-${cat_id};${offset}`));
					ctx.session.showMoreButton = {
						id: product.id, cat_name, cat_id, offset
					}
				}
				let keyboard = Markup.inlineKeyboard(buttons, options).resize().extra();
				keyboard.caption = `${product.name}\nЦена: ${convertToCurrency(product.price)}${product.description && '\nОписание: ' + product.description}\n${ctx.session.cart[product.id] && `В корзине: ${ctx.session.cart[product.id]}\nСумма: ${convertToCurrency(ctx.session.cart[product.id] * product.price)}`  || ''}`;
				await ctx.replyWithPhoto(
					{ url: process.env.URL_IMAGES + product.photo },
					keyboard
				);
			}
			ctx.answerCbQuery();
		},
	onCart:
		async ctx => {
			let input = ctx.match.input;
			let product_id = input.substring(input.lastIndexOf('-') + 1);
			let command = input.substring(input.indexOf('-') + 1, input.lastIndexOf('-'));
			if(command == 'add') {
				ctx.session.cart[product_id] = 1;
			}
			else if(command == 'increment') {
				ctx.session.cart[product_id]++;
			}
			else if(command == 'decrement') {
				ctx.session.cart[product_id]--;
				if(ctx.session.cart[product_id] <= 0) {
					delete ctx.session.cart[product_id];
				}
			} else if(command == 'remove') {
				delete ctx.session.cart[product_id];
			}
			let buttons = await getKeyboardButtons(ctx, product_id);
			let options = keyboardOptions(ctx, product_id);
			models.ProductsModel.getProductById(product_id)
				.then(products => {
					product = products[0];
					ctx
						.editMessageCaption(`${product.name}\nЦена: ${convertToCurrency(product.price)}${product.description && '\nОписание: ' + product.description}\n${ctx.session.cart[product.id] && `В корзине: ${ctx.session.cart[product.id]}\nСумма: ${convertToCurrency(ctx.session.cart[product.id] * product.price)}` || ''}`)
						.then(() => {
							if(product.id == ctx.session.showMoreButton.id) {
								const { cat_name, cat_id, offset } = ctx.session.showMoreButton;
								buttons.push(Markup.callbackButton('Загрузить еще', `category-${cat_name}-${cat_id};${offset}`));
							}
							ctx.editMessageReplyMarkup(
								Markup.inlineKeyboard(buttons, options)
							).catch(err => err);
						});
				});
			ctx.answerCbQuery();
		},

	/* inline_query */
	inlineQuery:
		async ctx => {
			const query = ctx.inlineQuery.query;
			const empty_card = [{
				type: 'article',
				id: query + Math.random(),
				title: 'В этой корзине пусто',
				input_message_content: {
					message_text: '/catalog'
				}
			}];
			session = await models.SessionsModel.getSession(query);
			if(!session.length)
				return ctx.answerInlineQuery(empty_card, { cache_time: 0 });
			session = parseSession(session[0].session);
			keys = Object.keys(session.cart);
			let products = [],
					total_amount = 0,
					total_price = 0;
			for(let i = 0; i < keys.length; ++i) {
				product = await models.ProductsModel.getProductById(keys[i]);
				if(session.cart[product[0].id]) {
					total_amount += session.cart[product[0].id];
					total_price += product[0].price * session.cart[product[0].id];
					products.push(product[0]);
				}
			}
			let result = products.map(product => ({
				type: 'article',
				id: query + product.id + Math.random(),
				input_message_content: {
					message_text: `📝#${product.id}`
				},
				title: product.name,
				description: `Сумма: ${convertToCurrency(product.price * session.cart[product.id])}\nКоличество: ${session.cart[product.id]}`,
				thumb_url: process.env.URL_IMAGES + product.photo,
				thumb_width: 50,
				thumb_height: 50
			}));
			if(result.length == 0) {
				return ctx.answerInlineQuery(empty_card, { cache_time: 0 });
			}
			result.push({
				type: 'article',
				id: query + Math.random(),
				input_message_content: {
					message_text: `очистить корзину`
				},
				title: '❌ ОЧИСТИТЬ КОРЗИНУ',
				description: `Удалить все товары из корзины`
			});
			result.push({
				type: 'article',
				id: query + Math.random(),
				input_message_content: {
					message_text: `оформить заказ`
				},
				title: '✔️ ОФОРМИТЬ ЗАКАЗ',
				description: `В корзине: ${total_amount}\nИтого: ${convertToCurrency(total_price)}`
			})
			ctx.answerInlineQuery(result, { cache_time: 0, is_personal: true });
		},
}

module.exports = Shop;
