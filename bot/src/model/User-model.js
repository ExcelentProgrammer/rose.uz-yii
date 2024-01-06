const Objection = require('objection');
const { chatUser, onError } = require('../config/methods');
const { Model } = Objection

class UserModel extends Model {
  /**
   * The DB table name
   *
   * @static
   * @returns {string}
   */
  static get tableName() {
    return 'telegram_users'
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
      required: ['id', 'username'],
      properties: {
        id: { type: 'integer' },
        first_name: { type: 'string', maxLength: 150 },
        last_name: { type: 'string', maxLength: 150 },
        username: { type: 'string', maxLength: 150, default: '' },
        lang: { type: 'string', default: 'en' },
        is_bot: { type: 'boolean' },
        is_admin: { type: 'boolean', default: false },
        chat_type: { type: 'string', maxLength: 15 },
        phone: { type: 'string', maxLength: 20 }
      },
    }
  }

  static getAdmins() {
    return this.query()
      .select('*')
      .where('is_admin', true);
  }

  /**
   * Stores the user
   * @param ctx
   * @param next
   */
  static store(ctx, next) {
    const chatUserData = chatUser(ctx)

    this.query()
      .findById(chatUserData.id)
      .then((user) => {
        if (user) {
          ctx.state.user = user
          return next(ctx)
        }
        return (
          this.query()
            .insert({ ...chatUserData })
            .then((user) => {
              ctx.state.user = user
              next(ctx)
            })
            .catch(onError)
        )
      })
      .catch(onError)
  }

  static setPhone(id, phone) {
    this.query()
      .where('id', '=', id)
      .update({
        phone
      })
  }

  static isAdmin(id) {
    return this.query()
      .select('is_admin')
      .where('id', '=', id)
      .then(res => res[0].is_admin);
  }
}

module.exports = UserModel;