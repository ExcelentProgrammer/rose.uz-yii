const Objection = require('objection');
const { onError } = require('../config/methods');
const { Model } = Objection

class CategoryModel extends Model {
  static get tableName() {
    return 'category'
  }

  static get jsonSchema() {
    return {
      type: 'object',
      properties: {
        id: { type: 'integer' },
        bot_id: { type: 'integer' },
        name: { type: 'string' },
        position: { type: 'integer' },
        hidden: { type: 'boolean' },
      },
    }
  }

  static getCategories(bot_id) {
    return this.query()
      .where('bot_id', '=', bot_id)
      .orWhere('id', 13)
      .andWhere('hidden', 0)
      .orderBy('id', 'desc')
      .then(
        data => {
          return data.filter(x => !x.hidden).map( x => ({ id: x.id, name: x.name }) )
        }
      );
  }
}

module.exports = CategoryModel;
