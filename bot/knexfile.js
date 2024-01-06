require('dotenv').config();

module.exports = {
  development: {
    client: 'mysql',
    connection: {
      database: process.env.DB_NAME,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      charset: 'utf8mb4',
    },
    pool: {
      min: 2,
      max: 10,
    }
  },

  staging: {
    client: 'mysql',
    connection: {
      database: process.env.DB_NAME,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      charset: 'utf8mb4',
    },
    pool: {
      min: 2,
      max: 10,
    }
  },

  production: {
    client: 'mysql',
    connection: {
      database: process.env.DB_NAME,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      charset: 'utf8mb4',
    },
    pool: {
      min: 2,
      max: 10,
    }
  },
}
