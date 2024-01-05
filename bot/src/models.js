const Knex = require('knex');
const dbConfig = require('../knexfile');
const knex = Knex(dbConfig['development']);

module.exports = require('./model')(knex);
module.exports.knex = knex;