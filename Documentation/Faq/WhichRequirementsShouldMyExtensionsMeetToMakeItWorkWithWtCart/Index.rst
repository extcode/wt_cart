

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


Which requirements should my extensions meet to make it work with wt\_cart?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

By analyzing the TypoScript constants you can see your extension
(which stores the products) should contain the following fields:

- An input field for the product title (string).

- An input field for the gross price (string).

- An input field for the product id (puid) which has to be an integer.

- A select field for the tax class. You could use the following
  classification: 1 for normal; 2 for reduced; 3 for free. This behavior
  can be easily changed by editing the TypoScript Setup.

