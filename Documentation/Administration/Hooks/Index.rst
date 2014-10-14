

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


Hooks
^^^^^

The following hooks are available to extend the functionality of wt_cart.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         changeCartAfterDeleteProduct

   Description
         See changeCartBeforeDeleteProduct.


.. container:: table-row

   Property
         changeCartAfterLoad

   Description
         The cart has been loaded from the session. With this hook one can change
         the whole cart. This is useful if you want to receive information from
         external sources like a PIM or ERP and check the warehouse stock.


.. container:: table-row

   Property
         changeCartBeforeDeleteProduct

   Description
         The whole cart can be changed as soon as a product is deleted from the cart.
         This is useful to delete other related products or to offer alternative
         products etc.


.. container:: table-row

   Property
         changeCartBeforeSave

   Description
         See changeCartAfterLoad. This hook can be used for implementing validation
         rules like the mininmal or maximal amount of ordered products.


.. container:: table-row

   Property
         changeFieldArrayBeforeRenderVariant

   Description
         Before the variants are ready for rendering the contained data can be changed.
         The TypoScript settings for the rendering process have not been applied, yet
         (i.e. no stdWrap-ing etc.). The session data will not be changed.


.. container:: table-row

   Property
         changeMarkerArrayBeforeRenderProductItemWithVariants

   Description
         Same as changeFieldArrayBeforeRenderVariant but the marker array of the product
         data (and not the variant data) can be changed.


.. container:: table-row

   Property
         changeMarkerArrayBeforeRenderVariant

   Description
         Same as changeFieldArrayBeforeRenderVariant but the TypoScript settings have
         been applied and the marker array is built.


.. container:: table-row

   Property
         changeProductBeforeAddToCart
   
   Description
         Product can be changed before it is added to wt\_cart. Inside the
         hook the whole product data is available and can be used for further
         manipulation.


.. container:: table-row

   Property
         changeServicesBeforeSave
   
   Description
         Triggered before changeCartBeforeSave. Can be used to remove/ disable
         services (payment or shipping methods, specials) based on the products
         in the cart.


.. container:: table-row

   Property
         changeVariantDiscount

   Description
         Triggered after the price of a **variant** is calculated (see chapter
         :ref:`methodsforcalculatingpricesofvariants-label`). The hook does not know the
         final price of the variant, but the parts which will result in the final
         price. The following information are available: method for calculating the
         price, price of variant, price of parent, calculated discount. Use case:
         grant a specific user group a special discount.

.. container:: table-row

   Property
         $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wt_cart']['beforeAddAttachmentToMail']
   
   Description
         If powermail 2.x is used for generating the checkout forms you can use this hook
         to attach files to the order success email. powermail'\s signalslot
         "slotCreateActionBeforeRenderView" is utilized. This hook is used by the brand
         new extension ""wt_cart_pdf".

.. ###### END~OF~TABLE ######
