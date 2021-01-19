<?php

$schema['vendors_contact_info'] = array (
	'templates' => array (
		'addons/vendor_info/blocks/vendor_information_new.tpl' => array(),
		'addons/vendor_info/blocks/vendor_location.tpl' => array(),
		),
	'wrappers' => 'blocks/wrappers',
        'content' => array(
            'vendor_info' => array(
                'type' => 'function',
                'function' => array('fn_blocks_get_vendor_info'),
            )
        ),
        'cache' => array(
            'update_handlers' => array('companies', 'company_descriptions', 'logos', 'images_links', 'images'),
            'request_handlers' => array('company_id')
        ),

	);

return $schema;