module.exports = (sequelize, Sequelize) => {
  const Product = sequelize.define("product", {
    sku: {
      type: Sequelize.STRING
    },
    name: {
      type: Sequelize.STRING
    },
    description: {
      type: Sequelize.TEXT('medium')
    },
    netPrice: {
      type: Sequelize.FLOAT
    },
    vat: {
      type: Sequelize.FLOAT
    },
    stock: {
      type: Sequelize.INTEGER
    },
    enabled: {
      type: Sequelize.BOOLEAN
    },
    attributes: {
      type: Sequelize.JSON
    },
    grossPrice: {
      type: Sequelize.VIRTUAL,
      get() {
        return this.netPrice * (1 + this.vat / 100);
      },
    }
  });

  return Product;
};
