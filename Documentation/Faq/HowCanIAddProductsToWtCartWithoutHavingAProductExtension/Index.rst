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


How can I add products to wt\_cart without having a product extension?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Maybe you don't want to use tt\_news or any other extension for
storing your products. Maybe you just need two products and you want
to keep your installation simple and smart. Since wt\_cart 1.1.0 it is
possible to add items to the cart by using post params. A lot of TYPO3
users had problems to build an HTML form. The TYPO3 backend users
needed specific rights to include HTML content elements. This feels
somehow unsafe. Furthermore it is possible to change the POST
parameters easily.

Version 1.4 of wt\_cart allows you to add a small plugin “wt\_cart:
add to cart” to your pages. It avoids all the described problems
above. The screenshot below shows you the possible settings.

|img-10|

The plugin provides a flexform with the following fields:

- Unique Product Id (PUID): Please enter a unique number (integer).
  wt\_cart uses this number for internal purposes. It can't be a string.
  The PUID will never be displayed.

- Title: Enter a product title. It will be displayed in the cart and the
  order emails.

- Stock Keeping Unit (SKU): An alphanumeric string which is the visible
  identifier for your product. It will be displayed in the cart and the
  order emails.

- Price

- Tax Class: You can choose between 3 options: normal (internal value
  1), reduced (internal value 2) and free (internal value 3).

- Additional Attributes: You can send up to 3 attribute values -
  service\_attribute\_1, service\_attribute\_2, service\_attribute\_3.
  To set an value use the following notation: e.g.
  service\_attribute\_1==300. Check out the configuration section to
  learn how to configure special shipping and payment rules.

In the frontend the order form is added to your page content. You can
have multiple plugins on one page. The general configuration is – as
usual – done via TypoScript.

