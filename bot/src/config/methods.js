const Markup = require('telegraf/markup');

module.exports.chatUser = (({ from, chat, state }) => state.user || ({
	id: from.id,
	first_name: from.first_name,
	last_name: from.lastName,
	username: from.username,
	chat_type: chat.type,
	lang: from.language_code,
	phone: from.phone, // always undefined
	is_bot: from.is_bot,
	is_admin: false
}));

module.exports.onError = (err) => console.log(err);
module.exports.isPrivateChat = ({ chat }) => chat.type === 'private';

module.exports.numberWithSpaces = (x) => {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

module.exports.getKeyboardButtons = async (ctx, productId) => {
	const models = require('../models.js');
	const products = await models.ProductsModel.getProductById(productId);
	product = products[0];
	if(!ctx.session.cart[productId]) {
		return [
			Markup.callbackButton(`Добавить в корзину ➕`, `cart-add-${productId}`)
		]
	} else {
		return [
			Markup.callbackButton(`➕`, `cart-increment-${productId}`),
			Markup.callbackButton(`➖`, `cart-decrement-${productId}`),
			// Markup.callbackButton(`Удалить из корзины`, `cart-remove-${productId}`),
			Markup.callbackButton(`Перейти в корзину`, `cart`),
		]
	}
}

module.exports.getNumberOfColumns = (ctx, productId) => {
	if(!ctx.session.cart[productId]) {
		return 1;
	} else {
		return 2;
	}
}

module.exports.keyboardOptions = (ctx, productId) => {
	if(!ctx.session.cart[productId]) {
		return {
			columns: 1
		};
	} else {
		return {
			columns: 2,
			wrap: (btn, index, currentRow) => index > 1
		};
	}
}

module.exports.parseSession = (session) => {
	return JSON.parse(unescape(session));
}
