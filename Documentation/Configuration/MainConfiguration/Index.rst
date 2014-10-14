

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


Main configuration
^^^^^^^^^^^^^^^^^^

There is nothing much to do. After you have accomplished all the steps
described above you have to set some TypoScript constants. Create a
new TypoScript extension template or use an existing one. You can use
the TypoScript Constants Editor. It will help you to configure the
main settings. The following settings are mandatory.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         gpvar.puid
   
   Data type
         Text
   
   Description
         Enter the correct POST parameter name for a unique product number
         (integer) of your order form. wt\_cart uses this number for internal
         purposes. It can't be a string. The PUID will never be displayed.
         
         If you are using a database table it could be the uid of the product
         (e.g. tx\_myext\_pi1\|showUid).
   
   Default
         puid


.. container:: table-row

   Property
         gpvar.qty
   
   Data type
         Text
   
   Description
         Enter the correct POST parameter name for the quantity/amount field of
         your order form (e.g. tx\_myext\_qty).
   
   Default
         qty


.. container:: table-row

   Property
         main.pid
   
   Data type
         Text
   
   Description
         Enter the uid of the page where wt\_cart plugin resides (e.g. 10).


.. container:: table-row

   Property
         powermailContent.uid
   
   Data type
         Int+
   
   Description
         Enter the uid of the powermail content element to hide this element if
         the cart is empty AND to clear cart if this form was submitted.


.. ###### END~OF~TABLE ######

wt\_cart can be based on a product database table but can also handle
data provided as POST parameters. You have to choose between these 2
different kind of settings.


Product database
""""""""""""""""

If your products are stored in a database table you can set the
following TypoScript constants (see table below). Fields marked with
\* are mandatory.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         db.table\*
   
   Data type
         Text
   
   Description
         Enter the correct table name where the products are stored (e.g.
         tx\_myext).


.. container:: table-row

   Property
         db.l10n\_parent
   
   Data type
         Text
   
   Description
         Enter the correct column name where the localisation parent is stored
         (e.g. l10n\_parent \| l18n\_parent). Some older extensions like
         tt\_news or tt\_content do not use the standard l10n\_parent. There
         the field is called l18n\_parent.
   
   Default
         l10n\_parent


.. container:: table-row

   Property
         db.title\*
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the product titles
         are stored (e.g. title).
   
   Default
         title


.. container:: table-row

   Property
         db.inherit\_price
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the inherit price
         flag is stored (e.g. inherit\_price).


.. container:: table-row

   Property
         db.price\*
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the prices are stored
         (e.g. price).
   
   Default
         price


.. container:: table-row

   Property
         db.taxclass\*
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the tax classes are
         stored (e.g. taxclass).
   
   Default
         taxclass


.. container:: table-row

   Property
         db.sku
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the SKU are stored
         (e.g. sku).


.. container:: table-row

   Property
         db.variants
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the variants are
         stored (e.g. variants).  **Please see the setup for variants below.**


.. container:: table-row

   Property
         db.service\_attribute\_1
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the service attribute
         1 are stored (e.g. for weight).


.. container:: table-row

   Property
         db.service\_attribute\_2
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the service attribute
         2 are stored (e.g. for volume).


.. container:: table-row

   Property
         db.service\_attribute\_3
   
   Data type
         Text
   
   Description
         Enter the correct column name of the table where the service attribute
         3 are stored (e.g. for length).


.. ###### END~OF~TABLE ######


POST parameter
""""""""""""""

If your products are somehow arranged and you want to send the needed
product data via POST parameter you can set the following TypoScript
constants (see table below). Fields marked with \* are mandatory.

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         gpvar.title
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the title field of your order
         form (e.g. title).
   
   Default
         title


.. container:: table-row

   Property
         gpvar.price
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the price field of your order
         form (e.g. price).
   
   Default
         price


.. container:: table-row

   Property
         gpvar.service\_attribute\_1
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the service attribute field 1 of
         your order form (e.g. weight).


.. container:: table-row

   Property
         gpvar.service\_attribute\_2
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the service attribute field 2 of
         your order form (e.g. volume).


.. container:: table-row

   Property
         gpvar.service\_attribute\_3
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the service attribute field 3 of
         your order form (e.g. length).


.. container:: table-row

   Property
         gpvar.sku
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for your SKU.


.. container:: table-row

   Property
         gpvar.variants.1
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the variant 1.


.. container:: table-row

   Property
         gpvar.variants.2
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the variant 2.


.. container:: table-row

   Property
         gpvar.variants.3
   
   Data type
         Text
   
   Description
         Enter the correct parameter name for the variant 3.


.. ###### END~OF~TABLE ######

