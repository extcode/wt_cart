

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


What does it do?
^^^^^^^^^^^^^^^^

- wt\_cart is a small but powerful extension which adds some shopping
  cart functionality to your TYPO3 installation. The extension neither
  allows you to create products, articles or categories nor includes a
  checkout functionality. The extension  **depends on powermail** which
  helps you to integrate the checkout process. Furthermore powermail is
  utilized for sending the order confirmation.

- wt\_cart is very flexible. First of all  **any item within your TYPO3
  installation could be a product** . After an easy installation and
  setting up a “add to cart button” you can add tt\_news items, cal
  events, seminars, items based on your own and individually created
  extension or even tt\_product items to wt\_cart. Second of all
  wt\_cart comes with a very  **powerful dynamic marker**
  functionality. With the help of TypoScript you can create individual
  markers. As soon as you've added a new marker it's available in your
  HTML template.

- wt\_cart is interesting for people who:
  
  - are typically running a small business / sell small amounts of
    products (wt\_cart is not limited to a specific amount of products but
    as soon as your shop grows you'll be looking for some special
    functions which will not be included in wt\_cart);
  
  - are looking for a smart, clean and flexible solution;
  
  - are NOT interested in installing tt\_products, commerce or any other
    TYPO3 shop extension;
  
  - are NOT looking for discounts / discount rules, gift certificats;
  
  - are NOT looking for special payment methods like credit card

- Technically wt\_cart is an extension which utilizes the TYPO3 session.
  Build your own add-to-cart form and send information like color, size
  and quantity as post parameters. These post parameters will be added
  to the TYPO3 session. wt\_cart handles the session data and – based on
  your setup – looks up a specific TYPO3 database table to present the
  product title and any other additional information.

- To stop mail abuse install wt\_spamshield.

