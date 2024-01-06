const Objection = require('objection');
const Markup = require('telegraf/markup');
const { onError } = require('../config/methods');
const { Model } = Objection;

class ProductCategories extends Model {
	static get tableName() {
		return 'product_categories'
	}

	static get jsonSchema() {
		return {
			type: 'object',
			properties: {
				id: { type: 'integer' },
				cat_id: { type: 'integer' },
				product_id: { type: 'integer' },
			}
		}
	}

	static getProductIds(cat_id) {
		return this.query()
			.select('product_id')
			.where('cat_id', '=', cat_id)
			.then(results => results.map(x => x.product_id));
	}
}

module.exports = ProductCategories;