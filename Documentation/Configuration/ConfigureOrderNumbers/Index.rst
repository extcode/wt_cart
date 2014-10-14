.. include:: Images.txt

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


Configure order numbers
^^^^^^^^^^^^^^^^^^^^^^^

Since wt\_cart 1.4 it is possible to use order numbers. The order
number is generated automatically.

There's an architectural problem in powermail 1.x. The order number is
generated as soon as the powermail order form is submitted for the
first time. That way your order numbers can be discontinuously. This
happens if the user cancels the order but has already submitted the
form (but not yet confirmed). We have to use a powermail hook. Right
now there is no better hook which allows us to create order numbers
reliably. This should be no problem since there is no law requesting
continuous order numbers.

You can display the order number as part of the email subject and
within the email body.


Email body
""""""""""

You can use the marker ###ORDERNUMBER### within the subpart
###WTCART\_POWERMAIL###. Edit your wt\_cart HTML template and change
and extend it as needed.


Email subject
"""""""""""""

Please open your powermail plugin and check the tabs for “Sender” and
“Recipients”. For powermail 1.x you can add the marker
###ORDERNUMBER### to the fields “Subject for sender's mail” and
“Subject for recipient's mail”. See screenshot for an example. For
powermail 2.x add
{f:cObject(typoscriptObjectPath:'lib.wt\_cart\_ordernumber')}.

|img-8|  *Set subject with order number for sender's email
confirmation (powermail 1.x)*

TypoScript Configuration There are 3 settings. Please see the following example. In this case
the first order number would be DE1000K.

::

   plugin.wtcart {
     # prefix for order number
     ordernumber.prefix = DE
     # suffix for order number
     ordernumber.suffix = K
     # offset for order number
     ordernumber.offset = 999
   }

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Property
         ordernumber.prefix
   
   Data type
         Text
   
   Description
         Prefix (prepend) for order number, e.g. DE.


.. container:: table-row

   Property
         ordernumber.suffix
   
   Data type
         Text
   
   Description
         Suffix (append) for order number, e.g. K.


.. container:: table-row

   Property
         ordernumber.offset
   
   Data type
         Int+
   
   Description
         Offset for order number, if the offset is added to the order number
         (e.g. the offset is 999 the first order number will be 1000).


.. ###### END~OF~TABLE ######

