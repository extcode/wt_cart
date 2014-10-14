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


Configure multiple carts
^^^^^^^^^^^^^^^^^^^^^^^^

wt\_cart 1.4 allows you to set up multiple carts, i.e. you can have
more than one cart on your site. Just create the pages you need for
the checkout process and insert the wt\_cart plugin “wt\_cart : cart”.
After that you have to create new TypoScript extension templates for
each page tree containing your set of products. Then define within
each TS extension template the constant plugin.wtcart.main.pid.

See the screenshot below for an example. The products on page “Catalog
Cart 1” (uid 11) will be added to the wt\_cart plugin on page “Cart 1”
(uid 10). Products on page “Catalog 2” (uid 18) will be added to the
wt\_cart plugin on page “Cart 2” (uid 17). The only thing you have to
configure is the TS constant plugin.wtcart.main.pid on page uid 11 and
page uid 18.

|img-9|  *TypoScript configuration for multiple carts on one site*

Configure tax classes The whole tax system has changed with wt\_cart 2.0. We have removed
the literal strings “normal” and “reduced”. Now the tax system is
based on numbers (classes) which is cleaner and offers much more
flexibility. Furthermore there is also the possibility to handle tax
free products. The following example is taken from the TypoScript
constants which ships with wt\_cart 2.0.

::

   plugin.wtcart {
     # Enter the tax value for this tax class item (e.g. 19). Value will be displayed.
     taxclass.1.value = 19
     # Enter the tax rate for this tax class item (e.g. 0.19). Used for calculation.
     taxclass.1.calc = 0.19
     # Enter the name for this tax class item (e.g. normal). Value can be displayed.
     taxclass.1.name = normal
     # configure 2nd tax class
     taxclass.2.value = 7
     taxclass.2.calc = 0.07
     taxclass.2.name = reduced
     # configure 3rd tax class
     taxclass.3.value = 0
     taxclass.3.calc = 0.00
     taxclass.3.name = free
   }

**Before you upgrade to wt\_cart 2.0 test the new tax system on a test
system!1elf**

