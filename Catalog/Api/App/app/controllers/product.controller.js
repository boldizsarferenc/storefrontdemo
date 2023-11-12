const db = require("../models");
const products = db.products;

exports.getBySku = (req, res) => {
  if (!req.params.sku) {
    res.status(400).send({
      message: "Email can not be empty!"
    });
    return;
  }

  products.findOne({where: {sku: req.params.sku}})
      .then(data => {
        if (data) {
          res.send(data);
        } else {
          res.status(401).send({
            message: `Porduct is not found`
          });
        }
      })
      .catch(err => {
        res.status(500).send({
          message: "Error on getting Porduct"
        });
      });
}

exports.create = (req, res) => {

  if (
      !req.body.sku ||
      !req.body.netPrice ||
      !req.body.vat ||
      !req.body.name
  ) {
    res.status(400).send({
      message: "Missing property | Required: sku, netPrice, vat, name"
    });
    return;
  }

  const product = {
    sku: req.body.sku,
    name: req.body.lastname,
    description: req.body.description,
    netPrice: req.body.price,
    vat: req.body.vat,
    attribute: req.body.attributes
  };

  products.create(product)
    .then(data => {
      res.send(data);
    })
    .catch(err => {
      res.status(500).send({
        message:
          err.message || "Some error occurred while creating the product."
      });
    });
};

exports.findAll = (req, res) => {

  products.findAll()
    .then(data => {
      res.send(data);
    })
    .catch(err => {
      res.status(500).send({
        message:
          err.message || "Some error occurred while retrieving products."
      });
    });
};

exports.findOne = (req, res) => {
  const id = req.params.id;

  products.findByPk(id)
    .then(data => {
      if (data) {
        res.send(data);
      } else {
        res.status(404).send({
          message: `Cannot find product with id=${id}.`
        });
      }
    })
    .catch(err => {
      res.status(500).send({
        message: `Error retrieving product with id=${id}`
      });
    });
};

exports.update = (req, res) => {
  const id = req.params.id;

  products.update(req.body, {
    where: { id: id }
  })
    .then(num => {
      if (num == 1) {
        res.send({
          message: "Product was updated successfully."
        });
      } else {
        res.send({
          message: `Cannot update product with id=${id}. Maybe product was not found or req.body is empty!`
        });
      }
    })
    .catch(err => {
      res.status(500).send({
        message: `Error updating producct with id=${id}`
      });
    });
};

exports.delete = (req, res) => {
  const id = req.params.id;

  products.destroy({
    where: { id: id }
  })
    .then(num => {
      if (num == 1) {
        res.send({
          message: "Product was deleted successfully!"
        });
      } else {
        res.send({
          message: `Cannot delete product with id=${id}. Maybe product was not found!`
        });
      }
    })
    .catch(err => {
      res.status(500).send({
        message: `Could not delete product with id=${id}`
      });
    });
};

exports.subtractStock = async (req, res) => {
  const id = req.params.id;
  const quantity = Number(req.query.quantity);
  if(!quantity) {
    res.status(400).send({
      message: `Missing or invalid quantity parameter!`
    });

    return
  }

  try {
    const foundProduct = await products.findByPk(id)

    const newStock = foundProduct.stock - quantity
    if (newStock < 0) {
      res.status(409).send({
        message: `Stock not enough!`
      });

      return
    }

    await foundProduct.update({stock: newStock})
    await foundProduct.save()

    res.status(200).send({
      foundProduct
    });
  } catch (err) {
    res.status(500).send({
      message: `Cannot update product with id=${id}!`
    });
  }
}

exports.addStock = async (req, res) => {
  const id = req.params.id;
  const quantity = Number(req.query.quantity);
  if(!quantity) {
    res.status(400).send({
      message: `Missing or invalid quantity parameter!`
    });

    return
  }

  try {
    const foundProduct = await products.findByPk(id)

    const newStock = Number(foundProduct.stock) + quantity

    await foundProduct.update({stock: newStock})
    await foundProduct.save()

    res.status(200).send({
      foundProduct
    });
  } catch (err) {
    res.send(500).send({
      message: `Cannot update product with id=${id}!`
    });
  }
}
