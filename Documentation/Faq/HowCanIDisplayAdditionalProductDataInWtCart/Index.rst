

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

.. _howcanidisplayadditionalproductdatainwtcart-label:

How can I display additional product data in wt\_cart?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

**Note**: The described method below is not the best way anymore. We
have added a new solution which is described in the chapter
:ref:`addnewfieldtowtcart-label`.

Maybe you want to display one image for each product in wt\_cart. This
can be easily achieved by adding some lines of Typoscript code to your
installation. We assume that your product table stores the name of the
product image, you've created an extension Typoscript template and
you're using your own HTML template for wt\_cart.

Add the following lines to your extension Typoscript template:


Setup
"""""

::

   plugin.tx_wtcart_pi1 {
     settings {
       fields {
         product_image = CONTENT
         product_image {
           # name of your db which stores your products
           table = tx_myext_tableName
           select {
             # uid of sysfolder where your products are stored
             pidInList = 7
             max = 1
             andWhere.cObject = TEXT
             andWhere.cObject.field = puid
             andWhere.cObject.wrap = uid=|
           }
           renderObj = IMAGE
           renderObj {
             # width of image
             file.width = 85
             # height of image
             file.height = 65
             # upload folder of your product extension
             file.import = uploads/tx_myext/
             file.import.field = image
             # select first image
             file.import.listNum = 0
             altText.field = title
           }
         }
       }
     }
   }

Furthermore you have to add the new marker ###PRODUCT\_IMAGE### to
your HTML template for wt\_cart.
