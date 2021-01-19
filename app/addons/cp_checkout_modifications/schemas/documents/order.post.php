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

$schema['order']['attributes'][] = 'cp_is_delivery_to_TC';

/*$schema['cp_user'] = array(
   'class' => '\Tygh\Addons\CpCheckoutModifications\Documents\Order\CpUser'
   );*/

$schema['user'] = array(
   'class'      => '\Tygh\Template\Document\Variables\GenericVariable',
   'alias'      => 'u',
   'data'       => function (\Tygh\Template\Document\Order\Context $context) { 
      $cp_new_context = fn_cp_checkout_modifications_add_custom_profile_fields($context);
      $context = $context->getOrder()->getUser();
      $context = array_merge($context, $cp_new_context);
      return $context;
   },
   'attributes' => function () {
      $attributes = ['email', 'firstname', 'lastname', 'phone', 'legal_address', 's_office'];
      $group_fields = fn_get_profile_fields('I');
      $sections = ['C', 'B', 'S'];

      foreach ($sections as $section) {
          if (isset($group_fields[$section])) {
              foreach ($group_fields[$section] as $field) {
                  if (!empty($field['field_name'])) {
                      $attributes[] = $field['field_name'];

                      if (in_array($field['field_type'], ['A', 'O'])) {
                          $attributes[] = $field['field_name'] . '_descr';
                      }
                  }
              }
          }

          $attributes[strtolower($section) . '_fields']['[0..N]'] = [
              'name',
              'value',
          ];
      }

      return $attributes;
   }
);

return $schema;