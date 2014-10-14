

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
:class:  typoscript
.. role::   php(code)


Configure variants
^^^^^^^^^^^^^^^^^^

The support of product variants was officially introduced in wt_cart 3.0
and is a big move forward but the setup can be quite complex. Variants allow you
to have different "shapes" of your product. Imagine you want to sell t-shirts.
Your product is a t-shirt of a certain brand. One variant (aka as "article")
would be the t-shirt, color green, size M. These product attributes (color, size)
will also be referred to as "variant dimensions" within this documentation.

General configuration and setup
"""""""""""""""""""""""""""""""

Each variant's dimension (e.g. color, size) has to be stored within a different database
table than your products. Nevertheless, it is not necessary to have a relation between
these tables. For the introduced example we need 3 database tables:

- table "tx_myext_domain_product": stores the product data (e.g. title, sku, description, image, data sheet)

- table "tx_myext_domain_color": stores the available colors (e.g. red, green, yellow)

- table "tx_myext_domain_size": stores the available sizes (e.g. XS, S, M, L, XL)

Furthermore you need to have at least the following Typoscript setup (based on our
example) to "map" the table and the database fields. For now one variant dimension
is enough (since the followin TS shall display the minimal setup).

Setup
~~~~~

::

  plugin.tx_wtcart_pi1 {
    ...
    db.variants {
      # db table which stores the available colors
      db.table = tx_myext_domain_color
      # db field which stores the color name
      db.title = title
      # db field which stores the price
      db.price = price
      # db field which stores the tax class
      db.taxclass = tax_class
    }
    ...
  }

::

Let's focus on the t-shirt example again and introduce the 2nd variant dimension.
It's quite easy and straight forward.

Setup
~~~~~

::

  plugin.tx_wtcart_pi1 {
    ...
    db.variants {
      # db table which stores the available colors
      db.table = tx_myext_domain_color
      # db field which stores the color name
      db.title = title
      # db field which stores the price
      db.price = price
      # db field which stores the tax class
      db.taxclass = tax_class
      db.variants {
        # db table which stores the available sizes
        db.table = tx_myext_domain_size
        db.title = title
        db.price = price
        db.taxclass = tax_class
      }
    }
    ...
  }

Price calculation
"""""""""""""""""

By default wt\_cart extinguishes that each variant dimension has its own price.
The process requests the price from the corresponding price field for each
variant dimension. The price of the deepest dimension will be utilized for
further calculations and will be displayed (= final price).

Example
~~~~~~~

- table "tx_myext_domain_product", product A, price = 10

- table "tx_myext_domain_color", color green,  price = 20

- table "tx_myext_domain_size", size M, price = 30

The example above depicts that the price for the product A is 10. If you create
a variant based on the color green the price in the cart will be 20. If you create
a variant based on the color green and the size M the price in the cart will
be 30.

This behaviour can be changed by either using a price inheritance or one of the
price calculation methods. Right now the settings price_calc_method and
inherit_price should not be combined for a variant since this has not been
tested intensively enough.

Price inheritance
~~~~~~~~~~~~~~~~~

If the price inheritance is activated for a variant the default price calculation
(see above) is not applied, i.e. the price of the respective variant is not
taken into consideration. Instead the price of the parent variant dimension/
product is utilized.

In order to use this feature you have to set the correct database field via TypoScript.
The following example does it for the 2nd variant dimension
(in our case "size").

::

  plugin.tx_wtcart_pi1.db.variants.db.variants.inherit_price = inherit_price

If the variant "t-shirt, color green, size M" with the activated flag "inherit_price"
is added to the cart the final price will be 20 (instead of 30).

.. _methodsforcalculatingpricesofvariants-label:

Price calculation methods
~~~~~~~~~~~~~~~~~~~~~~~~~

As described above you can define the method for calculating the price of
a **variant**. The database field which stores the method is defined via
the following TypoScript setup.

::

  plugin.tx_wtcart_pi1.db.variants.db.price_calc_method = ...

During the price calculation of a variant wt\_cart does the following:

* get the price calculation method for this variant
* get the "price" of the variant

After that wt\_cart calculates the final price of the variant.

Right now there are 5 methods available.

======   ===================================================================
Value    Price calulation method
======   ===================================================================
0        Fixed price; no connection to parent product, i.e. the price of
         the variant is used; Note: this is the default behaviour
1        Fixed discount on base price of parent product (e.g. 5.5)
2        Percental discount on base price of product; enter a value between
         0 and 100 (e.g. 80 for 80%)
3        Fixed surcharge on base price of parent product (e.g. 5.5)
4        Percental surcharge on base price of product; enter a value between
         0 and 100 (e.g. 80 for 80%)
======   ===================================================================

New variant dimensions
""""""""""""""""""""""

As shown above variants can have variants as well. Right now wt\_cart is pre-configured
to handle 3 dimensions of variants. If you want to have more dimensions than you have to
extend your TypoScript constants and setup (see below).

Constants
~~~~~~~~~

Enter the correct POST parameter name for the unique variant field (dimension 4) inside
your order form.

::

  plugin.wtcart.gpvar.variants.4 = variants|4

Setup
~~~~~

::

  plugin.tx_wtcart_pi1 {
    ...
    settings {
      variants {
        4 < .3
        4.data = GP:{$plugin.wtcart.gpvar.variants.4}
      }
    }
    ...
  }

Rendering of variant title and variant sku
""""""""""""""""""""""""""""""""""""""""""

If you dig deeper in the TS setup and the HTML template you will find 2 new fields which
output the title and the sku of a variant.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         plugin.tx_wtcart_pi1.settings.fields.title_with_variants

   Data type
         COA

   Description
         This content object array defines the rendering of the title of a variant
         inside the cart. Right now the above mentioned 3 levels of variants are used
         to generate the title. Since this cObject is an array you can easily extend it.
         stdWrap is also possible. If you have introduced a 4th level of variants and
         want to display these information here you have to extend the TS, accordingly.


.. container:: table-row

   Property
         plugin.tx_wtcart_pi1.settings.fields.sku_with_variants

   Data type
         COA

   Description
         This content object array defines the rendering of the sku of a variant inside
         the cart. See plugin.tx_wtcart_pi1.settings.fields.title_with_variants for
         further information.

.. ###### END~OF~TABLE ######

Additional fields
"""""""""""""""""

Furthermore, you can configure the following fields for your variants. They will also
be utilized by wt\_cart.

- l10n\_parent

- sku