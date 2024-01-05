const Objection = require('objection');
const { onError } = require('../config/methods');
const { Model } = Objection

class ProductsModel extends Model {
  /**
   * The DB table name
   *
   * @static
   * @returns {string}
   */
  static get tableName() {
    return 'products'
  }

  /**
   * JSON schema getter
   *
   * @static
   * @returns {Object}
   */
  static get jsonSchema() {
    return {
      type: 'object',
      properties: {
        id: { type: 'integer' },
        category_id: { type: 'integer' },
        name: { type: 'string' },
        description: { type: 'text' },
        photo: { type: 'string' },
        price: { type: 'decimal', precision: 10, scale: 2 },
        recommend: { type: 'boolean', default: false }
      },
    }
  }

  static getProductById(id) {
    return this.query()
      .where('id', '=', id);
  }
}

module.exports = ProductsModel;