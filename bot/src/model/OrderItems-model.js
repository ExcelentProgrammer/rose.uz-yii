const Objection = require('objection');
const Markup = require('telegraf/markup');
const { onError } = require('../config/methods');
const { Model } = Objection;

class OrderItemsModel extends Model {
	static get tableName() {
		return 'order_items'
	}

	static get jsonSchema() {
		return {
			type: 'object',
			properties: {
				id: { type: 'integer' },
				order_id: { type: 'integer' },
				product_id: { type: 'integer' },
				product_name: { type: 'string', maxLength: 255 },
				amount: { type: 'integer' },
				price: { type: 'decimal' },
			}
		}
	}

	static getOrderItems(id) {
		return this.query()
			.select('*')
			.where('order_id', '=', id);
	}

	static saveOrderItem(orderItem) {
		return this.query().insert(orderItem);
	}
}

module.exports = OrderItemsModel;