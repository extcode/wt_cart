

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


Specials
^^^^^^^^

With the “Specials” feature you can display additional checkboxes
below the shipping and payment methods. Use cases:

- Order a print catalog for 2,50 Euros.

- Request gift wrapping for 5,00 Dollars.

See the following example for both use cases.


Example Setup
"""""""""""""

::

   plugin.tx_wtcart_pi1.special {
     options {
       1 {
         title = Order catalog
         extra = 2.50
       }
       2 {
         title = Gift wrapping
         extra = each
         extra.1.extra = 5.00
         tax = normal
       }
     }
   }

Using the setting extra = each (see option 2 for gift wrapping) the
price is added for each product in your cart (respecting the quantity
as well). The settings available for shipping and payment are also
available for the specials.

