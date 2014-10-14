

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


How can I append an order form to my website?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

There are several ways to append an order form. You can put it in your
HTML template or in your extension template (TypoScript). The order
form is nothing special and you don't need any fancy functions. The
following example appends a form using TypoScript. As you can see it's
a bit more flexible and allows the usage of stdWrap functions.

Example Setup
~~~~~~~~~~~~~

Below you can find some example code for your TypoScript setup.

::

   temp.orderform = COA
   temp.orderform {
     prepend = COA
     prepend {
       10 = TEXT
       10.wrap = <form name="order" action="|" method="post"><fieldset id="add-to-cart">
       10.typolink.parameter = {$plugin.wtcart.main.pid} #uid of page with wt_cart plugin
       10.typolink.returnLast = url
     }
     # the name attribute of the input fields are very important, see TypoScript Constants
     value (
       <input type="hidden" name="tx_myext_pi1[showUid]" value="###TX_MYEXT.UID###" />
       <input type="text" name="tx_myext_qty" id="tx_myext_qty" value="1" />
       <input type="submit" name="submit" value="In den Warenkorb" />
     )
     append = COA
     append {
       10 = TEXT
       10.value = </fieldset></form>
     }
   }
   
   # included somewhere within your extension plugin
   lib.orderform < temp.orderform
   lib.orderform {
     # avoids display of empty form
     stdWrap.ifEmpty.cObject = TEXT
     stdWrap.ifEmpty.cObject.value =
   }

