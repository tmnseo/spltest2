<?php
 /*****************************************************************************
*                                                        © 2013 Cart-Power   *
*           __   ______           __        ____                             *
*          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
*      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
*     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
*    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
*                                                                            *
*                                                                            *
* -------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license *
* and  accept to the terms of the License Agreement can install and use this *
* program.                                                                   *
* -------------------------------------------------------------------------- *
* website: https://store.cart-power.com                                      *
* email:   sales@cart-power.com                                              *
******************************************************************************/


namespace Tygh\Addons\CpCheckoutModifications\Documents\Order;

use Tygh\Registry;
use Tygh\Template\Document\Order\Context;
use Tygh\Template\IVariable;


/**
 * Class CpUser
 */
Class CpUser implements IVariable
{
   public $legal_address;

   public $office;
   /**
    * @param $order object
    * @return $user_id 
    */
   private function GetUserId($order)
   {
      return $order->data['user_id'];
   }

   /**
    * @param $profile_fields array
    * @return $legal_address_id  
    */
   /*private function getLegalAddressId($profile_fields)
   {
      foreach ($profile_fields['C'] as $field) {
         if ($field['field_name'] == 'legal_address') {
            return $field['field_id']; 
         }
      }
   }*/

   private function getFieldId($profile_fields, $field_name)
   {
      foreach ($profile_fields as $field) {
         if ($field['field_name'] == $field_name) {
            return $field['field_id']; 
         }
      }
   }
   /**
    * @param $user_id array
    * @param $desired_field_id
    * @return $field value   
    */
   private function getFieldValue($user_id, $desired_field_id)
   {
      $user_data = fn_get_user_info($user_id, true);
      if (!empty($user_data['fields'])) {
        foreach ($user_data['fields'] as $field_id => $field_value) {
           if ($field_id == $desired_field_id) {
              return $field_value;
           }
        }
      }
   }   
   public function __construct(Context $context)
   {
        $order = $context->getOrder();
        $user_id = $this->getUserId($order);
        $profile_fields = fn_get_profile_fields();
        $legal_address_id = $this->getFieldId($profile_fields['C'], 'legal_address');
        $office_id = $this->getFieldId($profile_fields['S'], 's_office');

        $this->legal_address = $this->getFieldValue($user_id, $legal_address_id);
        $this->office = $this->getFieldValue($user_id, $office_id);
   }
}