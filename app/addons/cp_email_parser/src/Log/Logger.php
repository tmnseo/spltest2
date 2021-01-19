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

namespace Tygh\Addons\CpEmailParser\Log;

use Tygh\Addons\CpEmailParser\Log\LogTypesEnum;
use Tygh\Addons\CpEmailParser\Log\LogStatusesEnum;

class Logger
{
    private $log_table = "?:cp_email_parser_log";
    private $log_id;
    private $parent_log_id = 0;
    private $start_time = 0;
    private $end_time = 0;
    private $log_type = LogTypesEnum::SUCCESS_PARSING;
    private $log_status = LogStatusesEnum::SUCCESS;
    private $log_message = '';

    function __construct()
    {   
        $this->start_time = time();
        $this->createLog();
    }

    public function setError($type)
    {
        $this->log_status = LogStatusesEnum::ERROR;
        $this->setType($type);
    }

    public function setType($type)
    {
        $this->log_type = $type;
    }

    public function setMess($mess)
    {
        $this->log_message = $mess;
    }

    public function setParentLogId($id)
    {
        $this->parent_log_id = $id;
    }

    public function getLogId()
    {
        return $this->log_id;
    }

    public function finishLog()
    {   
        $this->end_time = time();

        $log_data = $this->buildLogData();
        db_query("UPDATE $this->log_table SET ?u WHERE log_id = ?i", $log_data, $this->log_id);
    }

    private function getLogTime()
    {
        if ($this->start_time > $this->end_time) {
            return 0;
        }else {
            return $this->end_time - $this->start_time;
        }
    }

    private function createLog()
    {
        $start_data = $this->buildLogData();
        $this->log_id = db_query("INSERT INTO $this->log_table ?e", $start_data);
    }

    private function buildLogData()
    {
        $data = array(
            'start_time' => $this->start_time,
            'time' => $this->getLogTime(),
            'status' => $this->log_status,
            'data' => $this->log_message,
            'type' => $this->log_type,
            'parent_log_id' => $this->parent_log_id
        );

        return $data;
    }

    

}