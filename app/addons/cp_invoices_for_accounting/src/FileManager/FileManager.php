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

namespace Tygh\Addons\CpInvoicesForAccounting\FileManager;

use Tygh;
use Tygh\Pdf;
use Tygh\Registry;
use Tygh\Addons\CpInvoicesForAccounting\ServiceProvider;
use Zoho;

require_once Registry::get('config.dir.addons'). 'nm_title/Zoho/Zoho.php';

class FileManager
{
    private $week_folder_name = "default_folder_for_accounting_from_";
    private $week_folder_path;
    private $zoho_uploads = [];
    private $accounting_invoices_folder = "Accounting_invoices";
    private $dropbox_folder_name = "Dropbox";
    private $dropbox_folder;

    function __construct()
    {
        $formatter = Tygh::$app['formatter'];

        $start_week = $formatter->asDatetime(time() - (SECONDS_IN_DAY * 7), "%d.%m.%y");
        $end_week = $formatter->asDatetime(time() - SECONDS_IN_DAY, "%d.%m.%y");
        
        if (!empty($start_week) && !empty($end_week)) {
            $this->week_folder_name = $start_week . "-" . $end_week;
        }else {
            $this->week_folder_name .= $formatter->asDatetime(time(),"%d.%m.%y");
        }

        if (!empty(ServiceProvider::folderName())) {
            $this->accounting_invoices_folder =  ServiceProvider::folderName();
        }
        

        $files_dir_result = fn_mkdir(Registry::get('config.dir.files') . $this->accounting_invoices_folder, 0777);

        if (!empty($files_dir_result)) {
            $this->week_folder_path = Registry::get('config.dir.files') . $this->accounting_invoices_folder . "/" . $this->week_folder_name;
            
            $week_dir_result = fn_mkdir($this->week_folder_path, 0777);

            /* create folder for dropbox */
            $this->dropbox_folder = Registry::get('config.dir.files') . $this->accounting_invoices_folder . "/" . $this->dropbox_folder_name;
            fn_mkdir($this->dropbox_folder, 0777);
            
            if (file_exists($this->dropbox_folder)) {
                foreach (glob($this->dropbox_folder . '/*') as $file) {
                    unlink($file);
                }
            }
            /* create folder for dropbox */
        }
        
            
        
    }

    public function saveFile($order_id)
    {
        if (!empty($this->week_folder_path)) {
            $params = array(
                'pdf'       => true,
                'area'      => AREA,
                'lang_code' => CART_LANGUAGE,
                'secondary_currency' => CART_SECONDARY_CURRENCY,
                'save'      => true,
            );

            $html = array();
            $data = array();

            $data['order_status_descr'] = fn_get_simple_statuses(STATUSES_ORDER, true, true, $params['lang_code']);
            $data['profile_fields'] = fn_get_profile_fields('I', array(), $params['lang_code']);

            /** @var \Tygh\Template\Document\Order\Type $document_type */
            $document_type = Tygh::$app['template.document.order.type'];
            $template_code = isset($params['template_code']) ? $params['template_code'] : 'invoice';
            $template = $document_type->renderById($order_id, $template_code, $params['lang_code'], $params['secondary_currency']);


            $html[] = $template;
            $repository = Tygh::$app['template.document.repository'];
            $filename = __('invoice') . '-' . $order_id;

            if (isset($params['template_code'])) {
                $document = $repository->findByTypeAndCode('order', $params['template_code']);
                $filename = $document->getName() . '-' . $order_id;
            }

            $saved_filename = $this->week_folder_path . '/' . $filename . '.pdf';
            
            $result = Pdf::render($html, $saved_filename, $params['save']);

            /*copy to dropbox folder*/
            fn_copy($saved_filename, $this->dropbox_folder);
            /*copy to dropbox folder*/

            return $params['save'] ? $saved_filename : $result;
        }
    }
}