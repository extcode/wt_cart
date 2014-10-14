

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


Configure multiple data sources
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

It is possible to add products to your cart from multiple data sources
(i.e. database tables). Imagine the following scenario.
Your products are based on tt\_news, cal and your own extension called
myfancyproducts. In the past you had to create 3 wt_cart plugins to handle
this use case. Now you only need on wt_cart plugin.

Example constants
"""""""""""""""""

Below you can find an example for your TypoScript constants.

::

   plugin.tx_wtcart_pi1 {
     gpvar.tableId = tx_myext_tableid
     db {
       table1 = tt_news
       table2 = tx_cal_event 
       table3 = tx_myfancyproducts_domain_model_event
     }
   }

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         gpvar.tableId\*
   
   Data type
         Text
   
   Description
         Enter the correct POST parameter name which stores the product table id
         (integer) of your order form. wt\_cart uses this number for internal purposes.
         It can't be a string. The tableId will never be displayed.


.. container:: table-row

   Property
         db.table1 … db.tableN\*
   
   Data type
         Text
   
   Description
         You can set up n tables which store your products (e.g. tt\_news).
         db.table is still working and not affected by db.table1


.. ###### END~OF~TABLE ######

Furthermore you have to extend your order forms (on each list or detail page).
According to our example above you have to add 3 order forms to your tt\_news
single view, your cal single view and your myfancyproducts single view. The
order view must include a hidden field with the name tx\_myext\_tableid.
Within this field you add the static value 1 (for tt\_news), 2 (for cal) or 3
(for myfancyproducts).
