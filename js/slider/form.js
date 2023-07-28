/**
 * Copyright since 2007 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    cdigruttola <c.digruttola@hotmail.it>
 *  @copyright Copyright since 2007 Carmine Di Gruttola
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 */

import TranslatableInput from '@PSJs/components/translatable-input';
import FormSubmitButton from '@PSJs/components/form-submit-button';
import ChoiceTree from '@PSJs/components/form/choice-tree';
import searchAutocomplete from './search-autocomplete';

const {$} = window;

$(() => {
    searchAutocomplete();
    window.prestashop.component.initComponents(
        [
            "TinyMCEEditor",
            'TranslatableField',
            'TranslatableInput',
        ],
    );

    BOEvent.emitEvent('Slider Product Management started', 'CustomEvent');
    new TranslatableInput();

    new ChoiceTree('#slider_category_id');
    new ChoiceTree('#slider_shop_association').enableAutoCheckChildren();

    type_change();

    $('#product_slider_type_products_show').change(function () {
        type_change();
    });


    new FormSubmitButton();
});

function type_change() {
    if ($('#product_slider_type_products_show').val() === "product") {
        $('#related-product').closest('.form-group.row').show();
    } else {
        $('#related-product').closest('.form-group.row').hide();
    }

    if ($('#product_slider_type_products_show').val() === "category") {
        $('#product_slider_category_id').closest('.category_choice_tree-widget').show();
    } else {
        $('#product_slider_category_id').closest('.category_choice_tree-widget').hide();
    }
}
