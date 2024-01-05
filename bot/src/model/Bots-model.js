const Objection = require('objection');
const { onError } = require('../config/methods');
const { Model } = Objection

class BotsModel extends Model {
  /**
   * The DB table name
   *
   * @static
   * @returns {string}
   */
  static get tableName() {
    return 'bots'
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
        name: { type: 'string' }
      },
    }
  }

  /**
   * Stores the user
   * @param ctx
   * @param next
   */
  static store(name) {
    this.query()
      .insert({ name })
      .catch(onError)
  }
}

// module.exports = BotsModel;