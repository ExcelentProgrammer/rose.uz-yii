const Objection = require('objection');
const { onError } = require('../config/methods');
const { Model } = Objection;

class SessionsModel extends Model {
	static get tableName() {
		return 'sessions';
	}

	static get jsonSchema() {
		return {
			type: 'object',
			properties: {
				id: { type: 'string' },
				session: { type: 'string' }
			}
		}
	}

	static getSession(id) {
		if(isNaN(id))
			return [];
		return this.query()
			.select('session')
			.where('id', '=', `${id}:${id}`);
	}
}

module.exports = SessionsModel;