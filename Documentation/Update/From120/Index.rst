

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


From < 1.2.0
^^^^^^^^^^^^

With introducing the new shipping and payment feature we had to clean
up the code a bit and introduce new markers. Furthermore we had to
change some names of existing markers. This was important to guarantee
the future development and the consistency of the extension. Therefore
you have to update your own HTML extension template to keep your mini
TYPO3 store running. See the following table to get more information
about changed markers:

.. ### BEGIN~OF~TABLE ###


.. container:: table-row

   Old marker
         WTCART\_LL\_AMOUNT
   
   New marker
         WTCART\_LL\_QTY
   
   Comment
         Localized string for quantity.


.. container:: table-row

   Old marker
         WTCART\_LL\_NET\_TOTAL
   
   New marker
         WTCART\_LL\_CART\_NET
   
   Comment
         Localized string for total cart net.


.. container:: table-row

   Old marker
         WTCART\_LL\_VAT
   
   New marker
         WTCART\_LL\_TAX
   
   Comment
         Localized string for tax rate.


.. container:: table-row

   Old marker
         TAX\_NORMAL
   
   New marker
         TAXRATE\_NORMAL\_STRING
   
   Comment
         Localized string for tax rate normal.


.. container:: table-row

   Old marker
         TAX\_REDUCED
   
   New marker
         TAXRATE\_REDUCED\_STRING
   
   Comment
         Localized string for tax rate reduced.


.. container:: table-row

   Old marker
         AMOUNT
   
   New marker
         QTY
   
   Comment
         Quantity of specific product.


.. container:: table-row

   Old marker
         TAX\_OVERALL\_NORMAL
   
   New marker
         CART\_TAX\_NORMAL
   
   Comment
         Total tax normal tax rate.


.. container:: table-row

   Old marker
         TAX\_OVERALL\_REDUCED
   
   New marker
         CART\_TAX\_REDUCED
   
   Comment
         Total tax reduced tax rate.


.. container:: table-row

   Old marker
         n/a

   
   New marker
         CART\_SERVICE\_COST\_NET
   
   Comment
         New marker. Total net shipping and payment.


.. container:: table-row

   Old marker
         n/a
   
   New marker
         CART\_SERVICE\_COST\_GROSS
   
   Comment
         New marker. Total gross shipping and payment.


.. container:: table-row

   Old marker
         NETTO\_TOTAL
   
   New marker
         CART\_NET\_NO\_SERVICE
   
   Comment
         Total cart net, including shipping and payment.


.. container:: table-row

   Old marker
         n/a
   
   New marker
         CART\_NET
   
   Comment
         New marker. Total cart net, excluding shipping and payment.


.. container:: table-row

   Old marker
         n/a
   
   New marker
         CART\_GROSS\_NO\_SERVICE
   
   Comment
         New marker. Total cart gross excluding shipping and payment.


.. container:: table-row

   Old marker
         PRICE\_TOTAL\_OVERALL
   
   New marker
         CART\_GROSS
   
   Comment
         Total cart gross including shipping and payment.


.. container:: table-row

   Old marker
         TAX\_OVERALL
   
   New marker
         n/a
   
   Comment
         Deleted. No function anymore.


.. container:: table-row

   Old marker
         AMOUNT\_OVERALL
   
   New marker
         n/a
   
   Comment
         Deleted. No function anymore.


.. container:: table-row

   Old marker
         PRICE\_OVERALL
   
   New marker
         n/a
   
   Comment
         Deleted. No function anymore.


.. ###### END~OF~TABLE ######

