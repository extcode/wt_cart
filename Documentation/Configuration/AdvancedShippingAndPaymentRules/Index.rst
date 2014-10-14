

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


Advanced shipping and payment rules
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The  **service costs** (for shipping and payment and even for
specials) can depend on different parameters:

- total gross price of your products

- total quantity

- 3 additional parameters which can be configured individually

Furthermore the service costs can be calculated based on each product
in your cart.

Right now it is not possible to combine these rules within one
shipping or payment method, i.e. you have to decide between one rule
for each method. For example the shipping method 1 is based on the
overall quantity in your cart but it cannot be based on the overall
quantity  **AND** the overall weight (individual parameter).

The table below shows the possible syntax for your individual
configuration.  **It is valid for shipping, payment and special
costs.**

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         options.n.extra
   
   Data type
         Text
   
   Description
         Possible values:
         
         \- any decimal number, e.g. 5.00
         
         \- by\_price
         
         \- by\_quantity
         
         \- by\_service\_attribute\_1\_sum
         
         \- by\_service\_attribute\_2\_sum
         
         \- by\_service\_attribute\_3\_sum
         
         \- by\_service\_attribute\_1\_max
         
         \- by\_service\_attribute\_2\_max
         
         \- by\_service\_attribute\_3\_max
         
         \- each
   
   Default
         1


.. container:: table-row

   Property
         options.n.extra.1 … options.n.extra.n
   
   Data type
         Array
   
   Description
         You can set up n ranges.


.. container:: table-row

   Property
         options.n.extra.n.value
   
   Data type
         Text
   
   Description
         The value determines the range, e.g. 21.
         
         You do not have to set this entry if you are using options.n.extra =
         each.


.. container:: table-row

   Property
         options.n.extra.n.extra
   
   Data type
         Text
   
   Description
         Enter the specific price for this range, e.g. 12.00.


.. ###### END~OF~TABLE ######


Price based calculation (by\_price)
"""""""""""""""""""""""""""""""""""

With this rule you can calculate one of your service costs depending
on the total gross price for all products.


Example setup
~~~~~~~~~~~~~

::

   plugin.tx_wtcart_pi1.shipping {
     options {
       1 {
         title = Standard
         extra = by_price
         extra {
           1 {
             value = 0
             extra = 10.00
           }
           2 {
             value = 100
             extra = 7.50
           }
           3 {
             value = 200
             extra = 5.00
           }
         }
         tax = normal
       }
     }
   }

In the example above we configure a special shipping rule based on the
price. There is one shipping method called “Standard”, the normal tax
will be applied.

- If the total gross price of all products is lower than 100.00 the
  shipping costs will be 10.00.

- If the total gross price of all products is lower than 200.00 the
  shipping costs will be 7.50.

- If the total gross price of all products is equal to or higher than
  200.00 the shipping costs will be 5.00.


Quantity based calculation (by\_quantity)
"""""""""""""""""""""""""""""""""""""""""

With this rule you can calculate one of your service costs depending
on the total quantity for products.


Example constants
~~~~~~~~~~~~~~~~~

::

   plugin.wtcart.main.quantitySymbol = Pcs.

With the setting above you can configure the symbol or abbreviation
which is displayed as suffix of the quantity figure.


Example setup
~~~~~~~~~~~~~

::

   plugin.tx_wtcart_pi1.shipping {
     options {
       1 {
         title = Standard
         extra = by_quantity
         extra {
           1 {
             value = 1
             extra = 5.35
           }
           2 {
             value = 4
             extra = 7.10
           }
           3 {
             value = 11
             extra = 9.70
           }
           4 {
             value = 21
             extra = 11.60
           }
         }
         tax = normal
       }
     }
   }

In the example above we configure a special shipping rule based on the
quantity. There is one shipping method called “Standard”, the normal
tax will be applied.

- If the total quantity of all products is lower than 4 the shipping
  costs will be 5.35.

- If the total quantity of all products is lower than 11 the shipping
  costs will be 7.10.

- If the total quantity of all products is lower than 21 the shipping
  costs will be 9.70.

- If the total quantity of all products is equal to or higher than 21
  the shipping costs will be 11.60.


Using an individual attribute for calculation (by\_service\_attribute *\_n* \_sum)
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

With this rule you can calculate one of your service costs depending
on the total of an individual attribute for all products.

The individual attributes are configured via TypoScript (see the
section “Main configuration” of this manual). You have to set either
the correct database field (plugin.wt\_cart.db.service\_attribute\_n)
or the corresponding POST parameter
(plugin.wt\_cart.gpvar.service\_attribute\_n).

If you are using the frontend plugin “wt\_cart : add to cart” (pi2)
you can use the field “Additional Attributes” to set your individual
attribute. The syntax is as follows: “service\_attribute\_n==value”,
e.g. “service\_attribute\_1==300”. You can set more than one
attribute. Just use a separate line.


Example constants
~~~~~~~~~~~~~~~~~

::

   plugin.wtcart {
     db.service_attribute_1 = weight
     main.service_attribute_1_symbol = lbs
   }

The constants set the database field and the abbreviation which is
displayed as suffix of the quantity value.


Example setup
~~~~~~~~~~~~~

::

   plugin.tx_wtcart_pi1.shipping {
     options {
       1 {
         title = Standard
         extra = by_service_attribute_1_sum
         extra {
           1 {
             value = 600
             extra = 4.00
           }
           2 {
             value = 800
             extra = 6.00
           }
           3 {
             value = 1000
             extra = 12.00
           }
         }
         tax = normal
       }
     }
   }

In the example above we configure a special shipping rule based on the
total of an individual attribute “weight” in pounds (lbs). The
attribute is stored in your database.

- If the total weight of all products is lower than 600 the shipping
  costs will be 0.00.

- If the total weight of all products is lower than 800 the shipping
  costs will be 4.00.

- If the total weight of all products is lower than 1.000 the shipping
  costs will be 6.00.

- If the total weight of all products is equal to or higher than 1.000
  the shipping costs will be 12.00.


Using an individual attribute for calculation (by\_service\_attribute *\_n* \_max)
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

With this rule you can calculate one of your service costs depending
on the maximum value of an individual attribute. For example this rule
could be used for sending posters. The longest poster determines the
length of the packaging.


Example constants
~~~~~~~~~~~~~~~~~

::

   plugin.wtcart {
     gpvar.service_attribute_2 = service_attribute_2
     main.service_attribute_2_symbol = cm
   }

The constants set the POST parameter for the 2nd service attribute and
the abbreviation which is displayed as suffix of the quantity value.


Example setup
~~~~~~~~~~~~~

::

   plugin.tx_wtcart_pi1.shipping {
     options {
       1 {
         title = Standard
         extra = by_service_attribute_2_max
         extra {
           1 {
             value = 0
             extra = 4.00
           }
           2 {
             value = 80
             extra = 6.00
           }
           3 {
             value = 100
             extra = 8.00
           }
           4 {
             value = 200
             extra = 12.00
           }
         }
         tax = normal
       }
     }
   }

In the example above we configure a special shipping rule based on the
maximum of an individual attribute “length” in centimeters (cm). The
attribute is a POST parameter.

- If the maximum length is lower than 80 the shipping costs will be
  4.00.

- If the maximum length is lower than 100 the shipping costs will be
  6.00.

- If the maximum length is lower than 200 the shipping costs will be
  8.00.

- If the maximum length is equal to or higher than 200 the shipping
  costs will be 12.00.

Please not that the quantity of a specific poster is not important.
The same poster does not get longer if you buy 2 or 3 etc.


Calculation for each product (each)
"""""""""""""""""""""""""""""""""""

With this rule you can calculate one of your service costs depending
on the total quantity of all products. The quantity will be multiplied
by the price you set for each product.


Example setup
~~~~~~~~~~~~~

::

   plugin.tx_wtcart_pi1.shipping {
     options {
       1 {
         title = Standard
         extra = each
         extra {
           1 {
             extra = 2.00
           }
         }
         tax = normal
       }
     }
   }

In the example above we configure a special shipping rule for each
product. There is one shipping method called “Standard”, the normal
tax will be applied.

- The shipping costs will be 2.00 for each product in your cart.

- If you have got 10 products in your cart (total quantity) the shipping
  will be 20.00.

