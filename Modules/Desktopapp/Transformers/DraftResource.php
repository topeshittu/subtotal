<?php

namespace Modules\Desktopapp\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;;
use App\Utils\Util;
use App\Utils\ProductUtil;

class DraftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        $productUtil = new ProductUtil;
        $products = [];

        foreach ($array['sell_lines'] as $key => $value) {
            //check if mapping exists
            if (isset($value['sell_line_purchase_lines'])) {
                $purchase_lines = [];
                foreach ($value['sell_line_purchase_lines'] as $sell_line_purchase_line) {
                    //check mapped purchase line
                    if (isset($sell_line_purchase_line['purchase_line'])) {

                        //get purchase details of the sell line
                        $purchase_lines[] = [
                            'purchase_price' => $sell_line_purchase_line['purchase_line']['purchase_price'],
                            'pp_inc_tax' => $sell_line_purchase_line['purchase_line']['purchase_price_inc_tax'],
                            'lot_number' => $sell_line_purchase_line['purchase_line']['lot_number']
                        ];
                    }
                }
                //unset mapping and set purchase details
                unset($array['sell_lines'][$key]['sell_line_purchase_lines']);
                $array['sell_lines'][$key]['purchase_price'] = $purchase_lines;
            }

            $products[] = (object) [
                'name' => array_diff_key($value['product'], array_flip($this->__excludeProductFields()))['name'],
                'sku' => array_diff_key($value['product'], array_flip($this->__excludeProductFields()))['sku'],
                'product_id' => $value['product']['id'],
                'variation_id' => $value['variation_id'],
                'brand_id' => $value['product']['brand_id'],
                'quantity' => $value['quantity'],
                'max_quantity' => $productUtil->getQtyAvailableApi($this->business_id, $value['variation_id'], $value['product']['id'], $this->location_id),
                'unit_price' => $value['unit_price'],
                'sub_unit_id' => $value['sub_unit_id'],
                'sub_unit_name' => $productUtil->getSubUnitNameApi($value['sub_unit_id'], $this->business_id),
                'sub_units' => empty($productUtil->getSubUnitsApiForDrafts($this->business_id, $value['product']['unit_id'], true, $value['product']['id'])) ? [] : [$productUtil->getSubUnitsApiForDrafts($this->business_id, $value['product']['unit_id'], true, $value['product']['id'])],
            ];
            
            unset($array['sell_lines']);
        }

       

        $commonUtil = new Util;
        $array['products'] = $products;
        $array['contact'] = $array['contact']['name'];
        $array['location'] = $array['location']['name'];
        $array['user_name'] = \Auth::user()->username;
        

        return array_diff_key($array, array_flip($this->__excludeSellFields()));

        return $array;
    }


            
    private function __excludeSellFields(){
        return [
            'res_waiter_id',
            'res_order_status',
            'sub_type',
            'adjustment_type',
            'shipping_custom_field_1',
            'shipping_custom_field_2',
            'shipping_custom_field_3',
            'shipping_custom_field_4',
            'shipping_custom_field_5',
            'is_export',
            'export_custom_fields_info',
            'additional_expense_key_1',
            'additional_expense_value_1',
            "additional_expense_key_2",
            "additional_expense_value_2",
            "additional_expense_key_3",
            "additional_expense_value_3",
            "additional_expense_key_4",
            "additional_expense_value_4",
            "expense_category_id",
            "expense_for",
            "document",
            'exchange_rate',
            "transfer_parent_id",
            "return_parent_id",
            "opening_stock_product_id",
            "created_by",
            "mfg_parent_production_purchase_id",
            "mfg_wasted_units",
            "prefer_payment_method",
            "prefer_payment_account",
            "sales_order_ids",
            "purchase_order_ids",
            "custom_field_1",
            "custom_field_2",
            "custom_field_3",
            "custom_field_4",
            "import_batch",
            "import_time",
            "types_of_service_id",
            "packing_charge",
            "packing_charge_type",
            "service_custom_field_1",
            "service_custom_field_2",
            "service_custom_field_3",
            "service_custom_field_4",
            "service_custom_field_5",
            "service_custom_field_6",
            "rp_earned",
            "order_addresses",
            "is_recurring",
            "recur_interval",
            "recur_interval_type",
            "recur_repetitions",
            "recur_stopped_on",
            "recur_parent_id",
            'pay_term_number',
            'pay_term_type',
            "ref_no",
            "res_table_id",
            "type",
            "payment_status",
            "customer_group_id",
            "source",
            "subscription_no",
            "subscription_repeat_on",
            "total_before_tax",
            "tax_id",
            "tax_amount",
            "rp_redeemed",
            "rp_redeemed_amount",
            "shipping_address",
            "shipping_status",
            "delivered_to",
            "additional_notes",
            "round_off_amount",
            "final_total",
            "expense_sub_category_id",
            "is_direct_sale",
            "is_suspend",
            "total_amount_recovered",
            "crm_is_order_request",
            "woocommerce_order_id",
            "repair_completed_on",
            "repair_warranty_id",
            "repair_brand_id",
            "repair_status_id",
            "repair_model_id",
            "repair_job_sheet_id",
            "repair_defects",
            "repair_serial_no",
            "repair_checklist",
            "repair_security_pwd",
            "repair_security_pattern",
            "repair_due_date",
            "repair_device_id",
            "repair_updates_notif",
            "mfg_production_cost",
            "mfg_production_cost_type",
            "mfg_is_final",
            "essentials_duration",
            "essentials_duration_unit",
            "essentials_amount_per_unit_duration",
            "essentials_allowances",
            "essentials_deductions",
            "is_created_from_api",
            "invoice_token",
            "stock_card_brand_id",
            "stock_card_expedite",
            "stock_card_reorder",
            "stock_card_date_from",
            "stock_card_date_to",
            "created_at",
            "updated_at",
        ];
    }

    private function __excludeSellLineFields(){
        return [
            'variation_id',
            'mfg_waste_percent',
            'mfg_ingredient_group_id',
            "so_line_id",
            "so_quantity_invoiced",
            "res_service_staff_id",
            "res_line_order_status",
            "parent_sell_line_id",
            "sub_unit_id",
            'product',
            'variations'
        ];
    }

    private function __excludeProductFields(){
        return [
            "id",
            "business_id",
            "type",
            "unit_id",
            "sub_unit_ids",
            "brand_id",
            "category_id",
            "sub_category_id",
            "tax",
            "tax_type",
            "enable_stock",
            "alert_quantity",
            "barcode_type",
            "expiry_period",
            "expiry_period_type",
            "enable_sr_no",
            "weight",
            "product_custom_field1",
            "product_custom_field2",
            "product_custom_field3",
            "product_custom_field4",
            "image",
            "woocommerce_media_id",
            "product_description",
            "created_by",
            "woocommerce_product_id",
            "woocommerce_disable_sync",
            "warranty_id",
            "is_inactive",
            "repair_model_id",
            "not_for_selling",
            "created_at",
            "updated_at",
            "image_url"
        ];
    }

}
