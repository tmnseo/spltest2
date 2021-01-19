  ALTER TABLE `?:orders`
            ADD COLUMN `cp_warehouse_id` int(11) unsigned NOT NULL DEFAULT '0',
            ADD KEY `cp_warehouse_id` (`cp_warehouse_id`);