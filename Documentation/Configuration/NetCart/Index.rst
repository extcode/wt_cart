

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


Cart based on net prices
^^^^^^^^^^^^^^^^^^^^^^^^

If you want to use wt\_cart within a B2B environment it is quite
often necessary to work on net prices. That is your product prices are
stored excluding taxes. Therefore wt\_cart has to calculate and display
prices differently.

Constants
"""""""""

::

   plugin.tx_wtcart_pi1 {
     main {
       isNetCart = 1
     }
   }

The setting above configures wt\_cart to work with net prices. This feature is **still beta** and needs some testing. So please report us your problems ;)
