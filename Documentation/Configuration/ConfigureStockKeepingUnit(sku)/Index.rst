

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


Configure Stock Keeping Unit (SKU)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Each product has its own unique number (SKU) which helps to identify
the product for customers and sellers. Within wt\_cart you can display
this number within your cart and confirmation emails. There are 2
TypoScript  **constants** . You  **must not** set both values. Either
your SKU is sent as POST parameter or stored in your product database.

::

   plugin.wtcart {
     # if no database for products is used
     gpvar.sku =
     # if database for products is used
     db.sku = 
   }

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         gpvar.sku
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for your SKU.


.. container:: table-row

   Property
         db.sku
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the SKUs are stored
         (e.g. sku).


.. ###### END~OF~TABLE ######

