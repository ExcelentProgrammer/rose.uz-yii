require('dotenv').config();
const Telegraf = require('telegraf');
const MySQLSession = require('telegraf-session-mysql');
const Markup = require('telegraf/markup');
const Calendar = require('telegraf-calendar-telegram');

const { chatUser, isPrivateChat } = require('./config/methods');
const { UserModel } = require('./models');
const shop = require('./routes');

/* configure bot and db */
const bot = new Telegraf(process.env.BOT_TOKEN);
const calendar = new Calendar(bot, {
    startWeekDay: 1,
    weekDayNames: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
    monthNames: [
        "Янв", "Фев", "Март", "Апр", "Май", "Июнь",
        "Июль", "Авг", "Сен", "Окт", "Нояб", "Дек"
    ],
});
const session = new MySQLSession({
	host: process.env.DB_HOST,
	user: process.env.DB_USER,
	password: process.env.DB_PASSWORD,
	database: process.env.DB_NAME
});

/* middleware */
bot.use(session.middleware());
bot.use((ctx, next) => {
	if (ctx.updateType !== 'inline_query' && ctx.updateType !== 'pre_checkout_query') {
		if(!ctx.session.cart) {
			ctx.session.cart = {};
		}
		if(!ctx.state.user) {
			return UserModel.store(ctx, next);
		}
	}
	return next(ctx);
})

/* calendar */
calendar.setDateListener((ctx, date) => {
	ctx.answerCbQuery();
	shop.onDate(ctx, date);
});

/* routes */
bot.start((ctx) => isPrivateChat(ctx) && shop.start(ctx));

bot.command('catalog', shop.catalogue);
bot.command('cart', shop.cart);
bot.command('help', shop.help);
bot.command('status', shop.status);
bot.command('admins', (ctx) => {
	UserModel.getAdmins().then(admins => {
		if(admins.length) {
			admins.forEach(async admin => {
				ctx.telegram.sendMessage(admin.id, 'test')
			});
		}
	});
});
bot.action(/^category/, shop.onCategory);
bot.action('cart', shop._cart);
bot.action(/^cart/, shop.onCart);
bot.action(/^payment-type/, shop.paymentType);
bot.hears('📋 Каталог', shop.catalogue);
bot.action(/^status/, shop.changeStatus);
bot.hears('🛒 Корзина', shop._cart);
bot.hears('🔍 Помощь', shop.help);
bot.hears('📦 Статус заказа', shop.status);
bot.hears('Да, очистить корзину', shop.clearCart);
bot.hears(['очистить корзину', '❌ ОЧИСТИТЬ КОРЗИНУ'], shop.askClearCart);
bot.hears(['оформить заказ', '✔️ ОФОРМИТЬ ЗАКАЗ'], shop.checkout);
bot.hears('Отмена', shop.cancel);
bot.hears('Мой последний заказ', shop.lastOrder);
bot.hears('Главное меню', shop.start);
bot.hears(/^📝#/, shop.product);

// for /cart_...
bot.hears(/.*?/, (ctx, next) => {
	if(ctx.message.text.indexOf('/cart_') > -1)
		return shop.cartHistory(ctx);
	return next(ctx);
});
bot.hears(/.*?/, (ctx, next) => {
	if (ctx.session.stage == 'catalogue')
		return shop.categories(ctx);
  else if (ctx.session.stage.indexOf('products') > -1)
    return shop.products(ctx);
	return next(ctx);
});


bot.hears(/.*?/, ctx => shop.checkout(ctx, calendar));
bot.on('contact', shop.onContact);
bot.on('inline_query', shop.inlineQuery);
bot.on('pre_checkout_query', ctx => {
	return ctx.answerPreCheckoutQuery(true).catch(err => err);
});
bot.on('successful_payment', shop.successfulPayment);

/* start polling */
bot.startPolling();
