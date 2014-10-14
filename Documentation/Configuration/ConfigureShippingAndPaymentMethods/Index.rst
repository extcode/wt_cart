

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


Configure shipping and payment methods
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

With wt\_cart 1.2 comes a new functionality to display and calculate
different shipping and payment methods. Per default wt\_cart comes
with one shipping and one payment method. You can change these
existing methods and add new methods via TypoScript.

In wt\_cart 1.3.2 we have introduced 4 new settings for each shipping
and payment method (keywords:  **available\_from, available\_until,
free\_from, free\_until** ) and a general setting for shipping and
payment (keyword:  **show\_all\_disabled** ).

See the example and tables below for additional information.


Example Setup
"""""""""""""

Below you can find an example for your TypoScript setup.

::

   plugin.tx_wtcart_pi1.shipping {
     # set default shipping method
     preset = 1
     options {
       # shipping method 1
       1 {
         # title of shipping method
         title = Standard
         # extra cost (gross price) of shipping method
         extra = 5.00
         # free shipping if gross price is greater than or equal 50.00
         free_from = 50.00
         # tax rate which will be applied (e.g. 1 for normal; 2 for reduced; 3 for free)
         taxclass = 1
       }
       # shipping method 2
       2 {
         title = Express
         extra = 7.00
         taxclass = 1
       }
     }
   }
   
   plugin.tx_wtcart_pi1.payment {
     # set default payment method
     preset = 1
     # always show all payment methods even if they are not available
     show_all_disabled = 1
     options {
       # payment method 1
       1 {
         # title of payment method
         title = Cash on delivery
         # extra cost (gross price) of payment method
         extra = 2.00
         taxclass = 1
       }
       # payment method 2
       2 {
         title = Cash in advance
         # extra cost (gross price) of payment method
         extra = 0.00
         # method only available if gross price is less than 200.00
         available_until = 200.00
         taxclass = 1
         note (
           p><b>Please note:</b></p>
           <p>No money<br>
           No funny</p>
         )
       }
     }
   }

The example above introduces 2 shipping (standard and express) and 2
payments methods (cash on delivery and cash in advance). Furthermore
some special settings for availability are configured.


TypoScript Setup of plugin.tx\_wtcart\_pi1.shipping
"""""""""""""""""""""""""""""""""""""""""""""""""""

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         preset
   
   Data type
         Int+
   
   Description
         Set the default shippig method (e.g. 2).
   
   Default
         1


.. container:: table-row

   Property
         show\_all\_disabled
   
   Data type
         Boolean
   
   Description
         Always show all shipping methods even if they are not available.


.. container:: table-row

   Property
         options.1 … options.n
   
   Data type
         Array
   
   Description
         You can set up n shipping options.
   
   Default
         options.1


.. container:: table-row

   Property
         options.n.title
   
   Data type
         Text
   
   Description
         Title of the current option (e.g. Express shipping).
   
   Default
         Standard


.. container:: table-row

   Property
         options.n.extra
   
   Data type
         Text
   
   Description
         Extra cost (gross price) of current option (e.g. 4.50).
         
         Since wt\_cart 1.4 it is possible to configure special rules for
         calculating the service costs depending on specific parameters. Please
         see the chapter “Advanced shipping and payment rules” below.
   
   Default
         0.00


.. container:: table-row

   Property
         options.n.free\_from
   
   Data type
         Text
   
   Description
         If the gross price is greater than or equal to your value the cost of
         the current method will be 0. This could be used for the well known
         free shipping feature.


.. container:: table-row

   Property
         options.n.free\_until
   
   Data type
         Text
   
   Description
         If the gross price is less than or equal to your value the cost of the
         current method will be 0.


.. container:: table-row

   Property
         options.n.available\_from
   
   Data type
         Text
   
   Description
         If the gross price is greater than or equal to your value the current
         method becomes available.


.. container:: table-row

   Property
         options.n.available\_until
   
   Data type
         Text
   
   Description
         If the gross price is less than or equal to your value the current
         method is available.


.. container:: table-row

   Property
         options.n.taxclass
   
   Data type
         Int+
   
   Description
         Tax class which will be applied for current option (e.g. 1 for normal;
         2 for reduced; 3 for free).
   
   Default
         1


.. ###### END~OF~TABLE ######


TypoScript Setup of plugin.tx\_wtcart\_pi1.payment
""""""""""""""""""""""""""""""""""""""""""""""""""

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         preset
   
   Data type
         Int+
   
   Description
         Set the default payment method (e.g. 2).
   
   Default
         1


.. container:: table-row

   Property
         show\_all\_disabled
   
   Data type
         Boolean
   
   Description
         Always show all payment methods even if they are not available.


.. container:: table-row

   Property
         options.1 … options.n
   
   Data type
         Array
   
   Description
         You can set up n payment options.
   
   Default
         options.1


.. container:: table-row

   Property
         options.n.title
   
   Data type
         Text
   
   Description
         Title of the current option (e.g. Cash in advance).
   
   Default
         Standard


.. container:: table-row

   Property
         options.n.extra
   
   Data type
         Text
   
   Description
         Extra cost (gross price) of current option (e.g. 6.00).
         
         Since wt\_cart 1.4 it is possible to configure special rules for
         calculating the service costs depending on specific parameters. Please
         see the chapter “Advanced shipping and payment rules” below.
   
   Default
         0.00


.. container:: table-row

   Property
         options.n.free\_from
   
   Data type
         Text
   
   Description
         If the gross price is greater than or equal to your value the cost of
         the current method will be 0.


.. container:: table-row

   Property
         options.n.free\_until
   
   Data type
         Text
   
   Description
         If the gross price is less than or equal to your value the cost of the
         current method will be 0.


.. container:: table-row

   Property
         options.n.available\_from
   
   Data type
         Text
   
   Description
         If the gross price is greater than or equal to your value the current
         method becomes available.


.. container:: table-row

   Property
         options.n.available\_until
   
   Data type
         Text
   
   Description
         If the gross price is less than or equal to your value the current
         method is available.


.. container:: table-row

   Property
         options.n.taxclass
   
   Data type
         Int+
   
   Description
         Tax class which will be applied for current option (e.g. 1 for normal;
         2 for reduced; 3 for free).
   
   Default
         normal


.. container:: table-row

   Property
         options.n.note
   
   Data type
         Text
   
   Description
         Additional notes for option, important for methods like cash in
         advance etc. That way you can provide your bank account. You can use
         HTML to style the text.


.. ###### END~OF~TABLE ######

