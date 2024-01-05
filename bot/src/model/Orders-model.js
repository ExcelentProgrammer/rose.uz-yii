const Objection = require('objection');
const Markup = require('telegraf/markup');
const { onError } = require('../config/methods');
const { Model } = Objection;

class OrdersModel extends Model {
	static get tableName() {
		return 'orders';
	}

	static get jsonSchema() {
		return {
			type: 'object',
			properties: {
				id: { type: 'integer' },
				chat_id: { type: 'integer' },
				system_id: { type: 'integer', maxLength: 1 },
				client_type: { type: 'string', maxLength: 20 },
				payment_type: { type: 'string', maxLength: 50 },
				date: { type: 'timestamp' },
				sender_name: { type: 'string', maxLength: 50 },
				sender_phone: { type: 'string', maxLength: 30 },
				sender_email: { type: 'string', maxLength: 50 },
				receiver_name: { type: 'string', maxLength: 50 },
				receiver_phone: { type: 'string', maxLength: 30 },
				delivery_date: { type: 'date' },
				delivery_price: { type: 'decimal', precision: 10, scale: 2 },
				receiver_address: { type: 'text' },
				know_address: { type: 'boolean', default: false },
				add_card: { type: 'boolean', default: true },
				take_photo: { type: 'boolean', default: true },
				card_text: { type: 'text' },
				state: { type: 'int', maxLength: 1 },
				total_paid: { type: 'decimal', precision: 10, scale: 2 },
			}
		}
	}

	static saveOrder(order) {
		return this.query().insert(order);
	}

	static getOrder(id) {
		return this.query()
			.select('*')
			.where('id', '=', id);
	}

	static getOrders(chat_id) {
		return this.query()
			.select('*')
			.where('chat_id', '=', chat_id);
	}

	static lastOrder(chat_id) {
		return this.query()
			.select('*')
			.where('chat_id', '=', chat_id)
			.orderBy('date');
	}

	static doesBelongsTo(id, chat_id) {
		return this.query()
			.select('*')
			.where('id', '=', id)
			.andWhere('chat_id', '=', chat_id)
			.then(res => res.length)
	}

	static updateStatus(id, state) {
		return this.query()
			.update({ state })
			.where('id', '=', id);
	}
}

module.exports = OrdersModel;