module.exports = function(knex) {
	const CategoryModel = require('./Category-model');
	CategoryModel.knex(knex);
	const OrderItemsModel = require('./OrderItems-model');
	OrderItemsModel.knex(knex);
	const OrdersModel = require('./Orders-model');
	OrdersModel.knex(knex);
	const ProductCategoriesModel = require('./ProductCategories-model');
	ProductCategoriesModel.knex(knex);
	const ProductsModel = require('./Products-model');
	ProductsModel.knex(knex);
	const UserModel = require('./User-model');
	UserModel.knex(knex);
	const UsersFeedbackModel = require('./UsersFeedback-model');
	UsersFeedbackModel.knex(knex);
	const SessionsModel = require('./Sessions-model');
	SessionsModel.knex(knex);
	return {
		CategoryModel,
		OrderItemsModel,
		OrdersModel,
		ProductCategoriesModel,
		ProductsModel,
		UserModel,
		UsersFeedbackModel,
		SessionsModel
	}
}