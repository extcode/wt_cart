

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


From < 3.0.0
^^^^^^^^^^^^

When upgrading to wt\_cart 3.x you don't need to worry. There are no
breaking changes and the upgrade from 2.x to 3.x should run smoothly.
The new major version was released to make wt\_cart compatible with
TYPO3 CMS 6.2 and powermail 2.1.x (with support of 2.0.x and 1.6.x
as well).

With wt\_cart 3.0.0 it is also possible to change the confirmation page.
There is now a new subpart ###WTCART_CONFIRMATION### in the HTML
templates which allows you to display the cart with all calculated
information again. But you can also completely change the output and
display whatever you like.