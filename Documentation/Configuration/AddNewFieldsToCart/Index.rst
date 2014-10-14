

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


.. _addnewfieldtowtcart-label:

Add new fields to wt\_cart
^^^^^^^^^^^^^^^^^^^^^^^^^^

There are different ways to display additional product data inside your cart
(see chapter :ref:`howcanidisplayadditionalproductdatainwtcart-label`). Compared
to the FAQ article there is now an easier way to achieve the same objective. Despite
from only displaying the data the following method also allows you to make the data
available in hooks. 

First of all you have to configure which fields of your database you want to display.

Setup
"""""

::

   plugin.tx_wtcart_pi1 {
     db {
       additional {
         # left side: key, right side (after comma) database field name
         event_date.field = event_date
         event_time.field = event_time
       }
       variants {
         db {
           additional {
             pricegroup.field = pricegroup_category
             # value allows to set static value which is not stored in database
             tidconf.value = 1_5
           }
         }
       }
     }
   }


The example above configures four additional fields. Two fields are added to the product
itself and two fields are added to the variants. As the comments of the example show you
have to define a key like event_date oder tidconf. Furthermore you can set static values
(then use "value") or data which is represented within the database (then use "field").

Now you have to define the output of your fields.

Setup
"""""

::

   plugin.tx_wtcart_pi1 {
     settings {
       fields {
         additional {
           # marker name
           event_date = TEXT
           # set marker name to key defined in plugin.tx_wtcart_pi1.db.additional
           event_date.field = event_date
           event_date.wrap = <span class="event_date">|</span>
           event_date.if.isTrue.field = event_date
         }
       }
     }
   }

The example above defines a new TEXT cObject "event_date". You can use the whole power of
stdWrap to enhance your output. In order to display the output you have to add a new marker
to your HTML template. In general the marker\'s name is ###ADDITIONAL_KEY###. In our example
the marker\'s name would be ###ADDITIONAL_EVENT_DATE###.
