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

namespace Tygh\Addons\CpEmailParser\MailParser;

use Tygh;
use Tygh\Registry;
use Tygh\Addons\CpEmailParser\ServiceProvider;
use Tygh\Enum\Addons\AdvancedImport\ImportStatuses;

class MailParser 
{
    private $filename;
    private $file_extensions = ['xls','xlsx'];
    public $company_id = 0;

    function __construct()
    {
        
    }

    public function setCompanyId($email)
    {       
        $this->company_id = db_get_field("SELECT company_id FROM ?:companies WHERE email_for_parser = ?s", $email);
        
        if (empty($this->company_id)) {
            $this->company_id = db_get_field("SELECT company_id FROM ?:companies WHERE email = ?s", $email);
        }
    }

    public function createCompanyImportFile($filename, $tmp_filepath)
    {   
        $this->filename = $filename;

        $true_path = fn_get_files_dir_path($this->company_id);
        $result = file_put_contents($true_path . $filename, file_get_contents($tmp_filepath));
        @chmod($true_path . $filename, 0666);
        @unlink($tmp_filepath);

        return $result;
    }

    public function updatePreset()
    {
        $extension = $this->getFileExtension();
        if (!in_array($extension, $this->file_extensions)) {
            return false;
        }

        db_query("UPDATE ?:import_presets SET file = ?s ,file_extension = ?s WHERE company_id = ?i", $this->filename, $extension, $this->company_id);

        return true;
    }

    public function startImport($logger)
    {   
        $presets_manager = Tygh::$app['addons.advanced_import.presets.manager'];
        $presets_importer = Tygh::$app['addons.advanced_import.presets.importer'];

        $preset_id = $this->getPresetId();

        list($presets,) = $presets_manager->find(false, array('ip.preset_id' => $preset_id), false);
        
        if ($presets) {
            
            $preset = reset($presets);

            /** @var \Tygh\Addons\AdvancedImport\Readers\Factory $reader_factory */
            $reader_factory = Tygh::$app['addons.advanced_import.readers.factory'];

            $is_success = false;

            try {

                $reader = $reader_factory->get($preset);
            
                $fields_mapping = $presets_manager->getFieldsMapping($preset['preset_id']);

                $pattern = $presets_manager->getPattern($preset['object_type']);
                $schema = $reader->getSchema();
                $schema->showNotifications();
                $schema = $schema->getData();

                $remapping_schema = $presets_importer->getEximSchema(
                    $schema,
                    $fields_mapping,
                    $pattern
                );

                if ($remapping_schema) {
                    $presets_importer->setPattern($pattern);
                    $result = $reader->getContents(null, $schema);
                    $result->showNotifications();

                    $import_items = $presets_importer->prepareImportItems(
                        $result->getData(),
                        $fields_mapping,
                        $preset['object_type'],
                        true,
                        $remapping_schema
                    );

                    $presets_manager->update($preset['preset_id'], array(
                        'last_launch' => TIME,
                        'last_status' => ImportStatuses::IN_PROGRESS,
                    ));

                    $preset['options']['preset'] = $preset;
                    unset($preset['options']['preset']['options']);

                    $is_success = fn_import($pattern, $import_items, $preset['options']);
                    if ($is_success) {

                        $count_products = count($import_items);
                        $logger->setMess(__("cp_email_parser.success_import", [ "[c_prod]" => $count_products, "[preset_id]" => $preset['preset_id']]));

                        $logger->finishLog();
                    }
                }
            } catch (Exception $e) {
                
            }

            $presets_manager->update($preset['preset_id'], array(
                'last_status' => $is_success
                    ? ImportStatuses::SUCCESS
                    : ImportStatuses::FAIL,
                'last_result' => Registry::get('runtime.advanced_import.result'),
            ));

            return $is_success;
        }
    }

    public function getPresetId()
    {
        return db_get_field("SELECT preset_id FROM ?:import_presets WHERE company_id = ?i", $this->company_id);
    }

    private function getFileExtension()
    {
        $path_info = fn_pathinfo($this->filename);
        $ext = strtolower($path_info['extension']);
           
        return $ext;
    }
}