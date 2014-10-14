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


Configure powermail to work with wt\_cart
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Edit your powermail plugin and configure the email which is sent to
the sender or recipient respectively. For  **powermail 1.x** add the
following marker:

###POWERMAIL\_TYPOSCRIPT\_CART###

If you are using  **powermail 2.x** you have to add the following
line:

{f:cObject(typoscriptObjectPath:'lib.wt\_cart')}

The described marker displays the whole shopping cart information
provided by wt\_cart. After including it your powermail emails will
contain the shopping cart data.

**Attention** for users of powermail 2.x: Due to some changes in the
powermail algorithms it is not possible to display the lib (wt\_cart
content) on the powermail submit page. There are no plans to implement
this in the near future.

Furthermore you **cannot use the powermail form caching** ("Enable Form
caching [enableCaching]"). As soon as you enable the form caching the
powermail plugin is executed before the wt\_cart plugin. That means the
selected products are not written to the session and the powermail form
will not be displayed (a page refresh would be required which is not very
convenient).

Include wt\_cart marker in powermail 1.x plugin
"""""""""""""""""""""""""""""""""""""""""""""""

|usermanual-1|

Include wt\_cart libs in powermail 2.x plugin
"""""""""""""""""""""""""""""""""""""""""""""

|usermanual-2|

