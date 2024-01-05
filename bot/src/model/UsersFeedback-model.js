const Objection = require('objection');
const Markup = require('telegraf/markup');
const { onError } = require('../config/methods');
const { Model } = Objection;

class UsersFeedbackModel extends Model {
	static get tableName() {
		return 'users_feedback'
	}

	static get jsonSchema() {
		return {
			type: 'object',
			properties: {
				user_id: { type: 'integer' },
				text: { type: 'string ' },
				cdate: { type: 'dateTime' }
			}
		}
	}

	static store(ctx, next) {
		const feedback = {
			user_id: ctx.from.id,
			text: ctx.message.text,
			cdate: new Date()
		}
		this.query()
			.insert({ ...feedback })
			.catch(onError)
	}
}

module.exports = UsersFeedbackModel;